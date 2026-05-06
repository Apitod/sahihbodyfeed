@extends('layouts.app')
@section('title', 'Approval Final Klaim Reward')

@section('content')
<div class="page-header d-print-none mb-4">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle text-uppercase fw-bold" style="color:var(--brand-4);letter-spacing:.1em;">Superadmin</div>
                <h2 class="page-title fw-black">Approval Final Klaim Reward</h2>
                <div class="text-muted small">Klaim reward yang sudah diverifikasi Admin dan menunggu persetujuan final Anda.</div>
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
        <a class="nav-link {{ !request('status') || request('status') === 'pending_superadmin' ? 'active fw-bold' : '' }}"
           href="{{ route('superadmin.verifications.rewards', ['status' => 'pending_superadmin']) }}">
            ⏳ Menunggu Approval
            @if(isset($pendingCount) && $pendingCount > 0)
                <span class="badge bg-danger ms-1">{{ $pendingCount }}</span>
            @endif
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request('status') === 'pending' ? 'active fw-bold' : '' }}"
           href="{{ route('superadmin.verifications.rewards', ['status' => 'pending']) }}">
            🕐 Menunggu Admin
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request('status') === 'approved' ? 'active fw-bold' : '' }}"
           href="{{ route('superadmin.verifications.rewards', ['status' => 'approved']) }}">
            ✅ Disetujui
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request('status') === 'rejected' ? 'active fw-bold' : '' }}"
           href="{{ route('superadmin.verifications.rewards', ['status' => 'rejected']) }}">
            ❌ Ditolak
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
                    <th class="text-muted small">Reward</th>
                    <th class="text-muted small">Nilai</th>
                    <th class="text-muted small">Poin Agen</th>
                    <th class="text-muted small">Status</th>
                    <th class="text-muted small">Review Admin</th>
                    <th class="text-muted small text-end d-print-none">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($claims as $claim)
                <tr>
                    <td>
                        <div class="text-muted small fw-bold">#{{ $claim->id }}</div>
                        <div class="text-muted" style="font-size:.7rem;">{{ $claim->created_at->format('d M Y') }}</div>
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <span class="avatar avatar-sm rounded-circle" style="background:var(--brand-1);color:var(--brand-4);font-weight:800;">
                                {{ strtoupper(substr($claim->agent->nama ?? '?', 0, 2)) }}
                            </span>
                            <div>
                                <div class="fw-semibold small">{{ $claim->agent->nama }}</div>
                                <div class="text-muted" style="font-size:.7rem;">{{ $claim->agent->user->username }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="fw-semibold small">{{ $claim->reward->name }}</div>
                        <div class="text-muted" style="font-size:.7rem;">{{ number_format($claim->reward->required_points, 0, ',', '.') }} poin</div>
                    </td>
                    <td class="fw-bold small">Rp {{ number_format((float)$claim->reward->reward_value, 0, ',', '.') }}</td>
                    <td>
                        @if($claim->agent->total_points >= $claim->reward->required_points)
                            <span class="badge bg-success-lt text-success">
                                ✓ {{ number_format($claim->agent->total_points, 0, ',', '.') }}
                            </span>
                        @else
                            <span class="badge bg-danger-lt text-danger">
                                ✗ {{ number_format($claim->agent->total_points, 0, ',', '.') }}
                            </span>
                        @endif
                    </td>
                    <td>
                        <span class="badge {{ $claim->status->badgeColor() }} rounded-pill px-3">
                            {{ $claim->status->label() }}
                        </span>
                    </td>
                    <td class="small text-muted">
                        {{ $claim->adminVerifier?->username ?? '—' }}
                    </td>
                    <td class="text-end d-print-none">
                        @if($claim->status->value === 'pending_superadmin')
                        <div class="d-flex gap-1 justify-content-end">
                            @php $rewardValue = 'Rp' . number_format((float)$claim->reward->reward_value, 0, ',', '.'); @endphp
                            <form action="{{ route('superadmin.rewards.approve', $claim) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success rounded-pill px-3"
                                    onclick="return confirm('Setujui klaim reward ini? Reward akan dicairkan senilai {{ $rewardValue }}.')"
                                    style="font-size:.72rem;">
                                    ✓ Setujui &amp; Cairkan
                                </button>
                            </form>
                            <form action="{{ route('superadmin.rewards.reject', $claim) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-danger rounded-pill px-3"
                                    onclick="return confirm('Tolak klaim reward ini?')"
                                    style="font-size:.72rem;">
                                    ✗ Tolak
                                </button>
                            </form>
                        </div>
                        @elseif($claim->status->value === 'approved')
                            <span class="small text-success fw-bold">✓ Disetujui</span>
                            @if($claim->superadminApprover)
                                <div style="font-size:.68rem;" class="text-muted">{{ $claim->superadminApprover->username }}</div>
                            @endif
                        @elseif($claim->status->value === 'rejected')
                            <span class="small text-danger fw-bold">✗ Ditolak</span>
                        @else
                            <span class="small text-muted">Menunggu Admin</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-5 text-muted">
                        <div class="fs-3 mb-2">🏆</div>
                        <div class="fw-bold">Tidak ada klaim reward dengan status ini.</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($claims->hasPages())
    <div class="card-footer d-flex align-items-center">
        <p class="m-0 text-muted small">
            Menampilkan <strong>{{ $claims->firstItem() }}–{{ $claims->lastItem() }}</strong>
            dari <strong>{{ $claims->total() }}</strong> klaim
        </p>
        <div class="ms-auto">{{ $claims->links() }}</div>
    </div>
    @endif
</div>

</div>
@endsection
