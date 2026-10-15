@extends('layouts.app')

@section('title', 'User Guide & Documentation')

@section('content')

    <!-- Header Section -->
    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb small">
                <li class="breadcrumb-item"><a href="{{ route('sites.index') }}" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">User Guide</li>
            </ol>
        </nav>

        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <h1 class="h3 fw-bold text-dark mb-1">
                    <i class="bi bi-book text-primary me-2"></i> User Guide & Documentation
                </h1>
                <p class="text-muted small mb-0">Learn how to add websites, understand health status indicators, and automate background checks.</p>
            </div>
            <a href="{{ route('sites.create') }}" class="btn btn-primary btn-sm shadow-sm">
                <i class="bi bi-plus-circle me-1"></i> Add Your First Website
            </a>
        </div>
    </div>

    <div class="row g-4">
        <!-- Main Documentation Content -->
        <div class="col-12 col-lg-8">

            <!-- GUIDE 1: How to Add a Website -->
            <div class="card bg-white shadow-sm border-0 mb-4" id="add-website">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="fw-bold mb-0 text-dark">
                        <i class="bi bi-plus-square text-primary me-2"></i> 1. How to Add a Website
                    </h5>
                </div>
                <div class="card-body p-4">
                    <p class="text-muted small mb-4">Adding a website to SitePulse takes less than a minute. Follow these simple steps:</p>

                    <div class="timeline-steps">
                        <!-- Step 1 -->
                        <div class="d-flex gap-3 mb-4">
                            <div class="flex-shrink-0">
                                <span class="badge bg-primary rounded-circle p-2 px-3 fs-6">1</span>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-1">Click "Add New Site"</h6>
                                <p class="text-muted small mb-0">Navigate to the Dashboard or click the <strong>"Add New Site"</strong> button in the top navigation bar.</p>
                            </div>
                        </div>

                        <!-- Step 2 -->
                        <div class="d-flex gap-3 mb-4">
                            <div class="flex-shrink-0">
                                <span class="badge bg-primary rounded-circle p-2 px-3 fs-6">2</span>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-1">Enter Website Name & Target URL</h6>
                                <p class="text-muted small mb-1">Enter an identifiable name (e.g., <code>Production Portal</code>) and the full website URL (e.g., <code>https://example.com</code>).</p>
                                <div class="bg-light p-2 rounded small text-secondary">
                                    <i class="bi bi-info-circle me-1"></i> If you omit <code>https://</code>, the system will automatically format the URL for you.
                                </div>
                            </div>
                        </div>

                        <!-- Step 3 -->
                        <div class="d-flex gap-3 mb-4">
                            <div class="flex-shrink-0">
                                <span class="badge bg-primary rounded-circle p-2 px-3 fs-6">3</span>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-1">Configure Domain & Hosting Expiry Dates (Optional)</h6>
                                <p class="text-muted small mb-0">Optionally record your domain registrar and hosting renewal dates. SitePulse will monitor them and display countdown warning badges starting 30 days before expiration.</p>
                            </div>
                        </div>

                        <!-- Step 4 -->
                        <div class="d-flex gap-3">
                            <div class="flex-shrink-0">
                                <span class="badge bg-success rounded-circle p-2 px-3 fs-6">4</span>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-1">Save & Run Initial Diagnostic Check</h6>
                                <p class="text-muted small mb-0">Click <strong>"Save & Run Initial Check"</strong>. The system will immediately send an anti-cache HTTP ping, inspect SSL certificate validity, and record the server response time on your dashboard.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- GUIDE 2: Status Indicators Explained -->
            <div class="card bg-white shadow-sm border-0 mb-4" id="statuses-explained">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="fw-bold mb-0 text-dark">
                        <i class="bi bi-check2-circle text-success me-2"></i> 2. Status Indicators & Metrics Explained
                    </h5>
                </div>
                <div class="card-body p-4">
                    <p class="text-muted small mb-4">Here is a comprehensive breakdown of all health badges and status indicators displayed across the dashboard:</p>

                    <!-- Status 1: Uptime & Latency -->
                    <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
                        <i class="bi bi-activity text-primary me-1"></i> Uptime & Server Response
                    </h6>
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered table-sm align-middle small">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 140px;">Badge</th>
                                    <th>Status Name</th>
                                    <th>Description & Meaning</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <span class="badge bg-success-subtle text-success fw-bold px-2 py-1">200 OK</span>
                                    </td>
                                    <td><strong>Online (Up)</strong></td>
                                    <td>The web server is reachable and returned a full HTTP success code (200, 301, or 302).</td>
                                </tr>
                                <tr>
                                    <td>
                                        <span class="badge bg-success-subtle text-success fw-bold px-2 py-1">206 Partial</span>
                                    </td>
                                    <td><strong>Online (Up - Fast Probe)</strong></td>
                                    <td>The server responded with partial content (first 1KB byte-range). This is a standard 2xx success status used to save bandwidth and compute while verifying live uptime with minimal latency.</td>
                                </tr>
                                <tr>
                                    <td>
                                        <span class="badge bg-danger text-white fw-bold px-2 py-1">DOWN / 500</span>
                                    </td>
                                    <td><strong>Offline (Down)</strong></td>
                                    <td>The site is currently down or unreachable. Caused by an origin server error (500, 502, 503) or a connection/DNS timeout.</td>
                                </tr>
                                <tr>
                                    <td>
                                        <span class="text-warning small fw-bold"><i class="bi bi-lightning-charge-fill"></i> 45 ms</span>
                                    </td>
                                    <td><strong>Response Latency</strong></td>
                                    <td>Round-trip server response time in milliseconds. Below <code>300ms</code> is optimal; above <code>1000ms</code> indicates a slow server response.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Status 2: SSL Certificates -->
                    <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
                        <i class="bi bi-shield-lock text-info me-1"></i> SSL Certificate Expiration Status
                    </h6>
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered table-sm align-middle small">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 140px;">Badge</th>
                                    <th>SSL Status</th>
                                    <th>Meaning & Recommended Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle">Valid</span>
                                    </td>
                                    <td><strong>Valid & Secure</strong></td>
                                    <td>The SSL certificate is active, verified, and has more than 14 days remaining before expiration.</td>
                                </tr>
                                <tr>
                                    <td>
                                        <span class="badge bg-warning text-dark">Expiring Soon</span>
                                    </td>
                                    <td><strong>Expiring Soon (Alert)</strong></td>
                                    <td>The SSL certificate expires in <strong>14 days or less</strong>. Immediate renewal is recommended to avoid browser warnings.</td>
                                </tr>
                                <tr>
                                    <td>
                                        <span class="badge bg-danger text-white">Expired</span>
                                    </td>
                                    <td><strong>Expired!</strong></td>
                                    <td>The SSL certificate has lapsed. Visitors will see a "Your connection is not private / security risk" browser warning.</td>
                                </tr>
                                <tr>
                                    <td>
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle">Invalid SSL</span>
                                    </td>
                                    <td><strong>Invalid / Misconfigured</strong></td>
                                    <td>Port 443 connection failed, hostname mismatch, or self-signed certificate handshake error.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Status 3: Domain & Hosting Renewals -->
                    <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
                        <i class="bi bi-calendar-event text-secondary me-1"></i> Domain & Hosting Expiry Badges
                    </h6>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm align-middle small">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 140px;">Badge</th>
                                    <th>Countdown</th>
                                    <th>Meaning</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><span class="badge bg-light text-secondary border">85d left</span></td>
                                    <td><strong>Safe</strong></td>
                                    <td>More than 30 days remaining before renewal is due.</td>
                                </tr>
                                <tr>
                                    <td><span class="badge bg-warning text-dark">18d left</span></td>
                                    <td><strong>Renewal Warning</strong></td>
                                    <td>Expires within 30 days. Prepare invoice or process payment with your registrar or hosting provider.</td>
                                </tr>
                                <tr>
                                    <td><span class="badge bg-danger text-white">5d left / Expired</span></td>
                                    <td><strong>Urgent Action</strong></td>
                                    <td>Expires in less than 7 days or has already expired. Renew immediately to prevent domain suspension or hosting termination.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- GUIDE 3: Real-Time Anti-Cache Technology -->
            <div class="card bg-white shadow-sm border-0 mb-4" id="how-it-works">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="fw-bold mb-0 text-dark">
                        <i class="bi bi-cpu text-info me-2"></i> 3. Direct Live Ping & Anti-Cache Architecture
                    </h5>
                </div>
                <div class="card-body p-4 small text-muted">
                    <h6 class="fw-bold text-dark">Why Manual Browser Checks Can Be Deceptive</h6>
                    <p>When loading a website in a standard browser, your local browser cache, service workers, or ISP-level proxy caches may display an outdated cached page. Even if the origin web server has crashed or is returning a 500 Internal Server Error, a browser might falsely appear to load the site.</p>

                    <h6 class="fw-bold text-dark mt-3">How SitePulse Eliminates False Positives</h6>
                    <ul class="mb-0 ps-3">
                        <li><strong>Zero-Cache HTTP Headers:</strong> Every server probe sends explicit <code>Cache-Control: no-cache, no-store, must-revalidate</code> and <code>Pragma: no-cache</code> headers, bypassing all upstream proxies and CDNs.</li>
                        <li><strong>High-Speed Byte-Range Probing (HTTP 206):</strong> To prevent excessive bandwidth usage and speed up checks across dozens of sites, SitePulse requests the initial 1KB slice of the webpage. Origin servers that support byte-ranges return <code>HTTP 206 Partial Content</code>, which confirms healthy live status instantly without downloading large media files or heavy bundles.</li>
                        <li><strong>Direct Socket Handshake for SSL:</strong> Connects directly to port 443 via OpenSSL stream sockets to parse the live peer certificate and calculate authentic expiration dates.</li>
                        <li><strong>Live RDAP / Registry Sync:</strong> Automatically verifies authoritative domain registration and expiry dates directly with registry servers.</li>
                    </ul>
                </div>
            </div>

            <!-- GUIDE 4: Automation with Cron Jobs -->
            <div class="card bg-white shadow-sm border-0 mb-4" id="automation">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="fw-bold mb-0 text-dark">
                        <i class="bi bi-terminal text-dark me-2"></i> 4. Background Automation with Cron Jobs
                    </h5>
                </div>
                <div class="card-body p-4">
                    <p class="text-muted small mb-3">To automatically monitor all websites in the background without needing to keep the dashboard open, use the built-in Artisan command:</p>

                    <div class="bg-dark text-white p-3 rounded font-monospace small mb-3">
                        php artisan sites:check
                    </div>

                    <p class="text-muted small mb-2">To schedule automatic checks every 15 minutes on a production Linux server (cPanel, VPS, Ubuntu), add this entry to your server's crontab:</p>
                    <div class="bg-light border p-3 rounded font-monospace small text-dark">
                        */15 * * * * cd /path-to-your-project &amp;&amp; php artisan sites:check &gt;&gt; /dev/null 2&gt;&amp;1
                    </div>
                </div>
            </div>

        </div>

        <!-- Right Side Quick Navigation & Tips -->
        <div class="col-12 col-lg-4">
            <div class="card bg-white shadow-sm border-0 sticky-top" style="top: 1.5rem;">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="fw-bold text-dark mb-0 small text-uppercase">
                        <i class="bi bi-compass text-primary me-2"></i> Quick Navigation
                    </h6>
                </div>
                <div class="list-group list-group-flush small">
                    <a href="#add-website" class="list-group-item list-group-item-action d-flex align-items-center">
                        <i class="bi bi-plus-square text-primary me-2"></i> 1. How to Add a Website
                    </a>
                    <a href="#statuses-explained" class="list-group-item list-group-item-action d-flex align-items-center">
                        <i class="bi bi-check2-circle text-success me-2"></i> 2. Statuses &amp; Metrics Explained
                    </a>
                    <a href="#how-it-works" class="list-group-item list-group-item-action d-flex align-items-center">
                        <i class="bi bi-cpu text-info me-2"></i> 3. Direct Live Ping Architecture
                    </a>
                    <a href="#automation" class="list-group-item list-group-item-action d-flex align-items-center">
                        <i class="bi bi-terminal text-dark me-2"></i> 4. Automation &amp; Cron Jobs
                    </a>
                </div>

                <div class="card-body p-3 bg-light border-top">
                    <h6 class="fw-bold small text-dark mb-2">
                        <i class="bi bi-lightbulb text-warning me-1"></i> Pro Tip
                    </h6>
                    <p class="text-muted small mb-0">
                        Click the <strong>"Check All Sites"</strong> button on your dashboard to instantly trigger live diagnostic checks for every registered website without a page reload.
                    </p>
                </div>
            </div>
        </div>
    </div>

@endsection
