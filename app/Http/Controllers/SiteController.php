<?php

namespace App\Http\Controllers;

use App\Models\Site;
use App\Services\SiteCheckerService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SiteController extends Controller
{
    public function __construct(
        protected SiteCheckerService $checker
    ) {}

    /**
     * Display a listing of sites and system stats.
     */
    public function index(Request $request): View
    {
        $query = Site::query();

        // Search filter
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('url', 'like', "%{$search}%")
                  ->orWhere('client_name', 'like', "%{$search}%");
            });
        }

        // Status filter
        $filter = $request->input('filter', 'all');
        if ($filter === 'online') {
            $query->where('is_online', true);
        } elseif ($filter === 'down') {
            $query->where('is_online', false);
        } elseif ($filter === 'ssl_alert') {
            $query->whereIn('ssl_status', ['expiring_soon', 'expired', 'invalid']);
        } elseif ($filter === 'domain_alert') {
            $query->whereNotNull('domain_expires_at')
                  ->where('domain_expires_at', '<=', now()->addDays(30));
        } elseif ($filter === 'host_alert') {
            $query->whereNotNull('host_expires_at')
                  ->where('host_expires_at', '<=', now()->addDays(30));
        }

        $sites = $query->orderBy('is_online', 'asc') // down sites appear on top
                       ->orderBy('name', 'asc')
                       ->paginate(15)
                       ->withQueryString();

        // Summary Statistics
        $stats = [
            'total' => Site::count(),
            'online' => Site::where('is_online', true)->count(),
            'down' => Site::where('is_online', false)->count(),
            'ssl_alerts' => Site::whereIn('ssl_status', ['expiring_soon', 'expired', 'invalid'])->count(),
            'domain_alerts' => Site::whereNotNull('domain_expires_at')
                                    ->where('domain_expires_at', '<=', now()->addDays(30))->count(),
            'host_alerts' => Site::whereNotNull('host_expires_at')
                                  ->where('host_expires_at', '<=', now()->addDays(30))->count(),
        ];

        return view('sites.index', compact('sites', 'stats', 'filter', 'search'));
    }

    /**
     * Show form to create a new client site.
     */
    public function create(): View
    {
        return view('sites.create');
    }

    /**
     * Store a newly created site and trigger immediate initial check.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'required|string|max:255',
            'client_name' => 'nullable|string|max:255',
            'client_email' => 'nullable|email|max:255',
            'client_phone' => 'nullable|string|max:50',
            'host_provider' => 'nullable|string|max:255',
            'host_ip' => 'nullable|string|max:100',
            'host_expires_at' => 'nullable|date',
            'domain_registrar' => 'nullable|string|max:255',
            'domain_expires_at' => 'nullable|date',
            'check_interval_minutes' => 'nullable|integer|min:1|max:1440',
        ]);

        // Standardize URL
        $url = trim($validated['url']);
        if (! preg_match('#^https?://#i', $url)) {
            $url = 'https://' . $url;
        }
        $validated['url'] = $url;
        $validated['check_interval_minutes'] = $validated['check_interval_minutes'] ?? 15;

        $site = Site::create($validated);

        // Run immediate initial check
        try {
            $this->checker->check($site);
        } catch (\Throwable $e) {
            // Check error won't break creation
        }

        return redirect()->route('sites.index')
                         ->with('success', "Website '{$site->name}' added and initial health check completed successfully!");
    }

    /**
     * Display site overview, latency stats, and check history.
     */
    public function show(Site $site): View
    {
        $recentChecks = $site->checks()->take(25)->get();

        return view('sites.show', compact('site', 'recentChecks'));
    }

    /**
     * Show edit form.
     */
    public function edit(Site $site): View
    {
        return view('sites.edit', compact('site'));
    }

    /**
     * Update site details.
     */
    public function update(Request $request, Site $site): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'required|string|max:255',
            'client_name' => 'nullable|string|max:255',
            'client_email' => 'nullable|email|max:255',
            'client_phone' => 'nullable|string|max:50',
            'host_provider' => 'nullable|string|max:255',
            'host_ip' => 'nullable|string|max:100',
            'host_expires_at' => 'nullable|date',
            'domain_registrar' => 'nullable|string|max:255',
            'domain_expires_at' => 'nullable|date',
            'check_interval_minutes' => 'nullable|integer|min:1|max:1440',
            'is_active' => 'nullable|boolean',
        ]);

        $url = trim($validated['url']);
        if (! preg_match('#^https?://#i', $url)) {
            $url = 'https://' . $url;
        }
        $validated['url'] = $url;
        $validated['is_active'] = $request->has('is_active');

        $site->update($validated);

        return redirect()->route('sites.show', $site)
                         ->with('success', 'Site settings updated successfully.');
    }

    /**
     * Delete a site.
     */
    public function destroy(Site $site): RedirectResponse
    {
        $name = $site->name;
        $site->delete();

        return redirect()->route('sites.index')
                         ->with('success', "Site '{$name}' has been deleted.");
    }

    /**
     * Manually trigger on-demand check for a single site.
     */
    public function check(Request $request, Site $site): JsonResponse|RedirectResponse
    {
        $result = $this->checker->check($site);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'is_online' => $site->is_online,
                'status_code' => $site->last_status_code,
                'response_time_ms' => $site->last_response_time_ms,
                'last_checked' => $site->last_checked_at?->diffForHumans(),
                'ssl_status' => $site->ssl_status,
                'days_ssl' => $site->daysUntilSslExpires(),
                'result' => $result,
            ]);
        }

        $statusText = $site->is_online ? 'ONLINE (Status: ' . $site->last_status_code . ')' : 'DOWN!';
        return back()->with('info', "Checked '{$site->name}': {$statusText} in {$site->last_response_time_ms}ms.");
    }

    /**
     * Trigger bulk check on all active sites.
     */
    public function checkAll(): RedirectResponse
    {
        $sites = Site::where('is_active', true)->get();
        $checkedCount = 0;

        foreach ($sites as $site) {
            try {
                $this->checker->check($site);
                $checkedCount++;
            } catch (\Throwable) {
                // Continue checking remaining sites
            }
        }

        return redirect()->route('sites.index')
                         ->with('success', "Live check finished for {$checkedCount} active websites!");
    }
}
