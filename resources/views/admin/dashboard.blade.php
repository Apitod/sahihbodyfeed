@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">Ringkasan Admin</h2>
                <div class="text-muted small mt-1">Pantau performa dan verifikasi sistem dari sini.</div>
            </div>
        </div>
    </div>
</div>

<div class="row row-cards mt-1">
    <!-- Total Agents Card -->
    <div class="col-sm-6 col-lg-3">
        <div class="card card-sm border-0 shadow-sm">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <span class="bg-primary text-white avatar shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" /><path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" /><path d="M16 3.13a4 4 0 0 1 0 7.75" /><path d="M21 21v-2a4 4 0 0 0 -3 -3.85" /></svg>
                        </span>
                    </div>
                    <div class="col">
                        <div class="font-weight-medium">Total Agen</div>
                        <div class="text-secondary small">{{ $stats['total_agents'] }} Orang Terdaftar</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Transactions Card -->
    <div class="col-sm-6 col-lg-3">
        <div class="card card-sm border-0 shadow-sm">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <span class="bg-yellow text-white avatar shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M12 8v4" /><path d="M12 16h.01" /></svg>
                        </span>
                    </div>
                    <div class="col">
                        <div class="font-weight-medium">Antrian Transaksi</div>
                        <div class="text-warning small font-weight-bold">{{ $stats['pending_verifications'] }} Perlu Verifikasi</div>
                    </div>
                    <div class="col-auto">
                        <a href="{{ route('admin.verifications.transactions') }}" class="btn btn-ghost-warning btn-sm">Lihat</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Reward Claims Card -->
    <div class="col-sm-6 col-lg-3">
        <div class="card card-sm border-0 shadow-sm">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <span class="bg-green text-white avatar shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 3a12 12 0 0 0 8.5 3a12 12 0 0 1 -8.5 15a12 12 0 0 1 -8.5 -15a12 12 0 0 0 8.5 -3" /><path d="M9 12l2 2l4 -4" /></svg>
                        </span>
                    </div>
                    <div class="col">
                        <div class="font-weight-medium">Klaim Reward</div>
                        <div class="text-green small font-weight-bold">{{ $stats['pending_reward_claims'] }} Menunggu</div>
                    </div>
                    <div class="col-auto">
                        <a href="{{ route('admin.verifications.rewards') }}" class="btn btn-ghost-success btn-sm">Buka</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Revenue Card -->
    <div class="col-sm-6 col-lg-3">
        <div class="card card-sm border-0 shadow-sm">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <span class="bg-blue text-white avatar shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M17 8v-3a1 1 0 0 0 -1 -1h-10a2 2 0 0 0 0 4h12a1 1 0 0 1 1 1v3a1 1 0 0 1 -1 1h-12a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2" /><path d="M10 12h4" /></svg>
                        </span>
                    </div>
                    <div class="col">
                        <div class="font-weight-medium">Total Volume</div>
                        <div class="text-primary small font-weight-bold">Rp {{ number_format((float)$stats['total_transactions_value'], 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row row-cards mt-2">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body py-4">
                <h3 class="card-title">Sistem Sahihbodyfeed Siap Operasi</h3>
                <p class="text-secondary small">Seluruh modul pendaftaran, repeat order, dan pembagian reward telah terintegrasi dengan benar sesuai flowchart bisnis. Admin dapat mulai melakukan verifikasi pada kartu antrian di atas.</p>
            </div>
        </div>
    </div>
</div>
@endsection
