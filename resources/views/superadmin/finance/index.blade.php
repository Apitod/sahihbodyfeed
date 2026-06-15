@extends('layouts.app')

@section('title', 'Laporan Keuangan & Kalkulator Stok')

@section('content')
<style>
    .stat-card { border-radius: 16px; border: none; transition: transform 0.2s, box-shadow 0.2s; }
    .stat-card:hover { transform: translateY(-3px); box-shadow: 0 12px 30px rgba(0,0,0,0.10) !important; }
    .metric-value { font-size: 1.8rem; font-weight: 900; letter-spacing: -0.03em; }
    .metric-label { font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; }
    .section-title { font-weight: 800; font-size: 1rem; letter-spacing: -0.01em; }

    /* In-memory CSS bar chart styling */
    .chart-bar-wrap { display: flex; align-items: flex-end; gap: 6px; height: 120px; }
    .chart-bar { width: 100%; border-radius: 6px 6px 0 0; min-width: 0; transition: opacity 0.2s; position: relative; cursor: default; }
    .chart-bar:hover { opacity: 0.8; }
    .chart-bar .bar-tooltip { display: none; position: absolute; bottom: 105%; left: 50%; transform: translateX(-50%);
        background: #1e293b; color: #fff; font-size: 0.65rem; white-space: nowrap; padding: 3px 7px; border-radius: 6px; font-weight: 700; z-index: 10; }
    .chart-bar:hover .bar-tooltip { display: block; }
    .month-label { font-size: 0.6rem; color: #94a3b8; text-align: center; font-weight: 600; }

    /* Number spinner button styling */
    .btn-spinner {
        width: 38px;
        height: 38px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0;
        font-weight: bold;
    }

    /* Print styling rules */
    @media print {
        body { background: white !important; color: black !important; }
        .d-print-none, .navbar-nav, .sidebar, .nav-tabs, .btn, form { display: none !important; }
        .card { border: none !important; box-shadow: none !important; }
        .table-responsive { overflow: visible !important; }
        .container-xl { max-width: 100% !important; padding: 0 !important; }
    }
</style>

{{-- Page Header --}}
<div class="page-header d-print-none mb-4">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle text-uppercase fw-bold" style="color:var(--brand-4);letter-spacing:.1em;">Pusat Data Owner</div>
                <h2 class="page-title fw-black fs-2">Laporan Keuangan & Stok
                    <span class="badge ms-2 align-middle" style="background:rgba(240,160,75,.15);color:var(--brand-4);font-size:.6rem;vertical-align:middle;">KEUANGAN</span>
                </h2>
            </div>
            <div class="col-auto ms-auto d-flex gap-2">
                <button type="button" class="btn btn-outline-secondary rounded-pill d-print-none" onclick="window.print()">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2" /><path d="M17 9v-4a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v4" /><path d="M7 13m0 2a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v4a2 2 0 0 1 -2 2h-6a2 2 0 0 1 -2 -2z" /></svg>
                    Print Laporan
                </button>
            </div>
        </div>
    </div>
</div>

<div class="container-xl">

    {{-- Tabs --}}
    @php
        $activeTab = session('active_tab', 'summary');
    @endphp
    <ul class="nav nav-tabs mb-4 d-print-none" id="finance-tabs" role="tablist">
        <li class="nav-item">
            <button class="nav-link {{ $activeTab === 'summary' ? 'active fw-bold' : '' }}" id="summary-tab" data-bs-toggle="tab" data-bs-target="#tab-summary" type="button">
                💵 Ringkasan Keuangan
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link {{ $activeTab === 'calculator' ? 'active fw-bold' : '' }}" id="calculator-tab" data-bs-toggle="tab" data-bs-target="#tab-calculator" type="button">
                📊 Kalkulator Stok & Promo
            </button>
        </li>
    </ul>

    <div class="tab-content">

        {{-- ── TAB 1: SUMMARY & TRANSACTION HISTORY ── --}}
        <div class="tab-pane fade {{ $activeTab === 'summary' ? 'show active' : '' }}" id="tab-summary">

            {{-- KPI cards --}}
            <div class="row g-3 mb-4">
                <div class="col-6 col-lg-3">
                    <div class="card stat-card shadow-sm h-100 bg-white border-start border-primary border-3">
                        <div class="card-body p-3">
                            <div class="metric-label text-muted mb-1">Total Omzet (Approved)</div>
                            <div class="metric-value text-primary">Rp {{ number_format($totalOmzetAllTime, 0, ',', '.') }}</div>
                            <div class="small text-muted mt-2">Bulan Ini: <strong>Rp {{ number_format($totalOmzetThisMonth, 0, ',', '.') }}</strong></div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-lg-3">
                    <div class="card stat-card shadow-sm h-100 bg-white border-start border-purple border-3">
                        <div class="card-body p-3">
                            <div class="metric-label text-muted mb-1">Omzet Registrasi Baru</div>
                            <div class="metric-value text-purple">Rp {{ number_format($totalNewAgentIncome, 0, ',', '.') }}</div>
                            <div class="small text-muted mt-2">Registrasi Agen Baru</div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-lg-3">
                    <div class="card stat-card shadow-sm h-100 bg-white border-start border-blue border-3">
                        <div class="card-body p-3">
                            <div class="metric-label text-muted mb-1">Omzet Repeat Order</div>
                            <div class="metric-value text-blue">Rp {{ number_format($totalROIncome, 0, ',', '.') }}</div>
                            <div class="small text-muted mt-2">Pemesanan Ulang</div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-lg-3">
                    <div class="card stat-card shadow-sm h-100 bg-white border-start border-orange border-3">
                        <div class="card-body p-3">
                            <div class="metric-label text-muted mb-1">Komisi Terbayar / Total</div>
                            <div class="metric-value text-orange">Rp {{ number_format($totalCommissionsPaid, 0, ',', '.') }}</div>
                            <div class="small text-muted mt-2">Total: <strong>Rp {{ number_format($totalCommissionsGenerated, 0, ',', '.') }}</strong></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 12-Month Chart --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header"><h3 class="card-title">Tren Omzet 12 Bulan Terakhir</h3></div>
                <div class="card-body">
                    @php
                        $maxIncome = $incomeData->max() ?: 1;
                    @endphp
                    <div class="chart-bar-wrap mb-2">
                        @foreach($incomeData as $index => $value)
                            @php
                                $height = ($value / $maxIncome) * 100;
                            @endphp
                            <div class="chart-bar bg-primary" style="height: {{ max($height, 4) }}%;">
                                <div class="bar-tooltip">Rp {{ number_format($value, 0, ',', '.') }}</div>
                            </div>
                        @endforeach
                    </div>
                    <div class="row g-0">
                        @foreach($months as $month)
                            <div class="col text-center">
                                <div class="month-label">{{ $month }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Filter form & Transactions Table --}}
            <div class="card shadow-sm">
                <div class="card-header d-print-none">
                    <h3 class="card-title">Riwayat Transaksi</h3>
                </div>

                {{-- Filters --}}
                <div class="card-body border-bottom d-print-none">
                    <form method="GET" action="{{ route('superadmin.finance.index') }}" class="row g-2">
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Tipe Transaksi</label>
                            <select class="form-select" name="type" onchange="this.form.submit()">
                                <option value="all" @selected($filterType === 'all')>Semua</option>
                                <option value="new_agent" @selected($filterType === 'new_agent')>Registrasi Agen</option>
                                <option value="repeat_order" @selected($filterType === 'repeat_order')>Repeat Order</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Status</label>
                            <select class="form-select" name="status" onchange="this.form.submit()">
                                <option value="all" @selected($filterStatus === 'all')>Semua</option>
                                <option value="pending" @selected($filterStatus === 'pending')>Menunggu Admin</option>
                                <option value="pending_superadmin" @selected($filterStatus === 'pending_superadmin')>Menunggu Superadmin</option>
                                <option value="approved" @selected($filterStatus === 'approved')>Disetujui</option>
                                <option value="rejected" @selected($filterStatus === 'rejected')>Ditolak</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Bulan Pembuatan</label>
                            <input type="month" class="form-control" name="month" value="{{ $filterMonth }}" onchange="this.form.submit()">
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <a href="{{ route('superadmin.finance.index') }}" class="btn btn-outline-secondary w-100">Reset</a>
                        </div>
                    </form>
                </div>

                {{-- Table --}}
                <div class="table-responsive">
                    <table class="table table-vcenter card-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Agen</th>
                                <th>Tipe</th>
                                <th>Nominal</th>
                                <th>Bukti</th>
                                <th>Status</th>
                                <th>Reviewer</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($query as $txn)
                            <tr>
                                <td>
                                    <span class="text-muted fw-bold">#{{ $txn->id }}</span><br>
                                    <small class="text-muted">{{ $txn->created_at->format('d/m/Y H:i') }}</small>
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $txn->agent->nama }}</div>
                                    <div class="text-muted small">@ {{ $txn->agent->user->username ?? '-' }}</div>
                                </td>
                                <td>
                                    <span class="badge {{ $txn->type->value === 'new_agent' ? 'bg-purple-lt' : 'bg-blue-lt' }}">
                                        {{ $txn->type->label() }}
                                    </span>
                                </td>
                                <td class="fw-bold">Rp {{ number_format($txn->amount, 0, ',', '.') }}</td>
                                <td>
                                    @if($txn->proof_of_payment)
                                        <a href="{{ asset('storage/' . $txn->proof_of_payment) }}" target="_blank" class="btn btn-sm btn-outline-secondary">Lihat</a>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge {{ $txn->status->badgeColor() }}">
                                        {{ $txn->status->label() }}
                                    </span>
                                </td>
                                <td>
                                    @if($txn->superadminVerifier)
                                        <span class="small text-success">{{ $txn->superadminVerifier->username }} (Super)</span>
                                    @elseif($txn->adminVerifier)
                                        <span class="small text-muted">{{ $txn->adminVerifier->username }} (Admin)</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">Tidak ada transaksi ditemukan.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($query->hasPages())
                <div class="card-footer d-flex align-items-center">
                    {{ $query->appends(request()->query())->links() }}
                </div>
                @endif
            </div>

        </div>

        {{-- ── TAB 2: IN-MEMORY CALCULATOR & EVENT PROMO ── --}}
        <div class="tab-pane fade {{ $activeTab === 'calculator' ? 'show active' : '' }}" id="tab-calculator">

            <div class="row g-4">

                {{-- Inputs --}}
                <div class="col-12 col-md-5 d-print-none">
                    <form method="POST" action="{{ route('superadmin.finance.calculate') }}">
                        @csrf
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header"><h3 class="card-title">Parameter Perhitungan Stok</h3></div>
                            <div class="card-body">

                                {{-- Stok Awal --}}
                                <div class="mb-3">
                                    <label class="form-label required">Jumlah Stok Produk Awal Bulan</label>
                                    <input type="number" name="stok_awal" id="stok_awal"
                                           class="form-control"
                                           value="{{ $calculations['stok_awal'] ?? 1000 }}"
                                           required min="0">
                                    <div class="form-hint">Jumlah botol yang tersedia di awal bulan berjalan.</div>
                                </div>

                                {{-- Kalender Periode Promo --}}
                                <div class="mb-3">
                                    <label class="form-label">Periode Event Promo / Bonus</label>
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <input type="date" name="promo_start" class="form-control"
                                                   value="{{ $calculations['promo_start'] ?? '' }}">
                                            <span class="form-hint">Mulai</span>
                                        </div>
                                        <div class="col-6">
                                            <input type="date" name="promo_end" class="form-control"
                                                   value="{{ $calculations['promo_end'] ?? '' }}">
                                            <span class="form-hint">Berakhir</span>
                                        </div>
                                    </div>
                                    <div class="form-hint mt-1">Transaksi approved dalam rentang tanggal ini akan ditambahkan bonus produk.</div>
                                </div>

                                {{-- Bonus New Agent --}}
                                <div class="mb-3">
                                    <label class="form-label">Botol per Registrasi Agent saat Promo</label>
                                    <div class="input-group">
                                        <button type="button" class="btn btn-outline-secondary btn-spinner" id="btn-bonus-na-minus">-</button>
                                        <input type="number" id="bonus_new_agent" name="bonus_new_agent"
                                               class="form-control text-center"
                                               value="{{ $calculations['bonus_new_agent'] ?? 10 }}">
                                        <button type="button" class="btn btn-outline-secondary btn-spinner" id="btn-bonus-na-plus">+</button>
                                    </div>
                                    <span class="form-hint mt-1">Di luar periode promo tetap 10 botol. Saat promo mengikuti input; jika lebih dari 10, selisihnya tampil sebagai bonus.</span>
                                </div>

                                {{-- Bonus Repeat Order --}}
                                <div class="mb-3">
                                    <label class="form-label">Botol per Repeat Order saat Promo</label>
                                    <div class="input-group">
                                        <button type="button" class="btn btn-outline-secondary btn-spinner" id="btn-bonus-ro-minus">-</button>
                                        <input type="number" id="bonus_repeat_order" name="bonus_repeat_order"
                                               class="form-control text-center"
                                               value="{{ $calculations['bonus_repeat_order'] ?? 10 }}">
                                        <button type="button" class="btn btn-outline-secondary btn-spinner" id="btn-bonus-ro-plus">+</button>
                                    </div>
                                    <span class="form-hint mt-1">Di luar periode promo tetap 10 botol. Saat promo mengikuti input; jika lebih dari 10, selisihnya tampil sebagai bonus.</span>
                                </div>

                                <div class="d-grid mt-4 gap-2">
                                    <button type="submit" class="btn btn-primary" id="btn-hitung">
                                        Hitung Akumulasi
                                    </button>
                                </div>

                            </div>
                        </div>
                    </form>

                    <form method="GET" action="{{ route('superadmin.finance.pdf') }}" id="pdf-form" class="mt-n2 mb-4">
                        <input type="hidden" name="stok_awal" id="pdf-stok-awal" value="{{ $calculations['stok_awal'] ?? 1000 }}">
                        <input type="hidden" name="total_keluar" id="pdf-total-keluar" value="{{ $calculations['total_keluar'] ?? 0 }}">
                        <input type="hidden" name="sisa_stok" id="pdf-sisa-stok" value="{{ $calculations['sisa_stok'] ?? 0 }}">
                        <input type="hidden" name="new_agent_count" id="pdf-new-agent-count" value="{{ $calculations['new_agent_count'] ?? 0 }}">
                        <input type="hidden" name="new_agent_promo_count" id="pdf-new-agent-promo-count" value="{{ $calculations['new_agent_promo_count'] ?? 0 }}">
                        <input type="hidden" name="new_agent_bonus_cnt" id="pdf-new-agent-bonus-cnt" value="{{ $calculations['new_agent_bonus_cnt'] ?? 0 }}">
                        <input type="hidden" name="ro_count" id="pdf-ro-count" value="{{ $calculations['ro_count'] ?? 0 }}">
                        <input type="hidden" name="ro_promo_count" id="pdf-ro-promo-count" value="{{ $calculations['ro_promo_count'] ?? 0 }}">
                        <input type="hidden" name="ro_bonus_cnt" id="pdf-ro-bonus-cnt" value="{{ $calculations['ro_bonus_cnt'] ?? 0 }}">
                        <input type="hidden" name="bonus_new_agent" id="pdf-bonus-new-agent" value="{{ $calculations['bonus_new_agent'] ?? 10 }}">
                        <input type="hidden" name="bonus_repeat_order" id="pdf-bonus-repeat-order" value="{{ $calculations['bonus_repeat_order'] ?? 10 }}">
                        <input type="hidden" name="promo_start" id="pdf-promo-start" value="{{ $calculations['promo_start'] ?? '' }}">
                        <input type="hidden" name="promo_end" id="pdf-promo-end" value="{{ $calculations['promo_end'] ?? '' }}">

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success" id="btn-save-pdf" style="{{ isset($calculations) && !empty($calculations) ? '' : 'display:none;' }}">
                                📥 Simpan & Download PDF
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Outputs / Results (Live updated via JS) --}}
                <div class="col-12 col-md-7">
                    <div class="card border-0 shadow-sm h-100 bg-white">
                        <div class="card-header d-flex align-items-center">
                            <h3 class="card-title mb-0">Hasil Akumulasi Mutasi Stok</h3>
                        </div>
                        <div class="card-body">

                            {{-- KPI Boxes --}}
                            <div class="row g-3 text-center mb-4">
                                <div class="col-4">
                                    <div class="border rounded p-3">
                                        <div class="small text-muted mb-1">Stok Awal</div>
                                        <div class="h2 fw-bold text-dark" id="preview-stok-awal">{{ number_format($calculations['stok_awal'] ?? 1000) }}</div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="border rounded p-3">
                                        <div class="small text-muted mb-1">Total Keluar</div>
                                        <div class="h2 fw-bold text-danger" id="preview-total-keluar">-{{ number_format($calculations['total_keluar'] ?? 0) }}</div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="border rounded p-3" style="background-color:rgba(74,222,128,0.1)">
                                        <div class="small text-muted mb-1">Sisa Stok</div>
                                        <div class="h2 fw-bold text-success" id="preview-sisa-stok">{{ number_format($calculations['sisa_stok'] ?? 0) }}</div>
                                    </div>
                                </div>
                            </div>

                            <h4 class="section-title text-muted mb-3">Rincian Perhitungan Transaksi Approved</h4>

                            <div class="table-responsive">
                                <table class="table table-bordered table-vcenter">
                                    <thead>
                                        <tr>
                                            <th>Tipe Mutasi</th>
                                            <th class="text-center">Total</th>
                                            <th class="text-center">Botol/Transaksi</th>
                                            <th class="text-center promo-only">Dalam Promo</th>
                                            <th class="text-center promo-only">Luar Promo</th>
                                            <th class="text-center promo-only">Botol Promo</th>
                                            <th class="text-center promo-only">Bonus</th>
                                            <th class="text-center">Total Botol</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr id="preview-na-row">
                                            <td>
                                                <strong>Registrasi Agent</strong><br>
                                                <small class="text-muted" id="preview-na-detail">
                                                    Basis: 10 botol
                                                </small>
                                            </td>
                                            <td class="text-center" id="preview-na-count">{{ $calculations['new_agent_count'] ?? 0 }}</td>
                                            <td class="text-center" id="preview-na-bottle-default">10</td>
                                            <td class="text-center promo-only" id="preview-na-promo-count">{{ $calculations['new_agent_promo_count'] ?? 0 }}</td>
                                            <td class="text-center promo-only" id="preview-na-regular-count">{{ ($calculations['new_agent_count'] ?? 0) - ($calculations['new_agent_promo_count'] ?? 0) }}</td>
                                            <td class="text-center promo-only" id="preview-na-bottle-promo">{{ $calculations['bonus_new_agent'] ?? 10 }}</td>
                                            <td class="text-center promo-only" id="preview-na-bonus">+{{ max(0, ($calculations['bonus_new_agent'] ?? 10) - 10) }}</td>
                                            <td class="text-center fw-bold" id="preview-na-total">
                                                {{ (($calculations['new_agent_count'] ?? 0) - ($calculations['new_agent_promo_count'] ?? 0)) * 10 + (($calculations['new_agent_promo_count'] ?? 0) * ($calculations['bonus_new_agent'] ?? 10)) }} botol
                                            </td>
                                        </tr>
                                        <tr id="preview-ro-row">
                                            <td>
                                                <strong>Repeat Order (RO)</strong><br>
                                                <small class="text-muted" id="preview-ro-detail">
                                                    Basis: 10 botol
                                                </small>
                                            </td>
                                            <td class="text-center" id="preview-ro-count">{{ $calculations['ro_count'] ?? 0 }}</td>
                                            <td class="text-center" id="preview-ro-bottle-default">10</td>
                                            <td class="text-center promo-only" id="preview-ro-promo-count">{{ $calculations['ro_promo_count'] ?? 0 }}</td>
                                            <td class="text-center promo-only" id="preview-ro-regular-count">{{ ($calculations['ro_count'] ?? 0) - ($calculations['ro_promo_count'] ?? 0) }}</td>
                                            <td class="text-center promo-only" id="preview-ro-bottle-promo">{{ $calculations['bonus_repeat_order'] ?? 10 }}</td>
                                            <td class="text-center promo-only" id="preview-ro-bonus">+{{ max(0, ($calculations['bonus_repeat_order'] ?? 10) - 10) }}</td>
                                            <td class="text-center fw-bold" id="preview-ro-total">
                                                {{ (($calculations['ro_count'] ?? 0) - ($calculations['ro_promo_count'] ?? 0)) * 10 + (($calculations['ro_promo_count'] ?? 0) * ($calculations['bonus_repeat_order'] ?? 10)) }} botol
                                            </td>
                                        </tr>
                                        <tr id="preview-empty-row" style="display:none;">
                                            <td colspan="8" class="text-center text-muted py-4">Belum ada transaksi approved.</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="alert alert-info small mt-3" id="preview-promo-alert">
                                <strong>Periode Promo Diterapkan:</strong>
                                <span id="preview-promo-period">Tidak ada promo aktif dalam rentang perhitungan.</span>
                            </div>

                            <div class="text-center text-muted small mt-2">
                                Dihitung secara real-time — tidak perlu reload halaman.
                            </div>

                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>

