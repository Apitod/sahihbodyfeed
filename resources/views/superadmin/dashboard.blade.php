@extends('layouts.app')
@section('title', 'Dashboard Superadmin')

@section('content')
<style>
    .stat-card { border-radius: 16px; border: none; transition: transform 0.2s, box-shadow 0.2s; }
    .stat-card:hover { transform: translateY(-3px); box-shadow: 0 12px 30px rgba(0,0,0,0.10) !important; }
    .metric-value { font-size: 2rem; font-weight: 900; letter-spacing: -0.03em; }
    .metric-label { font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; }
    .section-title { font-weight: 800; font-size: 1rem; letter-spacing: -0.01em; }
    .chart-bar-wrap { display: flex; align-items: flex-end; gap: 6px; height: 80px; }
    .chart-bar { flex: 1; border-radius: 6px 6px 0 0; min-width: 0; transition: opacity 0.2s; position: relative; cursor: default; }
    .chart-bar:hover { opacity: 0.8; }
    .chart-bar .bar-tooltip { display: none; position: absolute; bottom: 105%; left: 50%; transform: translateX(-50%);
        background: #1e293b; color: #fff; font-size: 0.65rem; white-space: nowrap; padding: 3px 7px; border-radius: 6px; font-weight: 700; z-index: 10; }
    .chart-bar:hover .bar-tooltip { display: block; }
    .month-label { font-size: 0.6rem; color: #94a3b8; text-align: center; font-weight: 600; }
    .pill-badge { padding: 0.3em 0.75em; border-radius: 999px; font-size: 0.72rem; font-weight: 700; }
    .queue-urgent { border-left: 4px solid #f0a04b; }
</style>

{{-- Page Header --}}
<div class="page-header d-print-none mb-4">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle text-uppercase fw-bold" style="color:var(--brand-4);letter-spacing:.1em;">Kontrol Pusat</div>
                <h2 class="page-title fw-black fs-2">Dashboard Eksekutif
                    <span class="badge ms-2 align-middle" style="background:rgba(240,160,75,.15);color:var(--brand-4);font-size:.6rem;vertical-align:middle;">SUPERADMIN</span>
                </h2>
            </div>
            <div class="col-auto ms-auto">
                <div class="text-muted small fw-bold">{{ now()->isoFormat('dddd, D MMMM Y') }}</div>
            </div>
        </div>
    </div>
</div>

<div class="container-xl">

{{-- ── ROW 1: 6 KPI CARDS ────────────────────────────────────────────── --}}
<div class="row g-3 mb-4">

    {{-- Total Omzet --}}
    <div class="col-6 col-lg-4">
        <div class="card stat-card shadow-sm h-100" style="background:linear-gradient(135deg,var(--brand-4),#d48a3a);">
            <div class="card-body p-4">
                <div class="metric-label text-white opacity-75 mb-2">Total Omzet (Approved)</div>
                <div class="metric-value text-white">Rp {{ number_format($totalSales, 0, ',', '.') }}</div>
                <div class="mt-2 small text-white opacity-75">Bulan ini: <strong>Rp {{ number_format($monthlySales, 0, ',', '.') }}</strong></div>
            </div>
        </div>
    </div>

    {{-- Total Agen --}}
    <div class="col-6 col-lg-4">
        <div class="card stat-card shadow-sm h-100" style="background:var(--brand-2);">
            <div class="card-body p-4">
                <div class="metric-label text-white opacity-75 mb-2">Total Agen Aktif</div>
                <div class="metric-value text-white">{{ number_format($totalAgents, 0, ',', '.') }}</div>
                <div class="mt-2 small text-white opacity-75">+{{ $newAgentsThisMonth }} agen baru bulan ini</div>
            </div>
        </div>
    </div>

    {{-- Komisi --}}
    <div class="col-6 col-lg-4">
        <div class="card stat-card shadow-sm h-100 bg-white">
            <div class="card-body p-4">
                <div class="metric-label text-muted mb-2">Total Komisi Terbentuk</div>
                <div class="metric-value">Rp {{ number_format($totalCommissions, 0, ',', '.') }}</div>
                <div class="mt-2 small text-muted">Belum dibayar: <strong class="text-danger">Rp {{ number_format($unpaidCommissions, 0, ',', '.') }}</strong></div>
            </div>
        </div>
    </div>

    {{-- Antrian Admin --}}
    <div class="col-6 col-lg-4">
        <div class="card stat-card shadow-sm h-100 bg-white">
            <div class="card-body p-4">
                <div class="metric-label text-muted mb-2">Menunggu Admin (Review)</div>
                <div class="metric-value {{ $pendingForAdmin > 0 ? 'text-warning' : 'text-success' }}">
                    {{ $pendingForAdmin }}
                </div>
                <div class="mt-2 small text-muted">Transaksi belum direview Admin</div>
            </div>
        </div>
    </div>

    {{-- Antrian Superadmin --}}
    <div class="col-6 col-lg-4">
        <div class="card stat-card shadow-sm h-100 bg-white queue-urgent">
            <div class="card-body p-4">
                <div class="metric-label text-muted mb-2">Menunggu Approval Anda</div>
                <div class="metric-value {{ $pendingForSuperadmin > 0 ? 'text-danger' : 'text-success' }}">
                    {{ $pendingForSuperadmin }}
                </div>
                <div class="mt-2">
                    @if($pendingForSuperadmin > 0)
                        <a href="{{ route('superadmin.verifications.transactions') }}" class="btn btn-sm btn-danger rounded-pill px-3" style="font-size:.72rem;">
                            Proses Sekarang →
                        </a>
                    @else
                        <span class="small text-success fw-bold">✓ Semua sudah diproses</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Reward Claims --}}
    <div class="col-6 col-lg-4">
        <div class="card stat-card shadow-sm h-100 bg-white">
            <div class="card-body p-4">
                <div class="metric-label text-muted mb-2">Klaim Reward</div>
                <div class="d-flex align-items-center gap-2 mt-1">
                    <div>
                        <div class="metric-value" style="font-size:1.6rem;">{{ $approvedClaimsCount }}</div>
                        <div class="small text-success fw-bold">Approved</div>
                    </div>
                    <div class="vr mx-1"></div>
                    <div>
                        <div class="metric-value text-warning" style="font-size:1.6rem;">{{ $pendingSuperadminClaimsCount }}</div>
                        <div class="small text-muted fw-bold">Perlu Review</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- ── ROW 2: RO CHART + AGENT GROWTH CHART ─────────────────────────── --}}
