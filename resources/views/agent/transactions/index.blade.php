@extends('layouts.app')

@section('title', 'Riwayat Transaksi')

@section('content')
{{-- ── Page Header ──────────────────────────────────────────────────── --}}
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">Riwayat Transaksi Saya</h2>
            </div>
        </div>
    </div>
</div>

<div class="container-xl">

    {{-- ── Status Filter Tabs ──────────────────────────────────────────── --}}
    <div class="card mb-3">
        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs">
                @php
                    $tabs = [
                        'all'               => 'Semua',
                        'pending'           => 'Menunggu Admin',
                        'pending_superadmin'=> 'Menunggu Superadmin',
                        'approved'          => 'Disetujui',
                        'rejected'          => 'Ditolak',
                    ];
                @endphp
                @foreach($tabs as $value => $label)
                    <li class="nav-item">
                        <a class="nav-link {{ $statusFilter === $value ? 'active' : '' }}"
                           href="{{ route('agent.transactions.index', $value !== 'all' ? ['status' => $value] : []) }}">
                            {{ $label }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>

        {{-- ── Table ───────────────────────────────────────────────────────── --}}
        <div class="table-responsive">
            <table class="table card-table table-vcenter text-nowrap">
                <thead>
                    <tr>
                        <th>Tipe Transaksi</th>
                        <th>Tanggal</th>
                        <th>Nominal</th>
                        <th>Bukti Bayar</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $txn)
                    <tr>
                        {{-- Tipe --}}
                        <td>
                            <span class="badge {{ $txn->type->value === 'new_agent' ? 'bg-purple-lt' : 'bg-blue-lt' }}">
                                {{ $txn->type->label() }}
                            </span>
                        </td>

                        {{-- Tanggal --}}
                        <td>{{ $txn->created_at->format('d/m/Y H:i') }}</td>

                        {{-- Nominal --}}
                        <td>Rp {{ number_format((float) $txn->amount, 0, ',', '.') }}</td>

                        {{-- Bukti Bayar --}}
                        <td>
                            @if($txn->proof_of_payment)
                                <a href="{{ asset('storage/' . $txn->proof_of_payment) }}"
                                   target="_blank"
                                   class="btn btn-sm btn-outline-secondary">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm me-1" width="16" height="16"
                                         viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M14 3v4a1 1 0 0 0 1 1h4"/>
                                        <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z"/>
                                        <path d="M9 9l1 0"/><path d="M9 13l6 0"/><path d="M9 17l6 0"/>
                                    </svg>
                                    Lihat Bukti
                                </a>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>

                        {{-- Status --}}
                        <td>
                            <span class="badge {{ $txn->status->badgeColor() }}">
                                {{ $txn->status->label() }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">
                            Belum ada transaksi
                            @if($statusFilter !== 'all')
                                dengan status <strong>{{ $tabs[$statusFilter] ?? $statusFilter }}</strong>
                            @endif
                            .
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($transactions->hasPages())
        <div class="card-footer d-flex align-items-center">
            {{ $transactions->appends(request()->query())->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
