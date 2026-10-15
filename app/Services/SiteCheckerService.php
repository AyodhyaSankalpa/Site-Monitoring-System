<?php

namespace App\Services;

use App\Models\Site;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Throwable;

class SiteCheckerService
{
    /**
     * Perform full monitoring check for a given site.
     */
    public function check(Site $site): array
    {
        $uptimeResult = $this->checkUptime($site->url);
        $sslResult = $this->checkSsl($site->url);
        $domainResult = $this->checkDomain($site->url, $site->domain_expires_at);

        // Update Site model
        $site->is_online = $uptimeResult['is_online'];
        $site->last_status_code = $uptimeResult['status_code'];
        $site->last_response_time_ms = $uptimeResult['response_time_ms'];
        $site->last_error = $uptimeResult['error'];
        $site->last_checked_at = now();

        if (! empty($uptimeResult['server_ip']) && empty($site->host_ip)) {
            $site->host_ip = $uptimeResult['server_ip'];
        }

        if ($sslResult['checked']) {
            $site->ssl_status = $sslResult['status'];
            $site->ssl_issuer = $sslResult['issuer'] ?? $site->ssl_issuer;
            if (! empty($sslResult['expires_at'])) {
                $site->ssl_expires_at = $sslResult['expires_at'];
            }
        }

        if ($domainResult['found'] && ! empty($domainResult['expires_at'])) {
            $site->domain_expires_at = $domainResult['expires_at'];
            if (! empty($domainResult['registrar'])) {
                $site->domain_registrar = $domainResult['registrar'];
            }
        }

        $site->save();

        // Record history log
        $site->checks()->create([
            'status_code' => $uptimeResult['status_code'],
            'response_time_ms' => $uptimeResult['response_time_ms'],
            'is_online' => $uptimeResult['is_online'],
            'error_message' => $uptimeResult['error'],
            'checked_at' => now(),
        ]);

        return [
            'uptime' => $uptimeResult,
            'ssl' => $sslResult,
            'domain' => $domainResult,
        ];
    }

    /**
     * Check HTTP status, response latency, and bypass all caches.
     */
    public function checkUptime(string $url): array
    {
        $startTime = microtime(true);
        $ch = curl_init();

        // Ensure URL has protocol
        if (! preg_match('#^https?://#i', $url)) {
            $url = 'https://' . $url;
        }

        // Add a random timestamp param to query string to break upstream edge proxy caches
        $cacheBusterUrl = $url . (str_contains($url, '?') ? '&' : '?') . '_nocache=' . microtime(true);

        curl_setopt_array($ch, [
            CURLOPT_URL => $cacheBusterUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_NOBODY => false,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_CONNECTTIMEOUT => 7,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 5,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_USERAGENT => 'SitePulseMonitor/1.0 (+https://agency.example.com)',
            CURLOPT_HTTPHEADER => [
                'Cache-Control: no-cache, no-store, must-revalidate',
                'Pragma: no-cache',
                'Expires: 0',
                'Accept: */*',
            ],
        ]);

        // Only fetch the first 1KB of content to save bandwidth and compute
        curl_setopt($ch, CURLOPT_RANGE, '0-1024');

        $response = curl_exec($ch);
        $totalTime = curl_getinfo($ch, CURLINFO_TOTAL_TIME);
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $primaryIp = curl_getinfo($ch, CURLINFO_PRIMARY_IP);
        $curlError = curl_error($ch);
        curl_close($ch);

        $responseTimeMs = (int) round(($totalTime > 0 ? $totalTime : (microtime(true) - $startTime)) * 1000);

        // A site is considered UP if status is 2xx, 3xx, or auth required (401/403)
        $isOnline = ($statusCode >= 200 && $statusCode < 400) || in_array($statusCode, [401, 403]);

        // If cURL failed with DNS or connection failure
        if ($statusCode === 0 || ! empty($curlError)) {
            $isOnline = false;
        }

        return [
            'is_online' => $isOnline,
            'status_code' => $statusCode ?: null,
            'response_time_ms' => $responseTimeMs,
            'server_ip' => $primaryIp ?: null,
            'error' => ! empty($curlError) ? $curlError : ($statusCode >= 500 ? "Server Error ($statusCode)" : null),
        ];
    }

