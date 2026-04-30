@extends('layouts.app')

@section('title', 'Laporan Komisi')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">
                    Laporan Komisi Agen
                </h2>
                <div class="text-muted mt-1">Kelola dan cairkan komisi agen yang berstatus pending.</div>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="d-flex gap-2">
                    @if($countPending > 0)
                        <a href="{{ route('admin.commissions.pdf') }}" class="btn btn-primary" target="_blank">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 3v4a1 1 0 0 0 1 1h4" /><path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" /><path d="M12 17v-6" /><path d="M9.5 14.5l2.5 2.5l2.5 -2.5" /></svg>
                            Download PDF ({{ $countPending }})
                        </a>
                        <form action="{{ route('admin.commissions.mark-paid') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin sudah mentransfer semua komisi pending? Tindakan ini akan mengubah status komisi menjadi Paid.');">
                            @csrf
                            <button type="submit" class="btn btn-success">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>
                                Tandai Paid
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.commissions.index') }}" class="row g-2 align-items-end">
            <div class="col-12 col-md-4">
                <label class="form-label">Status Komisi</label>
                <select name="status" class="form-select" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    @foreach($statuses as $s)
                        <option value="{{ $s->value }}" @selected($status == $s->value)>{{ $s->label() }}</option>
                    @endforeach
                </select>
            </div>
            @if($status === 'pending')
            <div class="col-12 col-md-8 text-end">
                <div class="text-muted small">Total Komisi Pending Saat Ini</div>
                <div class="h2 mb-0 text-primary">Rp {{ number_format($totalPending) }}</div>
            </div>
            @endif
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-vcenter card-table table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Penerima</th>
                    <th>Nominal</th>
                    <th>Tipe</th>
                    <th>Gen</th>
                    <th>Status</th>
                    <th>Tanggal Dibuat</th>
                </tr>
            </thead>
            <tbody>
                @forelse($commissions as $comm)
                <tr>
                    <td class="text-muted small">#{{ $comm->id }}</td>
                    <td>
                        <div class="fw-semibold">{{ $comm->recipient->nama }}</div>
                        <div class="text-muted small">{{ $comm->recipient->bank_name ?? 'Bank N/A' }} - {{ $comm->recipient->bank_account ?? 'Rek N/A' }}</div>
                    </td>
                    <td class="fw-bold">Rp {{ number_format($comm->amount) }}</td>
                    <td class="text-muted small">
                        {{ match($comm->type->value) {
                            'downline_registration' => 'Registrasi Agen',
                            'repeat_order' => 'Repeat Order',
                            default => $comm->type->value
                        } }}
                    </td>
                    <td class="text-muted">Gen-{{ $comm->generation_level }}</td>
                    <td>
                        @php
                            $badgeColor = match($comm->status->value) {
                                'menunggu' => 'bg-secondary',
                                'pending'  => 'bg-warning',
                                'paid'     => 'bg-success',
                                default    => 'bg-secondary',
                            };
                        @endphp
                        <span class="badge {{ $badgeColor }}">{{ $comm->status->label() }}</span>
                    </td>
                    <td class="text-muted small">{{ $comm->created_at->format('d M Y H:i') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">Tidak ada data komisi.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($commissions->hasPages())
    <div class="card-footer">
        {{ $commissions->links() }}
    </div>
    @endif
</div>
@endsection
