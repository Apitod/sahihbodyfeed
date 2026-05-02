@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<style>
    .bg-brand-1 { background-color: var(--brand-1) !important; }
    .bg-brand-2 { background-color: var(--brand-2) !important; }
    .bg-brand-3 { background-color: var(--brand-3) !important; }
    .bg-brand-4 { background-color: var(--brand-4) !important; }
    
    .avatar-brand-2 { background-color: color-mix(in srgb, var(--brand-2) 20%, white); color: var(--brand-2); }
    .avatar-brand-3 { background-color: color-mix(in srgb, var(--brand-3) 20%, white); color: #8a701d; }
    .avatar-brand-4 { background-color: color-mix(in srgb, var(--brand-4) 20%, white); color: var(--brand-4); }
</style>

<div class="page-header d-print-none mb-4">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle text-uppercase fw-bold text-brand-4" style="letter-spacing: 0.1em;">Ringkasan Global</div>
                <h2 class="page-title fw-black fs-2">Dashboard Administrasi</h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <a href="{{ route('admin.agents.create') }}" class="btn btn-primary d-none d-sm-inline-block rounded-3">
                        <i class="ti ti-plus me-1"></i> Tambah Agen Baru
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-xl">
    {{-- ─── Main Stats Grid ────────────────────────────────────────── --}}
    <div class="row row-cards mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-md rounded-3 avatar-brand-4 me-3">
                            <i class="ti ti-users fs-2"></i>
                        </div>
                        <div>
                            <div class="text-muted small fw-bold text-uppercase">Total Agen</div>
                            <div class="h2 fw-bold mb-0">{{ number_format($stats['total_agents'], 0, ',', '.') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-md rounded-3 avatar-brand-2 me-3">
                            <i class="ti ti-user-plus fs-2"></i>
                        </div>
                        <div>
                            <div class="text-muted small fw-bold text-uppercase">Agen Baru (Bulan Ini)</div>
                            <div class="h2 fw-bold mb-0 text-brand-2">{{ $stats['new_agents_month'] }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-md rounded-3 avatar-brand-3 me-3">
                            <i class="ti ti-clock-hour-4 fs-2"></i>
                        </div>
                        <div>
                            <div class="text-muted small fw-bold text-uppercase">Antrian Verifikasi</div>
                            <div class="h2 fw-bold mb-0" style="color: #8a701d;">{{ $stats['pending_verifications'] }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-md rounded-3 shadow-sm bg-brand-4 text-white me-3">
                            <i class="ti ti-chart-dots fs-2"></i>
                        </div>
                        <div>
                            <div class="text-muted small fw-bold text-uppercase">Total Volume Verified</div>
                            <div class="h2 fw-bold mb-0">Rp {{ number_format($stats['total_transactions_value'], 0, ',', '.') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ─── Financial Stats Row ────────────────────────────────────────── --}}
    <div class="row row-cards mb-4">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 bg-brand-1">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="mb-1 fw-bold text-brand-4">Total Komisi Terbentuk</h3>
                            <div class="h1 fw-black mb-0">Rp {{ number_format($stats['total_commissions_generated'], 0, ',', '.') }}</div>
                            <p class="text-muted small mt-2 mb-0">Akumulasi seluruh komisi yang dihasilkan oleh jaringan agen.</p>
                        </div>
                        <div class="col-auto">
                            <i class="ti ti-receipt-2 fs-1 text-brand-4 opacity-25"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4" style="background-color: #f0f4f8;">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="mb-1 fw-bold" style="color: #2c3e50;">Komisi Telah Terbayar</h3>
                            <div class="h1 fw-black mb-0" style="color: #2c3e50;">Rp {{ number_format($stats['total_paid_commissions'], 0, ',', '.') }}</div>
                            <p class="text-muted small mt-2 mb-0">Total komisi yang statusnya sudah 'Paid' di sistem.</p>
                        </div>
                        <div class="col-auto">
                            <i class="ti ti-checkup-list fs-1 opacity-25" style="color: #2c3e50;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row row-cards mb-4">
        {{-- Urgent Verification Queue --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                    <h3 class="card-title fw-bold">Antrian Verifikasi Mendesak</h3>
                    <a href="{{ route('admin.verifications.transactions') }}" class="btn btn-sm btn-ghost-primary rounded-pill">Lihat Semua</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-vcenter card-table table-hover">
                        <thead>
                            <tr>
                                <th>Agen</th>
                                <th>Invoice</th>
                                <th>Jumlah</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pendingTransactions as $tx)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-xs rounded-circle bg-brand-1 text-brand-4 me-2">
                                                {{ substr($tx->agent->nama, 0, 1) }}
                                            </div>
                                            <div class="fw-bold">{{ $tx->agent->nama }}</div>
                                        </div>
                                    </td>
                                    <td>#{{ $tx->invoice_number }}</td>
                                    <td class="fw-bold">Rp {{ number_format($tx->amount, 0, ',', '.') }}</td>
                                    <td class="text-end">
                                        <form action="{{ route('admin.transactions.approve', $tx) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success rounded-pill px-3">Approve</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted small">Tidak ada antrian verifikasi saat ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Newest Agents --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-transparent border-0 pt-4 px-4">
                    <h3 class="card-title fw-bold">Agen Terbaru</h3>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($recentAgents as $agent)
                            <div class="list-group-item border-0 px-4 py-3">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <span class="avatar avatar-md rounded-pill avatar-brand-2">
                                            {{ substr($agent->nama, 0, 2) }}
                                        </span>
                                    </div>
                                    <div class="col text-truncate">
                                        <a href="{{ route('admin.agents.show', $agent) }}" class="text-body d-block fw-bold">{{ $agent->nama }}</a>
                                        <div class="text-muted small text-truncate">{{ $agent->status->label() }}</div>
                                    </div>
                                    <div class="col-auto">
                                        <div class="text-muted small">{{ $agent->joined_at->diffForHumans() }}</div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="p-4 text-center text-muted small">Belum ada agen terdaftar.</div>
                        @endforelse
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 pb-4 px-4">
                    <a href="{{ route('admin.agents.index') }}" class="btn btn-light w-100 rounded-3 small">Lihat Semua Agen</a>
                </div>
            </div>
        </div>
    </div>

    {{-- Overall Recent Activity --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-transparent border-0 pt-4 px-4">
                    <h3 class="card-title fw-bold">Seluruh Transaksi Terbaru</h3>
                </div>
                <div class="table-responsive">
                    <table class="table table-vcenter card-table">
                        <thead>
                            <tr>
                                <th>Agen</th>
                                <th>ID Transaksi</th>
                                <th>Waktu</th>
                                <th>Jumlah</th>
                                <th class="text-end">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentTransactions as $rt)
                                <tr>
                                    <td>{{ $rt->agent->nama }}</td>
                                    <td class="text-muted">#{{ $rt->id }}</td>
                                    <td>{{ $rt->created_at->format('d M Y H:i') }}</td>
                                    <td class="fw-bold">Rp {{ number_format($rt->amount, 0, ',', '.') }}</td>
                                    <td class="text-end">
                                        <span class="badge {{ $rt->status->badgeColor() }} rounded-pill px-3">
                                            {{ $rt->status->label() }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
@endsection
