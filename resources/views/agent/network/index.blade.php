@extends('layouts.app')

@section('title', 'Hierarki Jaringan')

@section('content')

<div class="page-header d-print-none mb-4">
    <div class="row align-items-center">
        <div class="col">
            <h2 class="page-title text-primary fw-bold">Hierarki Jaringan</h2>
            <div class="text-muted small mt-1">Klik pada card agen untuk melihat jaringan nya.</div>
        </div>
        <div class="col-auto">
            <span class="badge bg-blue-lt px-3 py-2 rounded-pill fs-6">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="me-1"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
                {{ $totalTeam }} Total Tim
            </span>
        </div>
    </div>
</div>

{{-- ─── Tree Container ────────────────────────────────────────────────────── --}}
<div class="card border-0 shadow-sm overflow-hidden">
<div class="card border-0 shadow-sm overflow-hidden">
    <div class="card-header bg-white py-3">
        <div class="d-flex align-items-center w-100 flex-wrap gap-3">
            <div class="d-flex align-items-center gap-2">
                <div class="legend-dot" style="background:#F0A04B;"></div><span class="small text-muted">Anda</span>
                <div class="legend-dot ms-3" style="background:#B1C29E;"></div><span class="small text-muted">Gen 1</span>
                <div class="legend-dot ms-3" style="background:#FADA7A;"></div><span class="small text-muted">Gen 2</span>
                <div class="legend-dot ms-3" style="background:#FCE7C8; border: 1px solid #e2e8f0;"></div><span class="small text-muted">Gen 3</span>
            </div>
            
            <div class="ms-auto d-flex align-items-center gap-3">
                <div class="text-muted small d-none d-md-block">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1">
                        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/>
                        <line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                    Klik card untuk expand/collapse
                </div>
            </div>
        </div>
    </div>

    <div class="card-body p-4" style="background: #f8fafc; overflow-x: auto;">
        <div class="network-tree-wrapper">
            @include('agent.network._node', ['node' => $tree, 'isRoot' => true])
        </div>
    </div>
</div>

{{-- Modal Detail Removed --}}

{{-- ─── Styles ────────────────────────────────────────────────────────────── --}}
<style>
/* ── Layout ─────────────────────────────────────────────────────────────── */
.network-tree-wrapper {
    display: inline-block;
    min-width: 100%;
    padding: 2rem 1rem;
}

/* ── Tree node ───────────────────────────────────────────────────────────── */
.tree-node {
    display: flex;
    flex-direction: row;
    align-items: flex-start;
    position: relative;
}

/* ── Item (card + connector) ─────────────────────────────────────────────── */
.tree-item {
    display: flex;
    align-items: center;
    position: relative;
    flex-shrink: 0;
    z-index: 2;
}

/* ── Horizontal connector FROM parent card TO vertical rail ─────────────── */
.tree-children {
    display: flex;
    flex-direction: column;
    position: relative;
    padding-left: 48px; /* space for the horizontal arm */
}

/* Vertical rail that connects all siblings */
.tree-children-inner {
    display: flex;
    flex-direction: column;
    gap: 16px;
    position: relative;
}

/* Smooth vertical rail */
.tree-children-inner::before {
    content: '';
    position: absolute;
    left: -24px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #cbd5e1;
    z-index: 1;
}

/* Fix vertical rail top/bottom overshoot */
.tree-children-inner > .tree-node:first-child::before {
    content: '';
    position: absolute;
    left: -24px;
    top: 0;
    height: 50%;
    width: 2px;
    background: #f8fafc; /* Match background to hide upper half of rail */
    z-index: 2;
}

.tree-children-inner > .tree-node:last-child::before {
    content: '';
    position: absolute;
    left: -24px;
    bottom: 0;
    height: calc(50% - 1px);
    width: 2px;
    background: #f8fafc; /* Match background to hide lower half of rail */
    z-index: 2;
}

/* Horizontal arms from the vertical rail to each child card */
.tree-children-inner > .tree-node::after {
    content: '';
    position: absolute;
    left: -24px;
    top: 50%;
    width: 24px;
    height: 2px;
    background: #cbd5e1;
    z-index: 1;
}

/* ── Agent card ──────────────────────────────────────────────────────────── */
.agent-node-card {
    width: 230px;
    background: #ffffff;
    border-radius: 12px;
    border: 1.5px solid #e2e8f0;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    overflow: hidden;
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    cursor: pointer;
    position: relative;
    flex-shrink: 0;
}

.agent-node-card:hover {
    border-color: var(--accent);
    box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1);
    transform: translateY(-3px);
}

/* Left accent bar */
.node-accent-bar {
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 4px;
    background: var(--accent);
}

/* Card content row */
.node-content {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 14px 12px 14px 16px;
}

/* Avatar */
.node-avatar {
    width: 38px;
    height: 38px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    font-weight: 700;
    font-size: 0.85rem;
}

/* Info */
.node-info {
    flex: 1;
    min-width: 0;
}

.node-name {
    font-weight: 700;
    font-size: 0.85rem;
    color: #1e293b;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    line-height: 1.2;
}

.node-meta {
    display: flex;
    align-items: center;
    gap: 6px;
    margin-top: 4px;
}

.node-status-badge {
    font-size: 0.6rem;
    font-weight: 700;
    padding: 1px 6px;
    border-radius: 4px;
    text-transform: uppercase;
    letter-spacing: 0.02em;
}

.node-gen-label {
    font-size: 0.65rem;
    color: #94a3b8;
    font-weight: 600;
}

/* Toggle chevron */
.node-toggle {
    color: #cbd5e1;
    transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.node-toggle--open {
    transform: rotate(90deg);
    color: var(--accent);
}

/* Footer downline count */
.node-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 8px 12px 8px 16px;
    background: #fcfdfe;
    border-top: 1px solid #f1f5f9;
    font-size: 0.7rem;
    color: #64748b;
    font-weight: 500;
}

.node-count-badge {
    color: #fff;
    font-size: 0.65rem;
    font-weight: 800;
    padding: 1px 8px;
    border-radius: 20px;
}

/* ── Root node special styling ───────────────────────────────────────────── */
.tree-root > .tree-item > .agent-node-card {
    width: 250px;
    border-width: 2px;
    border-color: #3b82f633;
}

/* ── Horizontal arm FROM parent card TO children ───────────────────────────── */
/* Horizontal connector line between parent card edge and vertical rail */
.tree-item::after {
    content: '';
    display: block;
    width: 24px;
    height: 2px;
    background: #cbd5e1;
    flex-shrink: 0;
}

.tree-item:not(.has-children)::after {
    display: none;
}

/* ── Alpine.js transition classes ────────────────────────────────────────── */
.tree-enter {
    transition: all 0.3s ease-out;
}
.tree-enter-start {
    opacity: 0;
    transform: translateX(-15px);
}
.tree-enter-end {
    opacity: 1;
    transform: translateX(0);
}
.tree-leave {
    transition: all 0.2s ease-in;
}
.tree-leave-start {
    opacity: 1;
    transform: translateX(0);
}
.tree-leave-end {
    opacity: 0;
    transform: translateX(-15px);
}

/* ── Legend dot ──────────────────────────────────────────────────────────── */
.legend-dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
}
</style>

@endsection

@section('scripts')
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
@endsection
