@extends('layouts.app')

@section('title', 'Agency Dashboard')

@section('content')

    <!-- Top Headline & Action Bar -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h1 class="h3 fw-bold mb-1 text-dark">Client Websites Health Monitor</h1>
            <p class="text-muted mb-0 small">Real-time uptime, SSL certificate validation & domain expiration tracking for web agency clients.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('sites.create') }}" class="btn btn-primary shadow-sm">
                <i class="bi bi-plus-circle me-1"></i> Add Client Site
            </a>
        </div>
    </div>

    <!-- Quick Stats Cards Row -->
    <div class="row g-3 mb-4">
        <!-- Total Sites -->
        <div class="col-6 col-lg-2">
            <a href="{{ route('sites.index') }}" class="text-decoration-none">
                <div class="card card-stat h-100 p-3 bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted small fw-medium">Total Sites</span>
                            <h3 class="fw-bold mt-1 mb-0 text-dark">{{ $stats['total'] }}</h3>
                        </div>
                        <div class="stat-icon bg-light text-primary">
                            <i class="bi bi-globe"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Online Sites -->
        <div class="col-6 col-lg-2">
            <a href="{{ route('sites.index', ['filter' => 'online']) }}" class="text-decoration-none">
                <div class="card card-stat h-100 p-3 bg-white border-start border-success border-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted small fw-medium">Sites Online</span>
                            <h3 class="fw-bold mt-1 mb-0 text-success">{{ $stats['online'] }}</h3>
                        </div>
                        <div class="stat-icon bg-success-subtle text-success">
                            <i class="bi bi-check2-circle"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Down Sites -->
        <div class="col-6 col-lg-2">
            <a href="{{ route('sites.index', ['filter' => 'down']) }}" class="text-decoration-none">
                <div class="card card-stat h-100 p-3 bg-white {{ $stats['down'] > 0 ? 'border-start border-danger border-4 bg-danger-subtle' : '' }}">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted small fw-medium">Sites Down</span>
                            <h3 class="fw-bold mt-1 mb-0 {{ $stats['down'] > 0 ? 'text-danger' : 'text-dark' }}">{{ $stats['down'] }}</h3>
                        </div>
                        <div class="stat-icon {{ $stats['down'] > 0 ? 'bg-danger text-white' : 'bg-light text-danger' }}">
                            <i class="bi bi-exclamation-triangle"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <!-- SSL Alerts -->
        <div class="col-6 col-lg-2">
            <a href="{{ route('sites.index', ['filter' => 'ssl_alert']) }}" class="text-decoration-none">
                <div class="card card-stat h-100 p-3 bg-white {{ $stats['ssl_alerts'] > 0 ? 'border-start border-warning border-4' : '' }}">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted small fw-medium">SSL Alerts</span>
                            <h3 class="fw-bold mt-1 mb-0 {{ $stats['ssl_alerts'] > 0 ? 'text-warning' : 'text-dark' }}">{{ $stats['ssl_alerts'] }}</h3>
                        </div>
                        <div class="stat-icon bg-warning-subtle text-warning">
                            <i class="bi bi-shield-exclamation"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Domain Renewals -->
        <div class="col-6 col-lg-2">
            <a href="{{ route('sites.index', ['filter' => 'domain_alert']) }}" class="text-decoration-none">
                <div class="card card-stat h-100 p-3 bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted small fw-medium">Domain Due</span>
                            <h3 class="fw-bold mt-1 mb-0 text-info">{{ $stats['domain_alerts'] }}</h3>
                        </div>
                        <div class="stat-icon bg-info-subtle text-info">
                            <i class="bi bi-calendar-event"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Host Renewals -->
        <div class="col-6 col-lg-2">
            <a href="{{ route('sites.index', ['filter' => 'host_alert']) }}" class="text-decoration-none">
                <div class="card card-stat h-100 p-3 bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted small fw-medium">Host Due</span>
                            <h3 class="fw-bold mt-1 mb-0 text-secondary">{{ $stats['host_alerts'] }}</h3>
                        </div>
                        <div class="stat-icon bg-secondary-subtle text-secondary">
                            <i class="bi bi-hdd-network"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Filters & Search Toolbar -->
    <div class="card bg-white mb-4">
        <div class="card-body p-3">
            <div class="row g-2 align-items-center">
                <!-- Filter Pills -->
                <div class="col-12 col-md-8 d-flex flex-wrap gap-2">
                    <a href="{{ route('sites.index', ['search' => $search]) }}" class="btn btn-sm {{ $filter === 'all' ? 'btn-dark' : 'btn-light border' }}">
                        All ({{ $stats['total'] }})
                    </a>
                    <a href="{{ route('sites.index', ['filter' => 'online', 'search' => $search]) }}" class="btn btn-sm {{ $filter === 'online' ? 'btn-success' : 'btn-light border' }}">
                        <span class="pulse-dot pulse-online"></span> Online ({{ $stats['online'] }})
                    </a>
                    <a href="{{ route('sites.index', ['filter' => 'down', 'search' => $search]) }}" class="btn btn-sm {{ $filter === 'down' ? 'btn-danger' : 'btn-light border' }}">
                        <span class="pulse-dot pulse-down"></span> Down ({{ $stats['down'] }})
                    </a>
                    <a href="{{ route('sites.index', ['filter' => 'ssl_alert', 'search' => $search]) }}" class="btn btn-sm {{ $filter === 'ssl_alert' ? 'btn-warning text-dark' : 'btn-light border' }}">
                        <i class="bi bi-shield-lock me-1"></i> SSL Alerts ({{ $stats['ssl_alerts'] }})
                    </a>
                    <a href="{{ route('sites.index', ['filter' => 'domain_alert', 'search' => $search]) }}" class="btn btn-sm {{ $filter === 'domain_alert' ? 'btn-info text-white' : 'btn-light border' }}">
                        <i class="bi bi-calendar2-range me-1"></i> Domain Due ({{ $stats['domain_alerts'] }})
                    </a>
                    <a href="{{ route('sites.index', ['filter' => 'host_alert', 'search' => $search]) }}" class="btn btn-sm {{ $filter === 'host_alert' ? 'btn-secondary' : 'btn-light border' }}">
                        <i class="bi bi-server me-1"></i> Host Due ({{ $stats['host_alerts'] }})
                    </a>
                </div>

                <!-- Search Input Form -->
                <div class="col-12 col-md-4">
                    <form action="{{ route('sites.index') }}" method="GET" class="d-flex gap-2">
                        @if($filter !== 'all')
                            <input type="hidden" name="filter" value="{{ $filter }}">
                        @endif
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                            <input type="text" name="search" class="form-control border-start-0" placeholder="Search site or client..." value="{{ $search }}">
                            @if($search)
                                <a href="{{ route('sites.index', ['filter' => $filter]) }}" class="btn btn-outline-secondary" title="Clear search"><i class="bi bi-x-lg"></i></a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Sites Table Card -->
    <div class="card bg-white border-0 shadow-sm overflow-hidden">
        @if($sites->count() > 0)
            <div class="table-responsive">
                <table class="table table-custom table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th scope="col" style="min-width: 140px;">Status</th>
                            <th scope="col" style="min-width: 200px;">Site & URL</th>
                            <th scope="col" style="min-width: 140px;">Client</th>
                            <th scope="col" style="min-width: 160px;">SSL Certificate</th>
                            <th scope="col" style="min-width: 150px;">Domain Expiry</th>
                            <th scope="col" style="min-width: 150px;">Hosting Renewal</th>
                            <th scope="col" style="min-width: 110px;">Last Checked</th>
                            <th scope="col" class="text-end" style="min-width: 140px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sites as $site)
                            @php
                                $sslDays = $site->daysUntilSslExpires();
                                $domainDays = $site->daysUntilDomainExpires();
                                $hostDays = $site->daysUntilHostExpires();
                            @endphp
                            <tr id="site-row-{{ $site->id }}" class="{{ ! $site->is_online ? 'table-danger-subtle' : '' }}">
                                <!-- Status Column -->
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($site->is_online)
                                            <span class="pulse-dot pulse-online me-2"></span>
                                            <div>
                                                <span class="badge bg-success-subtle text-success fw-bold px-2 py-1">
                                                    {{ $site->last_status_code ?? 200 }} OK
                                                </span>
                                                @if($site->last_response_time_ms)
                                                    <div class="text-muted mt-1 small" style="font-size: 0.75rem;">
                                                        <i class="bi bi-lightning-charge-fill text-warning"></i> {{ $site->last_response_time_ms }} ms
                                                    </div>
                                                @endif
                                            </div>
                                        @else
                                            <span class="pulse-dot pulse-down me-2"></span>
                                            <div>
                                                <span class="badge bg-danger text-white fw-bold px-2 py-1">
                                                    {{ $site->last_status_code ? 'ERROR ' . $site->last_status_code : 'DOWN' }}
                                                </span>
                                                @if($site->last_error)
                                                    <div class="text-danger small text-truncate mt-1" style="max-width: 110px;" title="{{ $site->last_error }}">
                                                        {{ $site->last_error }}
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </td>

                                <!-- Site Name & URL -->
                                <td>
                                    <div class="fw-semibold text-dark">{{ $site->name }}</div>
                                    <a href="{{ $site->url }}" target="_blank" rel="noopener noreferrer" class="text-muted small text-decoration-none hover-link d-inline-flex align-items-center mt-1">
                                        <span class="text-truncate" style="max-width: 220px;">{{ $site->url }}</span>
                                        <i class="bi bi-box-arrow-up-right ms-1" style="font-size: 0.75rem;"></i>
                                    </a>
                                </td>

                                <!-- Client Name -->
                                <td>
                                    <div class="text-dark small fw-medium">{{ $site->client_name ?: '—' }}</div>
                                    @if($site->client_phone)
                                        <div class="text-muted small" style="font-size: 0.75rem;">
                                            <i class="bi bi-telephone me-1"></i>{{ $site->client_phone }}
                                        </div>
                                    @endif
                                </td>

                                <!-- SSL Certificate -->
                                <td>
                                    @if($site->ssl_status === 'valid')
                                        <span class="badge bg-success-subtle text-success border border-success-subtle badge-pill">
                                            <i class="bi bi-shield-check me-1"></i> Valid
                                        </span>
                                        <div class="small text-muted mt-1" style="font-size: 0.75rem;">
                                            @if($sslDays !== null)
                                                Expires in <span class="fw-semibold {{ $sslDays <= 30 ? 'text-warning' : 'text-dark' }}">{{ $sslDays }} days</span>
                                            @endif
                                        </div>
                                    @elseif($site->ssl_status === 'expiring_soon')
                                        <span class="badge bg-warning text-dark badge-pill">
                                            <i class="bi bi-shield-exclamation me-1"></i> Expiring Soon
                                        </span>
                                        <div class="small text-warning fw-semibold mt-1" style="font-size: 0.75rem;">
                                            {{ $sslDays }} days remaining
                                        </div>
                                    @elseif($site->ssl_status === 'expired')
                                        <span class="badge bg-danger text-white badge-pill">
                                            <i class="bi bi-shield-x me-1"></i> Expired!
                                        </span>
                                    @elseif($site->ssl_status === 'invalid')
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle badge-pill">
                                            <i class="bi bi-shield-x me-1"></i> Invalid SSL
                                        </span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary badge-pill">Unknown</span>
                                    @endif
                                </td>

                                <!-- Domain Expiry -->
                                <td>
                                    @if($site->domain_expires_at)
                                        <div class="small fw-semibold text-dark">{{ $site->domain_expires_at->format('M d, Y') }}</div>
                                        <div class="mt-1">
                                            @if($domainDays !== null)
                                                @if($domainDays < 0)
                                                    <span class="badge bg-danger text-white px-2 py-0" style="font-size: 0.7rem;">Expired</span>
                                                @elseif($domainDays <= 14)
                                                    <span class="badge bg-danger text-white px-2 py-0" style="font-size: 0.7rem;">{{ $domainDays }}d left!</span>
                                                @elseif($domainDays <= 30)
                                                    <span class="badge bg-warning text-dark px-2 py-0" style="font-size: 0.7rem;">{{ $domainDays }}d left</span>
                                                @else
                                                    <span class="badge bg-light text-secondary border px-2 py-0" style="font-size: 0.7rem;">{{ $domainDays }}d left</span>
                                                @endif
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-muted small">Not set</span>
                                    @endif
                                </td>

                                <!-- Hosting Renewal -->
                                <td>
                                    <div class="small fw-medium text-dark">{{ $site->host_provider ?: 'Hosting' }}</div>
                                    @if($site->host_expires_at)
                                        <div class="small text-muted" style="font-size: 0.75rem;">{{ $site->host_expires_at->format('M d, Y') }}</div>
                                        <div class="mt-1">
                                            @if($hostDays !== null)
                                                @if($hostDays < 0)
                                                    <span class="badge bg-danger text-white px-2 py-0" style="font-size: 0.7rem;">Expired</span>
                                                @elseif($hostDays <= 30)
                                                    <span class="badge bg-warning text-dark px-2 py-0" style="font-size: 0.7rem;">{{ $hostDays }}d left</span>
                                                @else
                                                    <span class="badge bg-light text-secondary border px-2 py-0" style="font-size: 0.7rem;">{{ $hostDays }}d left</span>
                                                @endif
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-muted small" style="font-size: 0.75rem;">Renewal not set</span>
                                    @endif
                                </td>

                                <!-- Last Checked -->
                                <td>
                                    <span class="text-muted small" id="last-checked-{{ $site->id }}">
                                        {{ $site->last_checked_at ? $site->last_checked_at->diffForHumans(null, true) . ' ago' : 'Never' }}
                                    </span>
                                </td>

                                <!-- Actions -->
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm">
                                        <!-- Instant Real-Time Check Button -->
                                        <button type="button" 
                                                class="btn btn-outline-primary btn-check-now" 
                                                data-site-id="{{ $site->id }}"
                                                title="Check Uptime & SSL Now"
                                                onclick="checkSiteLive({{ $site->id }})">
                                            <i class="bi bi-arrow-repeat" id="check-icon-{{ $site->id }}"></i>
                                        </button>

                                        <!-- Details -->
                                        <a href="{{ route('sites.show', $site) }}" class="btn btn-outline-secondary" title="View Diagnostics & History">
                                            <i class="bi bi-graph-up"></i>
                                        </a>

                                        <!-- Edit -->
                                        <a href="{{ route('sites.edit', $site) }}" class="btn btn-outline-secondary" title="Edit Site Details">
                                            <i class="bi bi-pencil"></i>
                                        </a>

                                        <!-- Delete -->
                                        <button type="button" class="btn btn-outline-danger" title="Delete Site" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $site->id }}">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>

                                    <!-- Delete Modal -->
                                    <div class="modal fade text-start" id="deleteModal{{ $site->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered modal-sm">
                                            <div class="modal-content">
                                                <div class="modal-body text-center p-4">
                                                    <i class="bi bi-exclamation-circle text-danger display-5 mb-3 d-inline-block"></i>
                                                    <h5 class="fw-bold">Delete Website?</h5>
                                                    <p class="text-muted small mb-3">Are you sure you want to remove <strong>{{ $site->name }}</strong>? All monitoring history will be deleted.</p>
                                                    <div class="d-flex justify-content-center gap-2">
                                                        <button type="button" class="btn btn-light btn-sm px-3" data-bs-dismiss="modal">Cancel</button>
                                                        <form action="{{ route('sites.destroy', $site) }}" method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger btn-sm px-3">Delete</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($sites->hasPages())
                <div class="p-3 border-top d-flex justify-content-center">
                    {{ $sites->links('pagination::bootstrap-5') }}
                </div>
            @endif

        @else
            <!-- Empty State -->
            <div class="text-center py-5 px-3">
                <div class="stat-icon bg-light text-muted mx-auto mb-3" style="width: 64px; height: 64px; font-size: 2rem;">
                    <i class="bi bi-hdd-stack"></i>
                </div>
                <h4 class="fw-bold">No Websites Found</h4>
                <p class="text-muted small mb-4" style="max-width: 450px; margin: 0 auto;">
                    @if($search || $filter !== 'all')
                        No client sites matched your filter or search query. Try resetting your search.
                    @else
                        You haven't added any client sites yet. Start tracking uptime, SSL certificate validity, and renewals now!
                    @endif
                </p>
                <div class="d-flex justify-content-center gap-2">
                    @if($search || $filter !== 'all')
                        <a href="{{ route('sites.index') }}" class="btn btn-outline-secondary btn-sm">Reset Filters</a>
                    @endif
                    <a href="{{ route('sites.create') }}" class="btn btn-primary btn-sm px-3 shadow-sm">
                        <i class="bi bi-plus-circle me-1"></i> Add Your First Site
                    </a>
                </div>
            </div>
        @endif
    </div>

@endsection

@section('scripts')
<script>
    /**
     * Perform real-time live site check via AJAX without reloading page
     */
    function checkSiteLive(siteId) {
        const icon = document.getElementById('check-icon-' + siteId);
        if (!icon) return;

        icon.classList.add('spinning');
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        fetch(`/sites/${siteId}/check`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.json())
        .then(data => {
            icon.classList.remove('spinning');
            if (data.success) {
                // Flash updated row or refresh page to update full badges
                window.location.reload();
            }
        })
        .catch(err => {
            icon.classList.remove('spinning');
            console.error('Error during live check:', err);
            window.location.reload();
        });
    }
</script>
@endsection
