@extends('layouts.app')

@section('title', 'Dashboard Agen')

@section('content')
    <style>
        .bg-brand-1 { background-color: var(--brand-1) !important; }
        .bg-brand-2 { background-color: var(--brand-2) !important; }
        .bg-brand-3 { background-color: var(--brand-3) !important; }
        .bg-brand-4 { background-color: var(--brand-4) !important; }
        
        .text-brand-4 { color: var(--brand-4) !important; }
        
        .avatar-brand-2 { background-color: color-mix(in srgb, var(--brand-2) 20%, white); color: var(--brand-2); }
        .avatar-brand-3 { background-color: color-mix(in srgb, var(--brand-3) 20%, white); color: #8a701d; }
        .avatar-brand-4 { background-color: color-mix(in srgb, var(--brand-4) 20%, white); color: var(--brand-4); }

        .progress-bar-brand { background-color: var(--brand-4); }
    </style>

    {{-- ─── Hero / Welcome Section ────────────────────────────────────────── --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm overflow-hidden text-white" style="background: var(--brand-gradient); border-radius: 16px;">
                <div class="card-body p-4 p-md-5">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="avatar avatar-xl rounded-4 bg-white-lt shadow-sm">
                                {{ substr(auth()->user()->agent->nama, 0, 2) }}
                            </span>
                        </div>
                        <div class="col">
                            <h1 class="fw-bold mb-1">Selamat Datang, {{ auth()->user()->agent->nama }}!</h1>
                            <p class="opacity-75 mb-3">Senang melihat Anda kembali. Berikut adalah ringkasan kinerja bisnis Anda hari ini.</p>
                            <div class="d-flex flex-wrap gap-2">
                                <span class="badge bg-white-lt px-3 py-2 rounded-pill">
                                    <i class="ti ti-id me-1"></i> ID Agen: #{{ str_pad(auth()->user()->agent->id, 5, '0', STR_PAD_LEFT) }}
                                </span>
                                <span class="badge bg-white-lt px-3 py-2 rounded-pill">
                                    <i class="ti ti-calendar me-1"></i> Bergabung: {{ auth()->user()->agent->joined_at->format('d M Y') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ─── Rank Progress Section ────────────────────────────────────────── --}}
    <div class="row row-cards mb-4">
        <div class="col-md-12 col-lg-8">
            <div class="card border-0 shadow-sm h-100 rounded-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <div>
                            <h3 class="card-title fw-bold mb-1">Progres Peringkat</h3>
                            <p class="text-muted small mb-0">Tingkatkan poin Anda untuk mencapai peringkat berikutnya.</p>
                        </div>
                        <div class="text-end">
                            <span class="badge px-3 py-2 rounded-pill" style="background: var(--brand-1); color: var(--brand-4);">{{ auth()->user()->agent->status->label() }}</span>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="fw-bold text-dark">{{ auth()->user()->agent->status->label() }}</span>
                            <span class="fw-bold text-brand-4">{{ $nextRank['label'] }}</span>
                        </div>
                        <div class="progress progress-lg rounded-pill shadow-sm" style="height: 12px; background: var(--brand-1);">
                            <div class="progress-bar progress-bar-brand progress-bar-striped progress-bar-animated" style="width: {{ $nextRank['percent'] }}%" role="progressbar"></div>
                        </div>
                    </div>

                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center text-muted small">
                            <i class="ti ti-trending-up me-1"></i>
                            <span>{{ auth()->user()->agent->total_points }} Poin saat ini</span>
                        </div>
                        @if($nextRank['needed'] > 0)
                            <div class="fw-bold small text-brand-4">
                                Butuh {{ $nextRank['needed'] }} poin lagi untuk ke {{ $nextRank['label'] }}
                            </div>
                        @else
                            <div class="text-success fw-bold small">
                                Anda telah mencapai peringkat tertinggi!
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-12 col-lg-4">
            <div class="card border-0 shadow-sm h-100 rounded-4 bg-light-subtle overflow-hidden">
                <div class="card-body p-4">
                    <h3 class="card-title fw-bold mb-3">Tindakan Cepat</h3>
                    <div class="d-grid gap-2">
                        <a href="{{ route('agent.transactions.index') }}" class="btn btn-outline-primary d-flex align-items-center justify-content-between py-2 px-3 rounded-3 bg-white">
                            <span>Riwayat Transaksi</span>
                            <i class="ti ti-plus fs-4"></i>
                        </a>
                        <a href="{{ route('agent.network') }}" class="btn btn-outline-primary d-flex align-items-center justify-content-between py-2 px-3 rounded-3 bg-white">
                            <span>Lihat Pohon Jaringan</span>
                            <i class="ti ti-sitemap fs-4"></i>
                        </a>
                        <a href="{{ route('agent.commissions') }}" class="btn btn-outline-primary d-flex align-items-center justify-content-between py-2 px-3 rounded-3 bg-white" style="border-color: var(--brand-2) !important; color: var(--brand-2) !important;">
                            <span>Laporan Komisi</span>
                            <i class="ti ti-receipt fs-4"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ─── Main Stats Row ────────────────────────────────────────── --}}
    <div class="row row-cards mb-4">
        <div class="col-sm-6 col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-md rounded-3 avatar-brand-2 me-3">
                            <i class="ti ti-wallet fs-2"></i>
                        </div>
                        <div>
                            <div class="text-muted small fw-bold text-uppercase">Total Komisi</div>
                            <div class="h2 fw-bold mb-0">Rp {{ number_format($stats['total_commissions'], 0, ',', '.') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-md rounded-3 avatar-brand-3 me-3">
                            <i class="ti ti-award fs-2"></i>
                        </div>
                        <div>
                            <div class="text-muted small fw-bold text-uppercase">Poin RO</div>
                            <div class="h2 fw-bold mb-0">{{ $stats['total_points'] }} Poin</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-12 col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-md rounded-3 avatar-brand-4 me-3">
                            <i class="ti ti-users fs-2"></i>
                        </div>
                        <div>
                            <div class="text-muted small fw-bold text-uppercase">Total Downline</div>
                            <div class="h2 fw-bold mb-0">{{ $stats['total_downlines'] }} Member</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ─── Recent Activity Section ────────────────────────────────────────── --}}
    <div class="row row-cards">
        {{-- Recent Commissions --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-transparent border-0 pt-4 px-4">
                    <h3 class="card-title fw-bold">Komisi Terbaru</h3>
                </div>
                <div class="table-responsive">
                    <table class="table table-vcenter card-table table-hover">
                        <thead>
                            <tr>
                                <th>Keterangan</th>
                                <th class="text-end">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentCommissions as $commission)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm rounded-2 avatar-brand-2 me-2">
                                                <i class="ti ti-cash"></i>
                                            </div>
                                            <div>
                                                <div class="fw-bold">{{ $commission->type->label() }}</div>
                                                <div class="text-muted small">{{ $commission->created_at->format('d M Y') }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-end fw-bold" style="color: var(--brand-2);">
                                        + Rp {{ number_format($commission->amount, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="text-center py-4 text-muted small">Belum ada komisi tercatat.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer bg-transparent border-0 pb-4 px-4">
                    <a href="{{ route('agent.commissions') }}" class="btn btn-light w-100 rounded-3 small">Lihat Semua Komisi</a>
                </div>
            </div>
        </div>

        {{-- Recent Transactions --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-transparent border-0 pt-4 px-4">
                    <h3 class="card-title fw-bold">Transaksi Terbaru</h3>
                </div>
                <div class="table-responsive">
                    <table class="table table-vcenter card-table table-hover">
                        <thead>
                            <tr>
                                <th>Transaksi</th>
                                <th class="text-end">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentTransactions as $tx)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm rounded-2 avatar-brand-4 me-2">
                                                <i class="ti ti-shopping-cart"></i>
                                            </div>
                                            <div>
                                                <div class="fw-bold">#{{ $tx->invoice_number }}</div>
                                                <div class="text-muted small">Rp {{ number_format($tx->total_amount, 0, ',', '.') }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-end">
                                        <span class="badge {{ $tx->status->badgeColor() }} rounded-pill px-2">
                                            {{ $tx->status->label() }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="text-center py-4 text-muted small">Belum ada transaksi tercatat.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer bg-transparent border-0 pb-4 px-4">
                    <a href="{{ route('agent.transactions.index') }}" class="btn btn-light w-100 rounded-3 small">Lihat Semua Transaksi</a>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
@endsection