</div>

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Keep active tab state on redirect
    const activeTab = "{{ $activeTab }}";
    if (activeTab === 'calculator') {
        const trigger = document.querySelector('#calculator-tab');
        if (trigger) {
            const tab = new bootstrap.Tab(trigger);
            tab.show();
        }
    }

    // ── Spinner Buttons ────────────────────────────────────────────
    function setupSpinner(inputId, minusBtnId, plusBtnId) {
        const input   = document.getElementById(inputId);
        const btnMinus = document.getElementById(minusBtnId);
        const btnPlus  = document.getElementById(plusBtnId);
        if (!input || !btnMinus || !btnPlus) return;

        btnMinus.addEventListener('click', function() {
            let val = parseInt(input.value) || 0;
            val = Math.max(0, val - 1);
            input.value = val;
            updatePreview();
        });

        btnPlus.addEventListener('click', function() {
            const hasPromo = document.querySelector('[name=promo_start]').value && document.querySelector('[name=promo_end]').value;
            let val = parseInt(input.value) || 0;
            val = hasPromo ? val + 1 : Math.min(10, val + 1);
            input.value = val;
            updatePreview();
        });

        // Allow manual typing too
        input.addEventListener('input', function() {
            this.value = Math.max(0, parseInt(this.value) || 0);
            updatePreview();
        });
    }

    setupSpinner('bonus_new_agent',  'btn-bonus-na-minus', 'btn-bonus-na-plus');
    setupSpinner('bonus_repeat_order', 'btn-bonus-ro-minus', 'btn-bonus-ro-plus');

    // ── Live Preview (client-side) ─────────────────────────────────
    // Counts loaded from PHP on page load
    const TRANSACTIONS = @json($currentMonthTransactions);
    const COUNTS = {
        new_agent: TRANSACTIONS.filter((txn) => txn.type === 'new_agent').length,
        repeat_order: TRANSACTIONS.filter((txn) => txn.type === 'repeat_order').length,
    };

    function updatePreview() {
        const stokAwal  = parseInt(document.getElementById('stok_awal').value) || 0;
        const promoStart = document.querySelector('[name=promo_start]').value;
        const promoEnd   = document.querySelector('[name=promo_end]').value;
        const hasPromo = promoStart && promoEnd;
        const bonusNAInput = document.getElementById('bonus_new_agent');
        const bonusROInput = document.getElementById('bonus_repeat_order');
        let bonusNA = parseInt(bonusNAInput.value) || 0;
        let bonusRO = parseInt(bonusROInput.value) || 0;

        if (!hasPromo) {
            bonusNA = Math.min(10, bonusNA);
            bonusRO = Math.min(10, bonusRO);
            bonusNAInput.value = bonusNA;
            bonusROInput.value = bonusRO;
        }

        // All approved transactions from database
        const naCount = COUNTS.new_agent;
        const roCount = COUNTS.repeat_order;

        const promoTransactions = hasPromo
            ? TRANSACTIONS.filter((txn) => txn.verified_at && txn.verified_at >= promoStart && txn.verified_at <= promoEnd)
            : [];
        const naPromoCount = promoTransactions.filter((txn) => txn.type === 'new_agent').length;
        const roPromoCount = promoTransactions.filter((txn) => txn.type === 'repeat_order').length;
        const naRegularCount = naCount - naPromoCount;
        const roRegularCount = roCount - roPromoCount;
        const naBonusPerTxn = Math.max(0, bonusNA - 10);
        const roBonusPerTxn = Math.max(0, bonusRO - 10);
        const naBonusCount = naBonusPerTxn > 0 ? naPromoCount : 0;
        const roBonusCount = roBonusPerTxn > 0 ? roPromoCount : 0;
        const naTotal = (naRegularCount * 10) + (naPromoCount * bonusNA);
        const roTotal = (roRegularCount * 10) + (roPromoCount * bonusRO);

        const totalKeluar = naTotal + roTotal;
        const sisaStok    = stokAwal - totalKeluar;

        // Update preview cards
        const stokAwalEl  = document.getElementById('preview-stok-awal');
        const totalKelEl  = document.getElementById('preview-total-keluar');
        const sisaStokEl  = document.getElementById('preview-sisa-stok');

        if (stokAwalEl) stokAwalEl.textContent  = stokAwal.toLocaleString('id-ID');
        if (totalKelEl) totalKelEl.textContent   = '-' + totalKeluar.toLocaleString('id-ID');
        if (sisaStokEl) sisaStokEl.textContent   = sisaStok.toLocaleString('id-ID');

        // Update table rows
        const el = (id) => document.getElementById(id);
        if (el('preview-na-count'))  el('preview-na-count').textContent  = naCount;
        if (el('preview-na-promo-count')) el('preview-na-promo-count').textContent = naPromoCount;
        if (el('preview-na-regular-count')) el('preview-na-regular-count').textContent = naRegularCount;
        if (el('preview-na-bottle-promo')) el('preview-na-bottle-promo').textContent = bonusNA;
        if (el('preview-na-bonus')) el('preview-na-bonus').textContent = '+' + naBonusPerTxn;
        if (el('preview-na-total'))  el('preview-na-total').textContent  = naTotal + ' botol';
        if (el('preview-na-detail')) el('preview-na-detail').textContent =
            hasPromo
                ? 'Luar promo 10 botol, dalam promo ' + bonusNA + ' botol'
                : 'Tidak ada periode promo, semua transaksi 10 botol';
        if (el('preview-ro-count'))  el('preview-ro-count').textContent  = roCount;
        if (el('preview-ro-promo-count')) el('preview-ro-promo-count').textContent = roPromoCount;
        if (el('preview-ro-regular-count')) el('preview-ro-regular-count').textContent = roRegularCount;
        if (el('preview-ro-bottle-promo')) el('preview-ro-bottle-promo').textContent = bonusRO;
        if (el('preview-ro-bonus')) el('preview-ro-bonus').textContent = '+' + roBonusPerTxn;
        if (el('preview-ro-total'))  el('preview-ro-total').textContent  = roTotal + ' botol';
        if (el('preview-ro-detail')) el('preview-ro-detail').textContent =
            hasPromo
                ? 'Luar promo 10 botol, dalam promo ' + bonusRO + ' botol'
                : 'Tidak ada periode promo, semua transaksi 10 botol';
        if (el('preview-promo-period')) el('preview-promo-period').textContent = promoStart + ' s/d ' + promoEnd;
        if (el('preview-promo-alert')) el('preview-promo-alert').style.display = hasPromo ? '' : 'none';

        document.querySelectorAll('.promo-only').forEach(function(item) {
            item.style.display = hasPromo ? '' : 'none';
        });
        if (el('preview-na-row')) el('preview-na-row').style.display = naCount > 0 ? '' : 'none';
        if (el('preview-ro-row')) el('preview-ro-row').style.display = roCount > 0 ? '' : 'none';
        if (el('preview-empty-row')) el('preview-empty-row').style.display = (naCount + roCount) > 0 ? 'none' : '';
        if (el('btn-bonus-na-plus')) el('btn-bonus-na-plus').disabled = !hasPromo && bonusNA >= 10;
        if (el('btn-bonus-ro-plus')) el('btn-bonus-ro-plus').disabled = !hasPromo && bonusRO >= 10;

        if (el('pdf-stok-awal')) el('pdf-stok-awal').value = stokAwal;
        if (el('pdf-total-keluar')) el('pdf-total-keluar').value = totalKeluar;
        if (el('pdf-sisa-stok')) el('pdf-sisa-stok').value = sisaStok;
        if (el('pdf-new-agent-count')) el('pdf-new-agent-count').value = naCount;
        if (el('pdf-new-agent-promo-count')) el('pdf-new-agent-promo-count').value = naPromoCount;
        if (el('pdf-new-agent-bonus-cnt')) el('pdf-new-agent-bonus-cnt').value = naBonusCount;
        if (el('pdf-ro-count')) el('pdf-ro-count').value = roCount;
        if (el('pdf-ro-promo-count')) el('pdf-ro-promo-count').value = roPromoCount;
        if (el('pdf-ro-bonus-cnt')) el('pdf-ro-bonus-cnt').value = roBonusCount;
        if (el('pdf-bonus-new-agent')) el('pdf-bonus-new-agent').value = bonusNA;
        if (el('pdf-bonus-repeat-order')) el('pdf-bonus-repeat-order').value = bonusRO;
        if (el('pdf-promo-start')) el('pdf-promo-start').value = promoStart;
        if (el('pdf-promo-end')) el('pdf-promo-end').value = promoEnd;
    }

    // Attach live update to all inputs
    ['stok_awal','bonus_new_agent','bonus_repeat_order'].forEach(function(id) {
        const el = document.getElementById(id);
        if (el) el.addEventListener('input', updatePreview);
    });
    document.querySelectorAll('[name=promo_start],[name=promo_end]').forEach(function(el) {
        el.addEventListener('change', updatePreview);
    });

    // Initial preview update
    updatePreview();
});
</script>
@endpush
@endsection