    /**
     * Inspect SSL Certificate validity, issuer, and expiration date.
     */
    public function checkSsl(string $url): array
    {
        $host = parse_url($url, PHP_URL_HOST);
        if (! $host) {
            $host = preg_replace('#^https?://#i', '', $url);
            $host = explode('/', $host)[0];
        }

        $streamContext = stream_context_create([
            'ssl' => [
                'capture_peer_cert' => true,
                'verify_peer' => false,
                'verify_peer_name' => false,
                'timeout' => 8,
            ],
        ]);

        $client = @stream_socket_client(
            "ssl://{$host}:443",
            $errno,
            $errstr,
            8,
            STREAM_CLIENT_CONNECT,
            $streamContext
        );

        if (! $client) {
            return [
                'checked' => true,
                'status' => 'invalid',
                'error' => "Could not connect to port 443: $errstr",
                'issuer' => null,
                'expires_at' => null,
            ];
        }

        $params = stream_context_get_params($client);
        fclose($client);

        if (empty($params['options']['ssl']['peer_certificate'])) {
            return [
                'checked' => true,
                'status' => 'invalid',
                'error' => 'No certificate found on server',
                'issuer' => null,
                'expires_at' => null,
            ];
        }

        $cert = openssl_x509_parse($params['options']['ssl']['peer_certificate']);
        if (! $cert || empty($cert['validTo_time_t'])) {
            return [
                'checked' => true,
                'status' => 'invalid',
                'error' => 'Failed to parse SSL certificate',
                'issuer' => null,
                'expires_at' => null,
            ];
        }

        $validTo = Carbon::createFromTimestamp($cert['validTo_time_t']);
        $daysRemaining = (int) now()->diffInDays($validTo, false);

        $issuerName = $cert['issuer']['O'] ?? ($cert['issuer']['CN'] ?? 'Unknown Issuer');

        $status = 'valid';
        if ($daysRemaining < 0) {
            $status = 'expired';
        } elseif ($daysRemaining <= 14) {
            $status = 'expiring_soon';
        }

        return [
            'checked' => true,
            'status' => $status,
            'issuer' => $issuerName,
            'expires_at' => $validTo,
            'days_remaining' => $daysRemaining,
        ];
    }

    /**
     * Try to fetch domain expiration via modern RDAP.
     */
    public function checkDomain(string $url, $existingExpiresAt = null): array
    {
        $host = parse_url($url, PHP_URL_HOST) ?: $url;
        // Strip www. or subdomains for standard domains
        $parts = explode('.', $host);
        if (count($parts) >= 2) {
            $domain = implode('.', array_slice($parts, -2));
        } else {
            $domain = $host;
        }

        try {
            $response = Http::timeout(4)->get("https://rdap.org/domain/{$domain}");
            if ($response->successful()) {
                $data = $response->json();
                $expirationDate = null;
                $registrar = null;

                if (! empty($data['events'])) {
                    foreach ($data['events'] as $event) {
                        if (($event['eventAction'] ?? '') === 'expiration') {
                            $expirationDate = Carbon::parse($event['eventDate'])->toDateString();
                            break;
                        }
                    }
                }

                if (! empty($data['entities'])) {
                    foreach ($data['entities'] as $entity) {
                        if (in_array('registrar', $entity['roles'] ?? [])) {
                            $registrar = $entity['vcardArray'][1][1][3] ?? ($entity['handle'] ?? null);
                            break;
                        }
                    }
                }

                if ($expirationDate) {
                    return [
                        'found' => true,
                        'expires_at' => $expirationDate,
                        'registrar' => $registrar,
                    ];
                }
            }
        } catch (Throwable) {
            // Silently fall back to manual date
        }

        return [
            'found' => false,
            'expires_at' => $existingExpiresAt,
            'registrar' => null,
        ];
    }
}
