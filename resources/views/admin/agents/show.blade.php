@extends('layouts.app')

@section('title', 'Detail Agen — ' . $agent->nama)

@section('content')
    {{-- ── Breadcrumb / Header ───────────────────────────────────────────────- --}}
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col mb-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-1">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.agents.index') }}">Agen</a></li>
                            <li class="breadcrumb-item active">{{ $agent->nama }}</li>
                        </ol>
                    </nav>
                    <h2 class="page-title">Profil Agen</h2>
                </div>
                <div class="col-auto ms-auto d-flex gap-2 flex-wrap">
                    <a href="{{ route('admin.agents.edit', $agent) }}" class="btn btn-outline-primary" id="btn-edit-agent">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="24" height="24" viewBox="0 0 24 24"
                            stroke-width="2" stroke="currentColor" fill="none">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                            <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                        </svg>
                        Edit
                    </a>

                    {{-- Approve / Suspend action --}}
                    @if(!$agent->user?->is_active)
                        <form method="POST" action="{{ route('admin.agents.approve', $agent) }}" id="form-approve-agent">
                            @csrf
                            <button type="submit" class="btn btn-success" id="btn-approve-agent">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="24" height="24"
                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M5 12l5 5l10 -10" />
                                </svg>
                                Aktifkan
                            </button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('admin.agents.suspend', $agent) }}" id="form-suspend-agent"
                            onsubmit="return confirm('Yakin ingin mensuspend {{ $agent->nama }}?')">
                            @csrf
                            <button type="submit" class="btn btn-warning" id="btn-suspend-agent">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="24" height="24"
                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M9 4h1a1 1 0 0 1 1 1v14a1 1 0 0 1 -1 1h-1a1 1 0 0 1 -1 -1v-14a1 1 0 0 1 1 -1z" />
                                    <path d="M14 4h1a1 1 0 0 1 1 1v14a1 1 0 0 1 -1 1h-1a1 1 0 0 1 -1 -1v-14a1 1 0 0 1 1 -1z" />
                                </svg>
                                Suspend
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ── Profile Card + Stats ─────────────────────────────────────────────── --}}
    <div class="row row-cards mb-3">
        {{-- Left: Profile --}}
        <div class="col-12 col-lg-4">
            <div class="card border-0 shadow-sm h-100" id="agent-profile-card">
                <div class="card-body text-center pt-4">
                    <span class="avatar avatar-xl rounded-circle bg-blue-lt text-blue fw-bold mb-3"
                        style="font-size:1.6rem;">
                        {{ strtoupper(substr($agent->nama, 0, 2)) }}
                    </span>
                    <h3 class="mb-0">{{ $agent->nama }}</h3>
                    <div class="text-muted small mb-2">{{ $agent->user?->username ?? '—' }}</div>

                    @php
                        $levelColor = match ($agent->status) {
                            \App\Enums\AgentStatus::Manager => 'bg-yellow text-yellow-fg',
                            \App\Enums\AgentStatus::AssManager => 'bg-purple text-white',
                            \App\Enums\AgentStatus::Supervisor => 'bg-blue text-white',
                            default => 'bg-secondary text-white',
                        };
                    @endphp
                    <span class="badge {{ $levelColor }} mb-3">{{ $agent->status->label() }}</span>

                    <div class="d-flex justify-content-center mb-3">
                        @if($agent->user?->is_active)
                            <span class="badge bg-green-lt text-green px-3 py-2">
                                <span class="status-dot status-dot-animated bg-green me-1"></span> Aktif
                            </span>
                        @else
                            <span class="badge bg-red-lt text-red px-3 py-2">
                                <span class="status-dot bg-red me-1"></span> Tidak Aktif / Suspended
                            </span>
                        @endif
                    </div>

                    <div class="hr-text">Info</div>
                    <dl class="row text-start g-1 small mt-2">

                        <dt class="col-6 text-muted">Telepon</dt>
                        <dd class="col-6">{{ $agent->no_telp ?? $agent->phone ?? '—' }}</dd>

                        <dt class="col-6 text-muted">Alamat</dt>
                        <dd class="col-6">{{ $agent->alamat ?? '—' }}</dd>

                        <dt class="col-6 text-muted">Upline</dt>
                        <dd class="col-6">
                            @if($agent->upline)
                                <a href="{{ route('admin.agents.show', $agent->upline) }}" class="text-decoration-none">
                                    {{ $agent->upline->nama }}
                                </a>
                            @else
                                <span class="text-muted fst-italic">Tidak ada</span>
                            @endif
                        </dd>

                        <dt class="col-6 text-muted">Total Poin</dt>
                        <dd class="col-6 fw-semibold">{{ number_format($agent->total_points) }}</dd>

                        <dt class="col-6 text-muted">Bergabung</dt>
                        <dd class="col-6">{{ $agent->joined_at?->format('d M Y') ?? '—' }}</dd>
                    </dl>

                    <div class="hr-text mt-3 mb-2">Data Rekening</div>
                    <dl class="row text-start g-1 small">
                        <dt class="col-6 text-muted">Bank</dt>
                        <dd class="col-6">{{ $agent->bank_name ?? '—' }}</dd>

                        <dt class="col-6 text-muted">No. Rekening</dt>
                        <dd class="col-6">{{ $agent->bank_account ?? '—' }}</dd>

                        <dt class="col-6 text-muted">Atas Nama</dt>
                        <dd class="col-6">{{ $agent->bank_account_name ?? '—' }}</dd>
                    </dl>

                    @if($agent->foto_ktp)
                        <div class="hr-text mt-3 mb-2">Dokumen</div>
                        <div class="text-start">
                            <div class="text-muted small mb-1">Foto KTP</div>
                            <a href="{{ asset('storage/' . $agent->foto_ktp) }}" target="_blank">
                                <img src="{{ asset('storage/' . $agent->foto_ktp) }}" alt="KTP {{ $agent->nama }}" class="img-fluid rounded border" style="max-height: 120px; object-fit: cover;">
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Right: KPI mini-cards --}}
        <div class="col-12 col-lg-8">
            <div class="row row-cards h-100">
                <div class="col-6 col-xl-3">
                    <div class="card border-0 shadow-sm text-center py-3">
                        <div class="text-muted small mb-1">Total Poin</div>
                        <div class="fs-2 fw-bold text-blue">{{ number_format($agent->total_points) }}</div>
                    </div>
                </div>
                <div class="col-6 col-xl-3">
                    <div class="card border-0 shadow-sm text-center py-3">
                        <div class="text-muted small mb-1">Direct Downline</div>
                        <div class="fs-2 fw-bold text-purple">{{ $downlines->total() }}</div>
                    </div>
                </div>
                <div class="col-6 col-xl-3">
                    <div class="card border-0 shadow-sm text-center py-3">
                        <div class="text-muted small mb-1">Total Komisi</div>
                        <div class="fs-4 fw-bold text-green">
                            Rp{{ number_format((float) $commissionTotal, 0, ',', '.') }}
                        </div>
                    </div>
                </div>
                <div class="col-6 col-xl-3">
                    <div class="card border-0 shadow-sm text-center py-3">
                        <div class="text-muted small mb-1">Transaksi</div>
                        <div class="fs-2 fw-bold text-secondary">{{ $agent->transactions()->count() }}</div>
                    </div>
                </div>

                {{-- Rank progression bar --}}
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted small">Pangkat Saat Ini</span>
                                <span class="fw-semibold small">{{ $agent->status->label() }}</span>
                            </div>
                            @php
                                $nextStatus = match ($agent->status) {
                                    \App\Enums\AgentStatus::Agent => \App\Enums\AgentStatus::Supervisor,
                                    \App\Enums\AgentStatus::Supervisor => \App\Enums\AgentStatus::AssManager,
                                    \App\Enums\AgentStatus::AssManager => \App\Enums\AgentStatus::Manager,
                                    \App\Enums\AgentStatus::Manager => null,
                                };
                                $currentMin = $agent->status->requiredPoints();
                                $nextMin = $nextStatus?->requiredPoints() ?? $currentMin;
                                $progress = $nextStatus
                                    ? min(100, (int) (($agent->total_points - $currentMin) / max(1, $nextMin - $currentMin) * 100))
                                    : 100;
                            @endphp
                            <div class="progress progress-sm mb-1">
                                <div class="progress-bar bg-blue" style="width: {{ $progress }}%" role="progressbar"></div>
                            </div>
                            <div class="d-flex justify-content-between text-muted" style="font-size:.72rem">
                                <span>{{ number_format($agent->total_points) }} poin</span>
                                @if($nextStatus)
                                    <span>Target: {{ number_format($nextStatus->requiredPoints()) }} poin
                                        ({{ $nextStatus->label() }})</span>
                                @else
                                    <span class="text-green fw-semibold">Pangkat Tertinggi</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Downline Table ───────────────────────────────────────────────────── --}}
    <div class="card border-0 shadow-sm mb-3" id="downline-section">
        <div class="card-header py-3 d-flex align-items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon text-purple me-2" width="24" height="24" viewBox="0 0 24 24"
                stroke-width="2" stroke="currentColor" fill="none">
                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                <path d="M5 7m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" />
                <path d="M5 17m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" />
                <path d="M12 12m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" />
                <path d="M7 8l4 3" />
                <path d="M7 16l4 -3" />
            </svg>
            <h3 class="card-title mb-0">Jaringan Downline Langsung</h3>
            <span class="badge bg-purple-lt ms-2">{{ $downlines->total() }}</span>
        </div>
        <div class="table-responsive">
            <table class="table table-vcenter table-hover card-table" id="downline-table">
                <thead>
                    <tr>
                        <th class="text-muted small">Nama</th>
                        <th class="text-muted small">Pangkat</th>
                        <th class="text-muted small">Status</th>
                        <th class="text-muted small text-center">Sub-Downline</th>
                        <th class="text-muted small">Bergabung</th>
                        <th class="text-muted small text-end">Detail</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($downlines as $dl)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="avatar avatar-sm rounded-circle bg-purple-lt text-purple fw-bold">
                                        {{ strtoupper(substr($dl->nama, 0, 2)) }}
                                    </span>
                                    <div>
                                        <div class="fw-semibold">{{ $dl->nama }}</div>
                                        <div class="text-muted small">{{ $dl->user?->username ?? '—' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-secondary-lt text-secondary">{{ $dl->status->label() }}</span>
                            </td>
                            <td>
                                @if($dl->user?->is_active)
                                    <span class="badge bg-green-lt text-green">Aktif</span>
                                @else
                                    <span class="badge bg-red-lt text-red">Tidak Aktif</span>
                                @endif
                            </td>
                            <td class="text-center text-muted small">{{ $dl->downlines_count }}</td>
                            <td class="text-muted small">{{ $dl->joined_at?->format('d M Y') ?? '—' }}</td>
                            <td class="text-end">
                                <a href="{{ route('admin.agents.show', $dl) }}" class="btn btn-sm btn-ghost-primary">
                                    Buka
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">Belum ada downline terdaftar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($downlines->hasPages())
            <div class="card-footer d-flex align-items-center">
                <p class="m-0 text-muted small">
                    Menampilkan {{ $downlines->firstItem() }}–{{ $downlines->lastItem() }}
                    dari {{ $downlines->total() }}
                </p>
                <div class="ms-auto">
                    {{ $downlines->links() }}
                </div>
            </div>
        @endif
    </div>

    {{-- ── Commission History Table ─────────────────────────────────────────── --}}
    <div class="card border-0 shadow-sm" id="commission-section">
        <div class="card-header py-3 d-flex align-items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon text-green me-2" width="24" height="24" viewBox="0 0 24 24"
                stroke-width="2" stroke="currentColor" fill="none">
                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                <path
                    d="M17 8v-3a1 1 0 0 0 -1 -1h-10a2 2 0 0 0 0 4h12a1 1 0 0 1 1 1v3a1 1 0 0 1 -1 1h-12a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2" />
                <path d="M10 12h4" />
            </svg>
            <h3 class="card-title mb-0">Riwayat Komisi</h3>
            <span class="badge bg-green-lt ms-2">{{ $commissions->total() }}</span>
            <div class="ms-auto text-muted small">
                Total: <strong class="text-green">Rp{{ number_format((float) $commissionTotal, 0, ',', '.') }}</strong>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-vcenter table-hover card-table" id="commission-table">
                <thead>
                    <tr>
                        <th class="text-muted small">#</th>
                        <th class="text-muted small">Tipe Komisi</th>
                        <th class="text-muted small">Generasi</th>
                        <th class="text-muted small text-end">Jumlah</th>
                        <th class="text-muted small">Status</th>
                        <th class="text-muted small">Transaksi</th>
                        <th class="text-muted small">Dibayar</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($commissions as $commission)
                        <tr>
                            <td class="text-muted small">{{ $commission->id }}</td>
                            <td>
                                <span class="badge bg-blue-lt text-blue">{{ $commission->type->label() }}</span>
                            </td>
                            <td class="text-center">
                                <span
                                    class="badge bg-secondary-lt text-secondary">Gen-{{ $commission->generation_level }}</span>
                            </td>
                            <td class="text-end fw-semibold text-green">
                                Rp{{ number_format((float) $commission->amount, 0, ',', '.') }}
                            </td>
                            <td>
                                @if($commission->status === \App\Enums\CommissionStatus::Paid)
                                    <span class="badge bg-green-lt text-green">Dibayar</span>
                                @else
                                    <span class="badge bg-yellow-lt text-yellow">Pending</span>
                                @endif
                            </td>
                            <td class="text-muted small">
                                #{{ $commission->transaction_id }}
                            </td>
                            <td class="text-muted small">
                                {{ $commission->paid_at?->format('d M Y') ?? '—' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">Belum ada riwayat komisi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($commissions->hasPages())
            <div class="card-footer d-flex align-items-center">
                <p class="m-0 text-muted small">
                    Menampilkan {{ $commissions->firstItem() }}–{{ $commissions->lastItem() }}
                    dari {{ $commissions->total() }}
                </p>
                <div class="ms-auto">
                    {{ $commissions->links() }}
                </div>
            </div>
        @endif
    </div>
@endsection