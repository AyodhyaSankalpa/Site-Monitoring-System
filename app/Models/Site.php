<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Site extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'url',
        'client_name',
        'client_email',
        'client_phone',
        'is_online',
        'last_status_code',
        'last_response_time_ms',
        'last_error',
        'last_checked_at',
        'check_interval_minutes',
        'is_active',
        'ssl_status',
        'ssl_issuer',
        'ssl_expires_at',
        'domain_registrar',
        'domain_expires_at',
        'host_provider',
        'host_ip',
        'host_expires_at',
    ];

    protected function casts(): array
    {
        return [
            'is_online' => 'boolean',
            'is_active' => 'boolean',
            'last_checked_at' => 'datetime',
            'ssl_expires_at' => 'datetime',
            'domain_expires_at' => 'date',
            'host_expires_at' => 'date',
        ];
    }

    public function checks(): HasMany
    {
        return $this->hasMany(SiteCheck::class)->latest('checked_at');
    }

    /**
     * Get remaining days until SSL expires.
     */
    public function daysUntilSslExpires(): ?int
    {
        if (! $this->ssl_expires_at) {
            return null;
        }

        return (int) now()->diffInDays($this->ssl_expires_at, false);
    }

    /**
     * Get remaining days until Domain expires.
     */
    public function daysUntilDomainExpires(): ?int
    {
        if (! $this->domain_expires_at) {
            return null;
        }

        return (int) now()->startOfDay()->diffInDays(Carbon::parse($this->domain_expires_at)->startOfDay(), false);
    }

    /**
     * Get remaining days until Hosting expires.
     */
    public function daysUntilHostExpires(): ?int
    {
        if (! $this->host_expires_at) {
            return null;
        }

        return (int) now()->startOfDay()->diffInDays(Carbon::parse($this->host_expires_at)->startOfDay(), false);
    }
}