<div class="row g-3 mb-4">

    {{-- RO Bulanan Chart --}}
    <div class="col-lg-7">
        <div class="card shadow-sm h-100" style="border-radius:16px;">
            <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
                <div class="section-title">📦 Repeat Order Bulanan ({{ now()->year }})</div>
                <div class="text-muted small">Jumlah RO yang sudah disetujui per bulan</div>
            </div>
            <div class="card-body px-4 pb-4">
                @php
                    $months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Ags','Sep','Okt','Nov','Des'];
                    $roValues = $roChartData->pluck('count_ro')->toArray();
                    $roMax = max(1, ...$roValues);
                @endphp
                <div class="chart-bar-wrap mt-3">
                    @foreach($roChartData as $i => $row)
                        @php $pct = $roMax > 0 ? ($row['count_ro'] / $roMax) * 100 : 0; @endphp
                        <div style="flex:1;display:flex;flex-direction:column;align-items:center;gap:4px;">
                            <div class="chart-bar" style="width:100%;height:{{ max(4, $pct * 0.8) }}px;background:{{ $row['count_ro'] > 0 ? 'var(--brand-4)' : '#e2e8f0' }};">
                                <span class="bar-tooltip">{{ $row['count_ro'] }} RO<br>Rp {{ number_format($row['total_ro'],0,',','.') }}</span>
                            </div>
                            <div class="month-label">{{ $months[$i] }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- Pertumbuhan Agen Chart --}}
    <div class="col-lg-5">
        <div class="card shadow-sm h-100" style="border-radius:16px;">
            <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
                <div class="section-title">📈 Pertumbuhan Agen ({{ now()->year }})</div>
                <div class="text-muted small">Jumlah agen baru per bulan</div>
            </div>
            <div class="card-body px-4 pb-4">
                @php
                    $agentValues = $agentChartData->pluck('total')->toArray();
                    $agentMax = max(1, max($agentValues));
                @endphp
                <div class="chart-bar-wrap mt-3">
                    @foreach($agentChartData as $i => $row)
                        @php $pct = $agentMax > 0 ? ($row['total'] / $agentMax) * 100 : 0; @endphp
                        <div style="flex:1;display:flex;flex-direction:column;align-items:center;gap:4px;">
                            <div class="chart-bar" style="width:100%;height:{{ max(4, $pct * 0.8) }}px;background:{{ $row['total'] > 0 ? 'var(--brand-2)' : '#e2e8f0' }};">
                                <span class="bar-tooltip">{{ $row['total'] }} Agen</span>
                            </div>
                            <div class="month-label">{{ $months[$i] }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

</div>

{{-- ── ROW 3: ANTRIAN APPROVAL + ADMIN PERFORMANCE ─────────────────── --}}
<div class="row g-3 mb-4">

    {{-- Antrian Menunggu Superadmin --}}
    <div class="col-lg-8">
        <div class="card shadow-sm" style="border-radius:16px;">
            <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                <div>
                    <div class="section-title">🔔 Transaksi Menunggu Approval Anda</div>
                    <div class="text-muted small">Sudah diverifikasi Admin, perlu persetujuan final</div>
                </div>
                <a href="{{ route('superadmin.verifications.transactions') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">Lihat Semua</a>
            </div>
            <div class="table-responsive">
                <table class="table table-vcenter card-table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Agen</th>
                            <th>Tipe</th>
                            <th>Review oleh Admin</th>
                            <th>Jumlah</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pendingSuperadminTx as $tx)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="avatar avatar-xs rounded-circle" style="background:var(--brand-1);color:var(--brand-4);font-weight:800;">
                                        {{ substr($tx->agent->nama ?? '?', 0, 1) }}
                                    </span>
                                    <span class="fw-bold small">{{ $tx->agent->nama ?? '-' }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="pill-badge bg-light text-dark">{{ $tx->type->label() }}</span>
                            </td>
                            <td class="small text-muted">{{ $tx->adminVerifier?->username ?? '-' }}</td>
                            <td class="fw-bold">Rp {{ number_format($tx->amount, 0, ',', '.') }}</td>
                            <td class="text-end">
                                <div class="d-flex gap-1 justify-content-end">
                                    <form action="{{ route('superadmin.transactions.approve', $tx) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success rounded-pill px-3" style="font-size:.72rem;">✓ Setujui</button>
                                    </form>
                                    <form action="{{ route('superadmin.transactions.reject', $tx) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Yakin ingin menolak transaksi ini?')">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-danger rounded-pill px-3" style="font-size:.72rem;">✗ Tolak</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <div class="fs-3 mb-2">✅</div>
                                <div class="fw-bold">Tidak ada transaksi yang menunggu approval Anda.</div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Kinerja Admin Bulan Ini --}}
    <div class="col-lg-4">
        <div class="card shadow-sm h-100" style="border-radius:16px;">
            <div class="card-header bg-transparent border-0 pt-4 px-4">
                <div class="section-title">👤 Kinerja Admin (Bulan Ini)</div>
                <div class="text-muted small">Jumlah transaksi yang direview per admin</div>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @forelse($adminPerformance as $perf)
                    <div class="list-group-item border-0 px-4 py-3">
                        <div class="d-flex align-items-center gap-3">
                            <span class="avatar avatar-md rounded-pill" style="background:var(--brand-1);color:var(--brand-4);font-weight:800;">
                                {{ substr($perf->adminVerifier?->username ?? 'A', 0, 2) }}
                            </span>
                            <div class="flex-fill">
                                <div class="fw-bold small">{{ $perf->adminVerifier?->username ?? 'Admin #'.$perf->verified_by_admin_id }}</div>
                                <div class="progress mt-1" style="height:5px;border-radius:99px;">
                                    @php
                                        $maxVerified = $adminPerformance->max('total_verified') ?: 1;
                                        $pct = ($perf->total_verified / $maxVerified) * 100;
                                    @endphp
                                    <div class="progress-bar" style="width:{{ $pct }}%;background:var(--brand-4);border-radius:99px;"></div>
                                </div>
                            </div>
                            <span class="pill-badge" style="background:rgba(240,160,75,.15);color:var(--brand-4);">
                                {{ $perf->total_verified }} tx
                            </span>
                        </div>
                    </div>
                    @empty
                    <div class="p-4 text-center text-muted small">
                        Belum ada Admin yang memproses transaksi bulan ini.
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

