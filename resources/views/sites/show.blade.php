@extends('layouts.app')

@section('title', $site->name . ' - Site Details')

@section('content')

    <!-- Breadcrumbs -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="{{ route('sites.index') }}" class="text-decoration-none">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $site->name }}</li>
        </ol>
    </nav>

    <!-- Top Banner: Site Status Summary -->
    <div class="card bg-white shadow-sm border-0 mb-4 overflow-hidden">
        <div class="p-4 {{ $site->is_online ? 'border-start border-success border-5' : 'border-start border-danger border-5 bg-danger-subtle' }}">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div class="d-flex align-items-center">
                    <span class="pulse-dot {{ $site->is_online ? 'pulse-online' : 'pulse-down' }} me-3" style="width: 14px; height: 14px;"></span>
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <h2 class="h4 fw-bold mb-0 text-dark">{{ $site->name }}</h2>
                            @if($site->is_online)
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">ONLINE</span>
                            @else
                                <span class="badge bg-danger text-white px-2 py-1">DOWN / UNREACHABLE</span>
                            @endif
                            @if(! $site->is_active)
                                <span class="badge bg-secondary-subtle text-secondary px-2 py-1">PAUSED</span>
                            @endif
                        </div>
                        <a href="{{ $site->url }}" target="_blank" rel="noopener noreferrer" class="text-primary small text-decoration-none d-inline-flex align-items-center">
                            {{ $site->url }} <i class="bi bi-box-arrow-up-right ms-1"></i>
                        </a>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <form action="{{ route('sites.check', $site) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-primary px-3 shadow-sm" onclick="this.innerHTML='<span class=\'spinner-border spinner-border-sm me-1\'></span> Pinging...'; this.form.submit();">
                            <i class="bi bi-arrow-repeat me-1"></i> Check Now
                        </button>
                    </form>
                    <a href="{{ route('sites.edit', $site) }}" class="btn btn-outline-secondary px-3">
                        <i class="bi bi-pencil me-1"></i> Edit
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- 3 Diagnostic Metric Cards -->
    <div class="row g-3 mb-4">
        <!-- 1. HTTP Uptime & Health -->
        <div class="col-12 col-md-4">
            <div class="card bg-white h-100 shadow-sm border-0">
                <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                    <span class="fw-bold text-dark small text-uppercase">
                        <i class="bi bi-activity text-primary me-2"></i> HTTP Health
                    </span>
                    <span class="badge bg-light text-secondary border">Live Ping</span>
                </div>
                <div class="card-body p-3">
                    <ul class="list-unstyled mb-0 small">
                        <li class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted">Last HTTP Status:</span>
                            <span class="fw-semibold {{ $site->is_online ? 'text-success' : 'text-danger' }}">
                                {{ $site->last_status_code ?? 'None' }} {{ $site->is_online ? '(OK)' : '(Error)' }}
                            </span>
                        </li>
                        <li class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted">Response Latency:</span>
                            <span class="fw-semibold text-dark">
                                {{ $site->last_response_time_ms ? $site->last_response_time_ms . ' ms' : '—' }}
                            </span>
                        </li>
                        <li class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted">Server IP:</span>
                            <span class="fw-semibold text-dark font-monospace">{{ $site->host_ip ?? 'Auto' }}</span>
                        </li>
                        <li class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted">Check Frequency:</span>
                            <span class="fw-semibold text-dark">Every {{ $site->check_interval_minutes }} mins</span>
                        </li>
                        <li class="d-flex justify-content-between py-2">
                            <span class="text-muted">Last Checked:</span>
                            <span class="fw-semibold text-dark">
                                {{ $site->last_checked_at ? $site->last_checked_at->format('M d, Y H:i:s') : 'Never' }}
                            </span>
                        </li>
                    </ul>

                    @if($site->last_error)
                        <div class="mt-3 p-2 bg-danger-subtle rounded text-danger small">
                            <i class="bi bi-exclamation-triangle-fill me-1"></i>
                            <strong>Error:</strong> {{ $site->last_error }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- 2. SSL Certificate Details -->
        <div class="col-12 col-md-4">
            <div class="card bg-white h-100 shadow-sm border-0">
                <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                    <span class="fw-bold text-dark small text-uppercase">
                        <i class="bi bi-shield-lock text-info me-2"></i> SSL Certificate
                    </span>
                    @if($site->ssl_status === 'valid')
                        <span class="badge bg-success-subtle text-success border border-success-subtle">Valid</span>
                    @elseif($site->ssl_status === 'expiring_soon')
                        <span class="badge bg-warning text-dark">Expiring Soon</span>
                    @elseif($site->ssl_status === 'expired')
                        <span class="badge bg-danger text-white">Expired</span>
                    @else
                        <span class="badge bg-secondary-subtle text-secondary">Unknown</span>
                    @endif
                </div>
                <div class="card-body p-3">
                    @php $sslDays = $site->daysUntilSslExpires(); @endphp
                    <ul class="list-unstyled mb-0 small">
                        <li class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted">Certificate Issuer:</span>
                            <span class="fw-semibold text-dark text-truncate" style="max-width: 170px;" title="{{ $site->ssl_issuer }}">
                                {{ $site->ssl_issuer ?: 'Unknown' }}
                            </span>
                        </li>
                        <li class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted">Expires On:</span>
                            <span class="fw-semibold text-dark">
                                {{ $site->ssl_expires_at ? $site->ssl_expires_at->format('M d, Y') : 'Not checked' }}
                            </span>
                        </li>
                        <li class="d-flex justify-content-between py-2">
                            <span class="text-muted">Time Remaining:</span>
                            <span class="fw-bold {{ $sslDays <= 14 ? 'text-danger' : ($sslDays <= 30 ? 'text-warning' : 'text-success') }}">
                                {{ $sslDays !== null ? ($sslDays < 0 ? 'Expired ' . abs($sslDays) . ' days ago' : $sslDays . ' days left') : '—' }}
                            </span>
                        </li>
                    </ul>

                    @if($sslDays !== null && $sslDays <= 14)
                        <div class="mt-3 p-2 bg-warning-subtle rounded text-dark small">
                            <i class="bi bi-alarm me-1"></i>
                            <strong>Urgent Action:</strong> SSL certificate requires renewal soon!
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- 3. Domain & Hosting Lifecycle -->
        <div class="col-12 col-md-4">
            <div class="card bg-white h-100 shadow-sm border-0">
                <div class="card-header bg-white border-bottom py-3">
                    <span class="fw-bold text-dark small text-uppercase">
                        <i class="bi bi-calendar-check text-secondary me-2"></i> Domain & Hosting Expiry
                    </span>
                </div>
                <div class="card-body p-3">
                    @php
                        $domainDays = $site->daysUntilDomainExpires();
                        $hostDays = $site->daysUntilHostExpires();
                    @endphp
                    <ul class="list-unstyled mb-0 small">
                        <li class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted">Domain Registrar:</span>
                            <span class="fw-semibold text-dark">{{ $site->domain_registrar ?: 'Manual tracking' }}</span>
                        </li>
                        <li class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted">Domain Expiry:</span>
                            <div>
                                <span class="fw-semibold text-dark">{{ $site->domain_expires_at ? $site->domain_expires_at->format('M d, Y') : 'Not set' }}</span>
                                @if($domainDays !== null)
                                    <span class="badge {{ $domainDays <= 30 ? 'bg-warning text-dark' : 'bg-light text-secondary border' }} ms-1">
                                        {{ $domainDays }}d
                                    </span>
                                @endif
                            </div>
                        </li>
                        <li class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted">Hosting Provider:</span>
                            <span class="fw-semibold text-dark">{{ $site->host_provider ?: 'Not specified' }}</span>
                        </li>
                        <li class="d-flex justify-content-between py-2">
                            <span class="text-muted">Hosting Renewal:</span>
                            <div>
                                <span class="fw-semibold text-dark">{{ $site->host_expires_at ? $site->host_expires_at->format('M d, Y') : 'Not set' }}</span>
                                @if($hostDays !== null)
                                    <span class="badge {{ $hostDays <= 30 ? 'bg-warning text-dark' : 'bg-light text-secondary border' }} ms-1">
                                        {{ $hostDays }}d
                                    </span>
                                @endif
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Client Info Card -->
    <div class="card bg-white shadow-sm border-0 mb-4">
        <div class="card-header bg-white border-bottom py-3">
            <span class="fw-bold text-dark small text-uppercase">
                <i class="bi bi-person-badge text-primary me-2"></i> Client Information
            </span>
        </div>
        <div class="card-body p-3">
            <div class="row g-3 small">
                <div class="col-md-4">
                    <span class="text-muted d-block">Client Name / Business:</span>
                    <span class="fw-semibold text-dark fs-6">{{ $site->client_name ?: 'No client name specified' }}</span>
                </div>
                <div class="col-md-4">
                    <span class="text-muted d-block">Email Address:</span>
                    @if($site->client_email)
                        <a href="mailto:{{ $site->client_email }}" class="fw-semibold text-decoration-none">
                            <i class="bi bi-envelope me-1"></i>{{ $site->client_email }}
                        </a>
                    @else
                        <span class="text-muted">—</span>
                    @endif
                </div>
                <div class="col-md-4">
                    <span class="text-muted d-block">Phone / WhatsApp:</span>
                    @if($site->client_phone)
                        <a href="tel:{{ $site->client_phone }}" class="fw-semibold text-decoration-none">
                            <i class="bi bi-telephone me-1"></i>{{ $site->client_phone }}
                        </a>
                    @else
                        <span class="text-muted">—</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Historical Check Logs Table -->
    <div class="card bg-white shadow-sm border-0 mb-4">
        <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
            <div>
                <h5 class="fw-bold mb-0 text-dark small text-uppercase">
                    <i class="bi bi-clock-history text-secondary me-2"></i> Recent Health Check Logs
                </h5>
            </div>
            <span class="text-muted small">Showing last {{ $recentChecks->count() }} checks</span>
        </div>

        @if($recentChecks->count() > 0)
            <div class="table-responsive">
                <table class="table table-custom table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">Timestamp</th>
                            <th scope="col">Status</th>
                            <th scope="col">HTTP Code</th>
                            <th scope="col">Latency</th>
                            <th scope="col">Diagnostics</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentChecks as $check)
                            <tr>
                                <td class="small text-muted font-monospace">
                                    {{ $check->checked_at->format('Y-m-d H:i:s') }}
                                </td>
                                <td>
                                    @if($check->is_online)
                                        <span class="badge bg-success-subtle text-success border border-success-subtle badge-pill">
                                            <i class="bi bi-check-circle me-1"></i> Online
                                        </span>
                                    @else
                                        <span class="badge bg-danger text-white badge-pill">
                                            <i class="bi bi-x-circle me-1"></i> Offline
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border font-monospace">
                                        {{ $check->status_code ?? '0' }}
                                    </span>
                                </td>
                                <td>
                                    @if($check->response_time_ms)
                                        <span class="small fw-semibold {{ $check->response_time_ms > 1000 ? 'text-warning' : 'text-dark' }}">
                                            {{ $check->response_time_ms }} ms
                                        </span>
                                    @else
                                        <span class="text-muted small">—</span>
                                    @endif
                                </td>
                                <td class="small text-muted">
                                    {{ $check->error_message ?: 'Healthy connection' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="p-4 text-center text-muted small">
                No check history logged yet. Click "Check Now" above to trigger an immediate check.
            </div>
        @endif
    </div>

@endsection
