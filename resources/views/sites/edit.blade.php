@extends('layouts.app')

@section('title', 'Edit ' . $site->name)

@section('content')

    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <nav aria-label="breadcrumb" class="mb-3">
                <ol class="breadcrumb small">
                    <li class="breadcrumb-item"><a href="{{ route('sites.index') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('sites.show', $site) }}" class="text-decoration-none">{{ $site->name }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit</li>
                </ol>
            </nav>

            <div class="card bg-white shadow-sm border-0 mb-4">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="fw-bold mb-0 text-dark">
                            <i class="bi bi-pencil-square text-primary me-2"></i> Edit Website Settings
                        </h5>
                        <p class="text-muted small mb-0 mt-1">Update details for <strong>{{ $site->name }}</strong>.</p>
                    </div>
                    <form action="{{ route('sites.check', $site) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-arrow-repeat me-1"></i> Check Now
                        </button>
                    </form>
                </div>

                <div class="card-body p-4">
                    <form action="{{ route('sites.update', $site) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Active Toggle -->
                        <div class="form-check form-switch mb-4 p-3 bg-light rounded-3">
                            <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" value="1" {{ old('is_active', $site->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold text-dark" for="is_active">
                                Enable Continuous Monitoring
                            </label>
                            <div class="text-muted small">When disabled, this site will be skipped during scheduled automated checks.</div>
                        </div>

                        <!-- SECTION 1: Site & Client Basics -->
                        <h6 class="fw-bold text-uppercase text-primary small mb-3 tracking-wide">
                            <i class="bi bi-globe me-1"></i> 1. Website & Client Details
                        </h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="name" class="form-label fw-semibold small">Site / Project Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $site->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="url" class="form-label fw-semibold small">Site URL <span class="text-danger">*</span></label>
                                <input type="text" name="url" id="url" class="form-control @error('url') is-invalid @enderror" value="{{ old('url', $site->url) }}" required>
                                @error('url')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="client_name" class="form-label fw-semibold small">Client Name / Business</label>
                                <input type="text" name="client_name" id="client_name" class="form-control @error('client_name') is-invalid @enderror" value="{{ old('client_name', $site->client_name) }}">
                                @error('client_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="client_email" class="form-label fw-semibold small">Client Email</label>
                                <input type="email" name="client_email" id="client_email" class="form-control @error('client_email') is-invalid @enderror" value="{{ old('client_email', $site->client_email) }}">
                                @error('client_email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="client_phone" class="form-label fw-semibold small">Client Phone</label>
                                <input type="text" name="client_phone" id="client_phone" class="form-control @error('client_phone') is-invalid @enderror" value="{{ old('client_phone', $site->client_phone) }}">
                                @error('client_phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <hr class="text-muted my-4">

                        <!-- SECTION 2: Domain Expiration -->
                        <h6 class="fw-bold text-uppercase text-info small mb-3 tracking-wide">
                            <i class="bi bi-calendar2-range me-1"></i> 2. Domain Registration
                        </h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="domain_registrar" class="form-label fw-semibold small">Domain Registrar</label>
                                <input type="text" name="domain_registrar" id="domain_registrar" class="form-control @error('domain_registrar') is-invalid @enderror" value="{{ old('domain_registrar', $site->domain_registrar) }}">
                                @error('domain_registrar')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="domain_expires_at" class="form-label fw-semibold small">Domain Expiration Date</label>
                                <input type="date" name="domain_expires_at" id="domain_expires_at" class="form-control @error('domain_expires_at') is-invalid @enderror" value="{{ old('domain_expires_at', $site->domain_expires_at?->format('Y-m-d')) }}">
                                @error('domain_expires_at')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <hr class="text-muted my-4">

                        <!-- SECTION 3: Hosting & Infrastructure -->
                        <h6 class="fw-bold text-uppercase text-secondary small mb-3 tracking-wide">
                            <i class="bi bi-hdd-network me-1"></i> 3. Hosting & Infrastructure
                        </h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label for="host_provider" class="form-label fw-semibold small">Hosting Provider</label>
                                <input type="text" name="host_provider" id="host_provider" class="form-control @error('host_provider') is-invalid @enderror" value="{{ old('host_provider', $site->host_provider) }}">
                                @error('host_provider')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="host_ip" class="form-label fw-semibold small">Server IP Address</label>
                                <input type="text" name="host_ip" id="host_ip" class="form-control @error('host_ip') is-invalid @enderror" value="{{ old('host_ip', $site->host_ip) }}">
                                @error('host_ip')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="host_expires_at" class="form-label fw-semibold small">Hosting Renewal Date</label>
                                <input type="date" name="host_expires_at" id="host_expires_at" class="form-control @error('host_expires_at') is-invalid @enderror" value="{{ old('host_expires_at', $site->host_expires_at?->format('Y-m-d')) }}">
                                @error('host_expires_at')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="check_interval_minutes" class="form-label fw-semibold small">Check Interval</label>
                                <select name="check_interval_minutes" id="check_interval_minutes" class="form-select @error('check_interval_minutes') is-invalid @enderror">
                                    <option value="5" {{ old('check_interval_minutes', $site->check_interval_minutes) == 5 ? 'selected' : '' }}>Every 5 Minutes (High Priority)</option>
                                    <option value="15" {{ old('check_interval_minutes', $site->check_interval_minutes) == 15 ? 'selected' : '' }}>Every 15 Minutes (Standard Default)</option>
                                    <option value="30" {{ old('check_interval_minutes', $site->check_interval_minutes) == 30 ? 'selected' : '' }}>Every 30 Minutes</option>
                                    <option value="60" {{ old('check_interval_minutes', $site->check_interval_minutes) == 60 ? 'selected' : '' }}>Every 1 Hour</option>
                                </select>
                                @error('check_interval_minutes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                            <a href="{{ route('sites.show', $site) }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-1"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary px-4 shadow-sm">
                                <i class="bi bi-check-lg me-1"></i> Update Site Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection
