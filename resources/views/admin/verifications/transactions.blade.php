@extends('layouts.app')

@section('title', 'Verifikasi Transaksi')

@section('content')
<div class="row row-cards">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Verifikasi Transaksi Agen</h3>
            </div>
            <div class="table-responsive">
                <table class="table card-table table-vcenter text-nowrap datatable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Agen</th>
                            <th>Tipe</th>
                            <th>Nominal</th>
                            <th>Bukti Bayar</th>
                            <th>Status</th>
                            <th>Verifikator</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $txn)
                        <tr>
                            <td><span class="text-muted">#{{ $txn->id }}</span><br><small>{{ $txn->created_at->format('d/m/Y H:i') }}</small></td>
                            <td>
                                <div>{{ $txn->agent->nama }}</div>
                                <div class="text-muted small">User: {{ $txn->agent->user->username ?? '-' }}</div>
                            </td>
                            <td>
                                @if($txn->type->value === 'new_agent')
                                    <span class="badge bg-purple-lt">{{ $txn->type->label() }}</span>
                                @else
                                    <span class="badge bg-blue-lt">{{ $txn->type->label() }}</span>
                                @endif
                            </td>
                            <td>Rp {{ number_format((float)$txn->amount, 0, ',', '.') }}</td>
                            <td>
                                @if($txn->proof_of_payment)
                                    <a href="{{ asset('storage/' . $txn->proof_of_payment) }}" target="_blank" class="btn btn-sm btn-outline-secondary">Lihat Bukti</a>
                                @else
                                    <span class="text-muted">Tidak ada</span>
                                @endif
                            </td>
                            <td>
                                @if($txn->status->value === 'pending')
                                    <span class="badge bg-yellow text-yellow-fg">{{ $txn->status->label() }}</span>
                                @elseif($txn->status->value === 'verified')
                                    <span class="badge bg-green text-green-fg">{{ $txn->status->label() }}</span>
                                @else
                                    <span class="badge bg-red text-red-fg">{{ $txn->status->label() }}</span>
                                @endif
                            </td>
                            <td>{{ $txn->verifier->username ?? '-' }}<br><small>{{ $txn->verified_at ? $txn->verified_at->format('d/m H:i') : '' }}</small></td>
                            <td>
                                @if($txn->status->value === 'pending')
                                <div class="d-flex gap-2">
                                    <form action="{{ route('admin.transactions.approve', $txn->id) }}" method="POST">
                                        @csrf
                                        <button class="btn btn-sm btn-success" onclick="return confirm('Peringatan: Konfirmasi pembayaran valid? Ini akan memicu pembagian komisi/poin.');">Valid</button>
                                    </form>
                                    <form action="{{ route('admin.transactions.reject', $txn->id) }}" method="POST">
                                        @csrf
                                        <button class="btn btn-sm btn-danger" onclick="return confirm('Tolak transaksi ini?');">Tolak</button>
                                    </form>
                                </div>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center">Tidak ada data transaksi.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer d-flex align-items-center">
                {{ $transactions->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
