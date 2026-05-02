@extends('layouts.app')

@section('title', 'Manajemen Agen')

@push('styles')
<style>
/* ── Hierarchy Tree ─────────────────────────────────────────────────── */
.agent-tree { list-style: none; padding: 0; margin: 0; }
.agent-tree .agent-tree { padding-left: 1.75rem; border-left: 2px solid var(--tblr-border-color); margin-left: .9rem; }

.tree-node { position: relative; }
.tree-node + .tree-node { margin-top: .35rem; }

.tree-item {
    display: flex;
    align-items: center;
    gap: .6rem;
    padding: .45rem .75rem;
    border-radius: .4rem;
    cursor: pointer;
    user-select: none;
    transition: background .15s;
}
.tree-item:hover { background: var(--tblr-gray-100); }

/* Toggle chevron */
.tree-toggle {
    flex-shrink: 0;
    width: 1.25rem;
    height: 1.25rem;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--tblr-muted);
    transition: transform .2s;
}
.tree-toggle.open { transform: rotate(90deg); }
.tree-toggle.leaf { visibility: hidden; }

/* Avatar */
.tree-avatar {
    flex-shrink: 0;
    width: 2rem;
    height: 2rem;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: .72rem;
    font-weight: 700;
    color: #fff;
}

.tree-info { flex: 1; min-width: 0; }
.tree-name  { font-weight: 600; font-size: .875rem; }
.tree-meta  { font-size: .75rem; color: var(--tblr-muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

.tree-children { display: none; margin-top: .35rem; }
.tree-children.open { display: block; }

/* Generation label pill */
.gen-pill {
    display: inline-flex;
    align-items: center;
    padding: .15rem .55rem;
    border-radius: 99px;
    font-size: .72rem;
    font-weight: 600;
    white-space: nowrap;
    flex-shrink: 0;
}
.gen-0 { background: rgba(32,107,196,.12); color: #206bc4; }
.gen-1 { background: rgba(43,174,102,.12); color: #2ba766; }
.gen-2 { background: rgba(164,110,9,.12);  color: #a46e09; }
.gen-3 { background: rgba(114,46,209,.12); color: #7230d1; }
</style>
@endpush

@section('content')
{{-- ── Page Header ──────────────────────────────────────────────────────── --}}
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">Manajemen Agen</h2>
                <div class="text-muted small mt-1 mb-4">Kelola seluruh data agen, status, dan jaringan referral.</div>
            </div>
            <div class="col-auto ms-auto">
                <a href="{{ route('admin.agents.create') }}" class="btn btn-primary" id="btn-create-agent">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="24" height="24" viewBox="0 0 24 24"
                        stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M12 5l0 14"/><path d="M5 12l14 0"/>
                    </svg>
                    Tambah Agen
                </a>
            </div>
        </div>
    </div>
</div>

{{-- ── Flash Messages ────────────────────────────────────────────────────── --}}
@if(session('success'))
<div class="alert alert-success alert-dismissible" role="alert">
    <div class="d-flex align-items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon text-success" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10"/></svg>
        <div>{{ session('success') }}</div>
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif
@if(session('error'))
<div class="alert alert-danger alert-dismissible" role="alert">
    {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- ── Tabs ──────────────────────────────────────────────────────────────── --}}
<ul class="nav nav-tabs mb-3" id="agent-tabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="tab-list-btn" data-bs-toggle="tab" data-bs-target="#tab-list"
            type="button" role="tab" aria-controls="tab-list" aria-selected="true">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="18" height="18" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 6l11 0"/><path d="M9 12l11 0"/><path d="M9 18l11 0"/><path d="M5 6l0 .01"/><path d="M5 12l0 .01"/><path d="M5 18l0 .01"/></svg>
            Daftar Agen
        </button>
    </li>
</ul>

<div class="tab-content">

{{-- ═══════════════════════════════════════════════════════════════════════ --}}
{{-- TAB 1: DAFTAR AGEN (paginated table)                                   --}}
{{-- ═══════════════════════════════════════════════════════════════════════ --}}
<div class="tab-pane fade show active" id="tab-list" role="tabpanel">

    {{-- Filters --}}
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.agents.index') }}" class="row g-2 align-items-end" id="filter-form">
                <div class="col-12 col-md-5">
                    <label class="form-label text-muted small mb-1">Cari Agen</label>
                    <input type="text" name="search" id="search-input" class="form-control"
                        placeholder="Nama atau username…" value="{{ request('search') }}">
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

    {{-- Table --}}
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
                        <td class="text-secondary small">
                            {!! $agent->upline?->nama ?? '<span class="text-muted fst-italic">—</span>' !!}
                        </td>
                        <td>
                            @if($agent->user?->is_active)
                                <span class="badge bg-green-lt text-green">Aktif</span>
                            @else
                                <span class="badge bg-red-lt text-red">Tidak Aktif</span>
                            @endif
                        </td>
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
                        <td class="text-muted small">{{ $agent->joined_at?->format('d M Y') ?? '—' }}</td>
                        <td class="text-end">
                            <div class="dropdown d-md-none">
                                <button class="btn btn-sm dropdown-toggle" data-bs-toggle="dropdown">Aksi</button>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <a href="{{ route('admin.agents.show', $agent) }}" class="dropdown-item">Detail</a>
                                    <a href="{{ route('admin.agents.edit', $agent) }}" class="dropdown-item">Edit</a>
                                    @if(! $agent->user?->is_active)
                                        <form method="POST" action="{{ route('admin.agents.approve', $agent) }}">
                                            @csrf
                                            <button type="submit" class="dropdown-item text-success">Aktifkan</button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('admin.agents.suspend', $agent) }}" onsubmit="return confirm('Yakin ingin mensuspend agen ini?')">
                                            @csrf
                                            <button type="submit" class="dropdown-item text-warning">Suspend</button>
                                        </form>
                                    @endif
                                    <div class="dropdown-divider"></div>
                                    <form method="POST" action="{{ route('admin.agents.destroy', $agent) }}" onsubmit="return confirm('Hapus agen ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="dropdown-item text-danger">Hapus</button>
                                    </form>
                                </div>
                            </div>
                            <div class="d-none d-md-flex gap-1 justify-content-end">
                                <a href="{{ route('admin.agents.show', $agent) }}"
                                    class="btn btn-sm btn-ghost-primary" title="Detail">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"/><path d="M22 12c-2.667 4.667 -6 7 -10 7s-7.333 -2.333 -10 -7c2.667 -4.667 6 -7 10 -7s7.333 2.333 10 7"/></svg>
                                </a>
                                <a href="{{ route('admin.agents.edit', $agent) }}"
                                    class="btn btn-sm btn-ghost-secondary" title="Edit">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1"/><path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z"/></svg>
                                </a>
                                @if(! $agent->user?->is_active)
                                    <form method="POST" action="{{ route('admin.agents.approve', $agent) }}"
                                        id="approve-form-{{ $agent->id }}">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-ghost-success" title="Aktifkan">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10"/></svg>
                                        </button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('admin.agents.suspend', $agent) }}"
                                        id="suspend-form-{{ $agent->id }}"
                                        onsubmit="return confirm('Yakin ingin mensuspend agen ini?')">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-ghost-warning" title="Suspend">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 4h1a1 1 0 0 1 1 1v14a1 1 0 0 1 -1 1h-1a1 1 0 0 1 -1 -1v-14a1 1 0 0 1 1 -1z"/><path d="M14 4h1a1 1 0 0 1 1 1v14a1 1 0 0 1 -1 1h-1a1 1 0 0 1 -1 -1v-14a1 1 0 0 1 1 -1z"/></svg>
                                        </button>
                                    </form>
                                @endif
                                <form method="POST" action="{{ route('admin.agents.destroy', $agent) }}"
                                    id="delete-form-{{ $agent->id }}"
                                    onsubmit="return confirm('Hapus agen ini secara permanen?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-ghost-danger" title="Hapus">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7l16 0"/><path d="M10 11l0 6"/><path d="M14 11l0 6"/><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"/><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">Tidak ada agen yang ditemukan.</td>
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
            <div class="ms-auto">{{ $agents->links() }}</div>
        </div>
        @endif
    </div>

</div>{{-- /tab-list --}}

</div>{{-- /tab-content --}}
@endsection
