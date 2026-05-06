@extends('layouts.app')
@section('title', 'Approval Final Transaksi')

@section('content')
<div class="page-header d-print-none mb-4">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle text-uppercase fw-bold" style="color:var(--brand-4);letter-spacing:.1em;">Superadmin</div>
                <h2 class="page-title fw-black">Approval Final Transaksi</h2>
                <div class="text-muted small">Transaksi yang sudah diverifikasi Admin dan menunggu persetujuan final Anda.</div>
            </div>
            <div class="col-auto ms-auto">
                <a href="{{ route('superadmin.dashboard') }}" class="btn btn-outline-secondary rounded-pill">← Dashboard</a>
            </div>
        </div>
    </div>
</div>

<div class="container-xl">

{{-- Status Tabs --}}
<ul class="nav nav-tabs mb-4">
    <li class="nav-item">
        <a class="nav-link {{ request('status','pending_superadmin') === 'pending_superadmin' ? 'active fw-bold' : '' }}"
           href="{{ route('superadmin.verifications.transactions', ['status' => 'pending_superadmin']) }}">
            ⏳ Menunggu Approval
            @if($pendingCount > 0)
                <span class="badge bg-danger ms-1">{{ $pendingCount }}</span>
            @endif
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request('status') === 'approved' ? 'active fw-bold' : '' }}"
           href="{{ route('superadmin.verifications.transactions', ['status' => 'approved']) }}">
            ✅ Disetujui
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request('status') === 'rejected' ? 'active fw-bold' : '' }}"
           href="{{ route('superadmin.verifications.transactions', ['status' => 'rejected']) }}">
            ❌ Ditolak
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request('status') === 'pending' ? 'active fw-bold' : '' }}"
           href="{{ route('superadmin.verifications.transactions', ['status' => 'pending']) }}">
            🕐 Menunggu Admin
        </a>
    </li>
</ul>

<div class="card shadow-sm" style="border-radius:16px;">
    <div class="table-responsive">
        <table class="table table-vcenter table-hover card-table mb-0">
            <thead>
                <tr>
                    <th class="text-muted small">ID / Waktu</th>
                    <th class="text-muted small">Agen</th>
                    <th class="text-muted small">Tipe</th>
                    <th class="text-muted small">Nominal</th>
                    <th class="text-muted small">Bukti</th>
                    <th class="text-muted small">Status</th>
                    <th class="text-muted small">Review Admin</th>
                    <th class="text-muted small text-end d-print-none">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $txn)
                <tr>
                    <td>
                        <div class="text-muted small fw-bold">#{{ $txn->id }}</div>
                        <div class="text-muted" style="font-size:.7rem;">{{ $txn->created_at->format('d M Y H:i') }}</div>
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <span class="avatar avatar-sm rounded-circle" style="background:var(--brand-1);color:var(--brand-4);font-weight:800;">
                                {{ strtoupper(substr($txn->agent->nama ?? '?', 0, 2)) }}
                            </span>
                            <div>
                                <div class="fw-semibold small">{{ $txn->agent->nama ?? '-' }}</div>
                                <div class="text-muted" style="font-size:.7rem;">{{ $txn->agent->user->username ?? '-' }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="badge {{ $txn->type->value === 'new_agent' ? 'bg-purple-lt text-purple' : 'bg-blue-lt text-blue' }}">
                            {{ $txn->type->label() }}
                        </span>
                    </td>
                    <td class="fw-bold">Rp {{ number_format($txn->amount, 0, ',', '.') }}</td>
                    <td>
                        @if($txn->proof_of_payment)
                            <a href="{{ asset('storage/' . $txn->proof_of_payment) }}" target="_blank"
                               class="btn btn-sm btn-outline-secondary rounded-pill" style="font-size:.72rem;">
                                📎 Lihat
                            </a>
                        @else
                            <span class="text-muted small">—</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge {{ $txn->status->badgeColor() }} rounded-pill px-3">
                            {{ $txn->status->label() }}
                        </span>
                    </td>
                    <td class="small text-muted">
                        {{ $txn->adminVerifier?->username ?? '—' }}
                        @if($txn->updated_at && $txn->status->value === 'pending_superadmin')
                            <div style="font-size:.68rem;">{{ $txn->updated_at->diffForHumans() }}</div>
                        @endif
                    </td>
                    <td class="text-end d-print-none">
                        @if($txn->status->value === 'pending_superadmin')
                        <div class="d-flex gap-1 justify-content-end">
                            <form action="{{ route('superadmin.transactions.approve', $txn) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success rounded-pill px-3"
                                    onclick="return confirm('Setujui transaksi ini? Komisi akan langsung didistribusikan.')"
                                    style="font-size:.72rem;">
                                    ✓ Setujui
                                </button>
                            </form>
                            <form action="{{ route('superadmin.transactions.reject', $txn) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-danger rounded-pill px-3"
                                    onclick="return confirm('Tolak transaksi ini?')"
                                    style="font-size:.72rem;">
                                    ✗ Tolak
                                </button>
                            </form>
                        </div>
                        @elseif($txn->status->value === 'approved')
                            <span class="small text-success fw-bold">✓ Disetujui</span>
                            @if($txn->superadminVerifier)
                                <div style="font-size:.68rem;" class="text-muted">{{ $txn->superadminVerifier->username }}</div>
                            @endif
                        @elseif($txn->status->value === 'rejected')
                            <span class="small text-danger fw-bold">✗ Ditolak</span>
                        @else
                            <span class="small text-muted">Menunggu Admin</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-5 text-muted">
                        <div class="fs-3 mb-2">📭</div>
                        <div class="fw-bold">Tidak ada transaksi dengan status ini.</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($transactions->hasPages())
    <div class="card-footer d-flex align-items-center">
        <p class="m-0 text-muted small">
            Menampilkan <strong>{{ $transactions->firstItem() }}–{{ $transactions->lastItem() }}</strong>
            dari <strong>{{ $transactions->total() }}</strong> transaksi
        </p>
        <div class="ms-auto">{{ $transactions->links() }}</div>
    </div>
    @endif
</div>

</div>
@endsection
