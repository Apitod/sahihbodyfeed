@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<style>
    .avatar-brand-2 { background-color: color-mix(in srgb, var(--brand-2) 20%, white); color: var(--brand-2); }
    .avatar-brand-3 { background-color: color-mix(in srgb, var(--brand-3) 20%, white); color: #8a701d; }
    .avatar-brand-4 { background-color: color-mix(in srgb, var(--brand-4) 20%, white); color: var(--brand-4); }
</style>

<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title fw-bold">Ringkasan Admin</h2>
                <div class="text-muted small mt-1">Pantau performa dan verifikasi sistem dari sini.</div>
            </div>
        </div>
    </div>
</div>

<div class="row row-cards mt-1">
    <!-- Total Agents Card -->
    <div class="col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <span class="avatar avatar-md rounded-3 avatar-brand-4 shadow-sm">
                            <i class="ti ti-users fs-2"></i>
                        </span>
                    </div>
                    <div class="col">
                        <div class="text-muted small fw-bold text-uppercase">Total Agen</div>
                        <div class="h3 fw-bold mb-0">{{ $stats['total_agents'] }} Orang</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Transactions Card -->
    <div class="col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <span class="avatar avatar-md rounded-3 avatar-brand-3 shadow-sm">
                            <i class="ti ti-clock fs-2"></i>
                        </span>
                    </div>
                    <div class="col">
                        <div class="text-muted small fw-bold text-uppercase">Antrian Transaksi</div>
                        <div class="h3 fw-bold mb-0 text-brand-4">{{ $stats['pending_verifications'] }} Verifikasi</div>
                    </div>
                    <div class="col-auto">
                        <a href="{{ route('admin.verifications.transactions') }}" class="btn btn-ghost-warning btn-icon rounded-pill">
                            <i class="ti ti-chevron-right fs-3"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Reward Claims Card -->
    <div class="col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <span class="avatar avatar-md rounded-3 avatar-brand-2 shadow-sm">
                            <i class="ti ti-gift fs-2"></i>
                        </span>
                    </div>
                    <div class="col">
                        <div class="text-muted small fw-bold text-uppercase">Klaim Reward</div>
                        <div class="h3 fw-bold mb-0" style="color: var(--brand-2);">{{ $stats['pending_reward_claims'] }} Menunggu</div>
                    </div>
                    <div class="col-auto">
                        <a href="{{ route('admin.verifications.rewards') }}" class="btn btn-ghost-success btn-icon rounded-pill">
                            <i class="ti ti-chevron-right fs-3"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Revenue Card -->
    <div class="col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <span class="avatar avatar-md rounded-3 shadow-sm" style="background: var(--brand-gradient); color: white;">
                            <i class="ti ti-chart-bar fs-2"></i>
                        </span>
                    </div>
                    <div class="col">
                        <div class="text-muted small fw-bold text-uppercase">Total Volume</div>
                        <div class="h3 fw-bold mb-0">Rp {{ number_format((float)$stats['total_transactions_value'], 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row row-cards mt-2">
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4" style="background: var(--brand-1);">
            <div class="card-body py-5 px-4 text-center">
                <div class="avatar avatar-lg rounded-circle mb-3 avatar-brand-4">
                    <i class="ti ti-check fs-1"></i>
                </div>
                <h2 class="fw-black mb-2" style="color: var(--brand-4);">Sistem Sahihbodyfeed Siap Operasi</h2>
                <p class="text-secondary mx-auto mb-4" style="max-width: 600px;">Seluruh modul pendaftaran, repeat order, dan pembagian reward telah terintegrasi dengan benar sesuai flowchart bisnis. Admin dapat mulai melakukan verifikasi pada kartu antrian di atas.</p>
                <div class="d-flex justify-content-center gap-2">
                    <a href="{{ route('admin.verifications.transactions') }}" class="btn btn-primary px-4 py-2">Mulai Verifikasi</a>
                    <a href="{{ route('admin.agents.index') }}" class="btn btn-outline-primary px-4 py-2">Kelola Agen</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
@endsection