</div>

{{-- ── ROW 4: KOMISI BREAKDOWN ───────────────────────────────────────── --}}
<div class="row g-3 mb-4">
    <div class="col-lg-4">
        <div class="card shadow-sm" style="border-radius:16px;background:linear-gradient(135deg,#f8fafc,#f1f5f9);">
            <div class="card-body p-4">
                <div class="section-title mb-3">💰 Komisi Breakdown</div>
                <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                    <span class="small text-muted fw-bold">Total Terbentuk</span>
                    <span class="fw-black">Rp {{ number_format($totalCommissions, 0, ',', '.') }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                    <span class="small text-muted fw-bold">Sudah Dibayar</span>
                    <span class="fw-black text-success">Rp {{ number_format($paidCommissions, 0, ',', '.') }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center py-2">
                    <span class="small text-muted fw-bold">Belum Dibayar</span>
                    <span class="fw-black text-danger">Rp {{ number_format($unpaidCommissions, 0, ',', '.') }}</span>
                </div>
                @if($totalCommissions > 0)
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card shadow-sm" style="border-radius:16px;background:linear-gradient(135deg,#f8fafc,#f1f5f9);">
            <div class="card-body p-4">
                <div class="section-title mb-3">🏆 Status Klaim Reward</div>
                <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                    <span class="small text-muted fw-bold">Menunggu Admin</span>
                    <span class="pill-badge bg-warning-lt text-warning">{{ $pendingClaimsCount }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                    <span class="small text-muted fw-bold">Menunggu Superadmin</span>
                    <span class="pill-badge bg-azure-lt text-azure">{{ $pendingSuperadminClaimsCount }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center py-2">
                    <span class="small text-muted fw-bold">Sudah Disetujui</span>
                    <span class="pill-badge bg-success-lt text-success">{{ $approvedClaimsCount }}</span>
                </div>
                @if($pendingSuperadminClaimsCount > 0)
                <div class="mt-3">
                    <a href="{{ route('superadmin.verifications.rewards') }}" class="btn btn-sm w-100 rounded-3 fw-bold"
                       style="background:var(--brand-4);color:white;font-size:.8rem;">
                        Proses {{ $pendingSuperadminClaimsCount }} Klaim →
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card shadow-sm h-100" style="border-radius:16px;background:linear-gradient(135deg,var(--brand-4),#d48a3a);">
            <div class="card-body p-4 d-flex flex-column justify-content-between">
                <div>
                    <div class="section-title text-white opacity-75 mb-2">⚡ Quick Actions</div>
                    <p class="text-white opacity-75 small">Akses cepat ke fitur utama Superadmin.</p>
                </div>
                <div class="d-flex flex-column gap-2">
                    <a href="{{ route('superadmin.verifications.transactions') }}" class="btn btn-light rounded-3 fw-bold text-start small">
                        📋 Kelola Approval Transaksi
                    </a>
                    <a href="{{ route('superadmin.agents.index') }}" class="btn btn-light rounded-3 fw-bold text-start small">
                        👥 Manajemen Agen (Full)
                    </a>
                    <a href="{{ route('superadmin.commissions.index') }}" class="btn btn-light rounded-3 fw-bold text-start small">
                        💸 Laporan & Bayar Komisi
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

</div>{{-- /container-xl --}}
@endsection
