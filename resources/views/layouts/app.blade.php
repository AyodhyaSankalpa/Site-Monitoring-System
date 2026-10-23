<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Site Pulse Monitor') - Site Monitoring System</title>

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="alternate icon" href="{{ asset('favicon.ico') }}">

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5.3.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --bs-body-font-family: 'Inter', system-ui, -apple-system, sans-serif;
            --bs-body-bg: #f8fafc;
            --bs-card-border-radius: 0.85rem;
            --brand-primary: #3b82f6;
            --brand-dark: #0f172a;
        }

        body {
            font-family: var(--bs-body-font-family);
            background-color: var(--bs-body-bg);
            color: #1e293b;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .navbar-custom {
            background-color: #0f172a;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .card {
            border: 1px solid #e2e8f0;
            border-radius: var(--bs-card-border-radius);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }

        .card-stat {
            position: relative;
            overflow: hidden;
        }

        .card-stat:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.06);
        }

        .stat-icon {
            width: 46px;
            height: 46px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            font-size: 1.35rem;
        }

        .pulse-dot {
            display: inline-block;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            margin-right: 6px;
            animation: pulse-animation 2s infinite;
        }

        .pulse-online {
            background-color: #10b981;
            box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
        }

        .pulse-down {
            background-color: #ef4444;
            box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7);
        }

        @keyframes pulse-animation {
            0% {
                box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.6);
            }
            70% {
                box-shadow: 0 0 0 8px rgba(16, 185, 129, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(16, 185, 129, 0);
            }
        }

        .table-custom {
            --bs-table-bg: transparent;
            --bs-table-hover-bg: #f1f5f9;
        }

        .table-custom th {
            font-weight: 600;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #64748b;
            border-top: none;
            padding: 1rem 0.75rem;
        }

        .table-custom td {
            vertical-align: middle;
            padding: 0.9rem 0.75rem;
            font-size: 0.9rem;
        }

        .badge-pill {
            font-weight: 500;
            padding: 0.4em 0.75em;
            border-radius: 30px;
        }

        .btn-check-now {
            transition: all 0.2s ease;
        }

        .btn-check-now:hover {
            transform: scale(1.05);
        }

        .spinning {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            100% { transform: rotate(360deg); }
        }
    </style>
    @yield('styles')
</head>
<body>

    <!-- Header Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom py-3">
        <div class="container-fluid px-lg-5">
            <a class="navbar-brand d-flex align-items-center fw-bold" href="{{ route('sites.index') }}">
                <span class="d-inline-flex align-items-center justify-content-center bg-primary text-white rounded-3 p-2 me-2" style="width: 36px; height: 36px;">
                    <i class="bi bi-activity"></i>
                </span>
                <span>SitePulse</span>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-3">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('sites.index') ? 'active fw-semibold' : '' }}" href="{{ route('sites.index') }}">
                            <i class="bi bi-speedometer2 me-1"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('sites.create') ? 'active fw-semibold' : '' }}" href="{{ route('sites.create') }}">
                            <i class="bi bi-plus-circle me-1"></i> Add Website
                        </a>
                    </li>
                    <!-- <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('guide') ? 'active fw-semibold' : '' }}" href="{{ route('guide') }}">
                            <i class="bi bi-book me-1"></i> Guide
                        </a>
                    </li> -->
                </ul>

                <div class="d-flex align-items-center gap-2">
                    <form action="{{ route('sites.checkAll') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-light btn-sm px-3" onclick="this.innerHTML='<span class=\'spinner-border spinner-border-sm me-1\'></span> Checking all...'; this.disabled=true; this.form.submit();">
                            <i class="bi bi-arrow-repeat me-1"></i> Check All Sites
                        </button>
                    </form>
                    <a href="{{ route('sites.create') }}" class="btn btn-primary btn-sm px-3 shadow-sm">
                        <i class="bi bi-plus-lg me-1"></i> Add New Site
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content Area -->
    <main class="flex-grow-1 py-4">
        <div class="container-fluid px-lg-5">
            <!-- Flash Alerts -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 d-flex align-items-center" role="alert">
                    <i class="bi bi-check-circle-fill fs-5 me-2"></i>
                    <div>{{ session('success') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('info'))
                <div class="alert alert-info alert-dismissible fade show shadow-sm border-0 d-flex align-items-center" role="alert">
                    <i class="bi bi-info-circle-fill fs-5 me-2"></i>
                    <div>{{ session('info') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 d-flex align-items-center" role="alert">
                    <i class="bi bi-exclamation-triangle-fill fs-5 me-2"></i>
                    <div>{{ session('error') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0" role="alert">
                    <div class="fw-bold mb-1"><i class="bi bi-exclamation-circle-fill me-1"></i> Please fix the following errors:</div>
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-top py-3 mt-auto">
        <div class="container-fluid px-lg-5">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center text-center text-md-start gap-2 gap-md-0 small text-muted">
                <div class="d-flex flex-wrap justify-content-center justify-content-md-start align-items-center gap-2 gap-sm-3">
                    <span class="fw-semibold text-dark">&copy; {{ date('Y') }} Site Pulse Monitor</span>
                    <span class="text-secondary opacity-50 d-none d-sm-inline">&bull;</span>
                    <a href="{{ route('guide') }}" class="text-decoration-none fw-medium text-secondary link-primary d-inline-flex align-items-center">
                        <i class="bi bi-question-circle me-1 text-primary"></i> User Guide &amp; Documentation
                    </a>
                </div>
                <div class="d-flex align-items-center justify-content-center gap-3">
                    <a href="https://github.com/AyodhyaSankalpa/Site-Monitoring-System" target="_blank" rel="noopener noreferrer" class="text-decoration-none text-dark link-secondary d-inline-flex align-items-center p-1" title="View Repository on GitHub">
                        <i class="bi bi-github fs-5"></i>
                    </a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    @yield('scripts')
</body>
</html>
