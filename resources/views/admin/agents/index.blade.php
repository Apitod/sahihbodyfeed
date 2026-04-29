@extends('layouts.app')

@section('title', 'Manajemen Agen')

@section('content')
{{-- ── Page Header ──────────────────────────────────────────────────────── --}}
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">Manajemen Agen</h2>
                <div class="text-muted small mt-1">Kelola seluruh data agen, status, dan jaringan referral.</div>
            </div>
            <div class="col-auto ms-auto">
                <a href="{{ route('admin.agents.create') }}" class="btn btn-primary" id="btn-create-agent">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="24" height="24" viewBox="0 0 24 24"
                        stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M12 5l0 14"/>
                        <path d="M5 12l14 0"/>
                    </svg>
                    Tambah Agen
                </a>
            </div>
        </div>
    </div>
</div>

{{-- ── Filters ──────────────────────────────────────────────────────────── --}}
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.agents.index') }}" class="row g-2 align-items-end" id="filter-form">
            <div class="col-12 col-md-5">
                <label class="form-label text-muted small mb-1">Cari Agen</label>
                <input type="text" name="search" id="search-input" class="form-control" placeholder="Nama, username, atau kode referral…"
                    value="{{ request('search') }}">
            </div>
            <div class="col-12 col-md-3">
                <label class="form-label text-muted small mb-1">Filter Status</label>
                <select name="status" id="filter-status" class="form-select">
                    <option value="">Semua Status</option>
                    @foreach($statuses as $s)
                        <option value="{{ $s->value }}" @selected(request('status') === $s->value)>{{ $s->label() }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary" id="btn-filter">Filter</button>
                <a href="{{ route('admin.agents.index') }}" class="btn btn-outline-secondary ms-1">Reset</a>
            </div>
        </form>
    </div>
</div>

{{-- ── Agent Table ──────────────────────────────────────────────────────── --}}
<div class="card border-0 shadow-sm">
    <div class="card-header d-flex align-items-center py-3">
        <h3 class="card-title mb-0">Daftar Agen</h3>
        <span class="badge bg-blue-lt ms-2">{{ $agents->total() }} total</span>
    </div>
    <div class="table-responsive">
        <table class="table table-vcenter table-hover card-table" id="agents-table">
            <thead>
                <tr>
                    <th class="text-muted small">#</th>
                    <th class="text-muted small">Nama / Username</th>
                    <th class="text-muted small">Kode Referral</th>
                    <th class="text-muted small">Upline</th>
                    <th class="text-muted small">Status</th>
                    <th class="text-muted small">Pangkat</th>
                    <th class="text-muted small text-center">Downline</th>
                    <th class="text-muted small">Bergabung</th>
                    <th class="text-muted small text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($agents as $agent)
                <tr id="agent-row-{{ $agent->id }}">
                    <td class="text-muted small">{{ $agent->id }}</td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <span class="avatar avatar-sm rounded-circle bg-blue-lt text-blue fw-bold">
                                {{ strtoupper(substr($agent->nama, 0, 2)) }}
                            </span>
                            <div>
                                <div class="fw-semibold">{{ $agent->nama }}</div>
                                <div class="text-muted small">{{ $agent->user?->username ?? '—' }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <code class="text-secondary small">{{ $agent->referral_code }}</code>
                    </td>
                    <td class="text-secondary small">
                        {{ $agent->upline?->nama ?? '<span class="text-muted fst-italic">—</span>' }}
                    </td>

                    {{-- Account active / suspended badge --}}
                    <td>
                        @if($agent->user?->is_active)
                            <span class="badge bg-green-lt text-green">Aktif</span>
                        @else
                            <span class="badge bg-red-lt text-red">Tidak Aktif</span>
                        @endif
                    </td>

                    {{-- AgentStatus level (Agen / Supervisor / Asisten Manager / Manager) --}}
                    <td>
                        @php
                            $levelColor = match($agent->status) {
                                \App\Enums\AgentStatus::Manager    => 'bg-yellow-lt text-yellow',
                                \App\Enums\AgentStatus::AssManager => 'bg-purple-lt text-purple',
                                \App\Enums\AgentStatus::Supervisor => 'bg-blue-lt text-blue',
                                default                            => 'bg-secondary-lt text-secondary',
                            };
                        @endphp
                        <span class="badge {{ $levelColor }}">{{ $agent->status->label() }}</span>
                    </td>

                    <td class="text-center text-muted small">{{ $agent->downlines_count }}</td>

                    <td class="text-muted small">
                        {{ $agent->joined_at?->format('d M Y') ?? '—' }}
                    </td>

                    <td class="text-end">
                        <div class="d-flex gap-1 justify-content-end flex-wrap">
                            <a href="{{ route('admin.agents.show', $agent) }}"
                                class="btn btn-sm btn-ghost-primary" title="Detail">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm" width="16" height="16"
                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M12 12m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"/>
                                    <path d="M22 12c-2.667 4.667 -6 7 -10 7s-7.333 -2.333 -10 -7c2.667 -4.667 6 -7 10 -7s7.333 2.333 10 7"/>
                                </svg>
                            </a>
                            <a href="{{ route('admin.agents.edit', $agent) }}"
                                class="btn btn-sm btn-ghost-secondary" title="Edit">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm" width="16" height="16"
                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1"/>
                                    <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z"/>
                                </svg>
                            </a>

                            {{-- Approve / Suspend toggle --}}
                            @if(! $agent->user?->is_active)
                                <form method="POST" action="{{ route('admin.agents.approve', $agent) }}"
                                    id="approve-form-{{ $agent->id }}">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-ghost-success" title="Aktifkan">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm" width="16" height="16"
                                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M5 12l5 5l10 -10"/>
                                        </svg>
                                    </button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('admin.agents.suspend', $agent) }}"
                                    id="suspend-form-{{ $agent->id }}"
                                    onsubmit="return confirm('Yakin ingin mensuspend agen ini?')">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-ghost-warning" title="Suspend">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm" width="16" height="16"
                                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M9 4h1a1 1 0 0 1 1 1v14a1 1 0 0 1 -1 1h-1a1 1 0 0 1 -1 -1v-14a1 1 0 0 1 1 -1z"/>
                                            <path d="M14 4h1a1 1 0 0 1 1 1v14a1 1 0 0 1 -1 1h-1a1 1 0 0 1 -1 -1v-14a1 1 0 0 1 1 -1z"/>
                                        </svg>
                                    </button>
                                </form>
                            @endif

                            <form method="POST" action="{{ route('admin.agents.destroy', $agent) }}"
                                id="delete-form-{{ $agent->id }}"
                                onsubmit="return confirm('Hapus agen ini secara permanen? Tindakan tidak dapat dibatalkan.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-ghost-danger" title="Hapus">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm" width="16" height="16"
                                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M4 7l16 0"/>
                                        <path d="M10 11l0 6"/>
                                        <path d="M14 11l0 6"/>
                                        <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"/>
                                        <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center text-muted py-4">
                        Tidak ada agen yang ditemukan.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($agents->hasPages())
    <div class="card-footer d-flex align-items-center">
        <p class="m-0 text-muted small">
            Menampilkan <strong>{{ $agents->firstItem() }}–{{ $agents->lastItem() }}</strong>
            dari <strong>{{ $agents->total() }}</strong> agen
        </p>
        <div class="ms-auto">
            {{ $agents->links() }}
        </div>
    </div>
    @endif
</div>
@endsection
