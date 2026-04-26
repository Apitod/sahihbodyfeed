@extends('layouts.app')

@section('title', 'Riwayat Transaksi')

@section('content')
<div class="row row-cards">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Riwayat Transaksi Saya</h3>
            </div>
            <div class="table-responsive">
                <table class="table card-table table-vcenter text-nowrap datatable">
                    <thead>
                        <tr>
                            <th>Tipe Transaksi</th>
                            <th>Tanggal</th>
                            <th>Nominal</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $txn)
                        <tr>
                            <td>
                                @if($txn->type->value === 'new_agent')
                                    <span class="badge bg-purple-lt">{{ $txn->type->label() }}</span>
                                @else
                                    <span class="badge bg-blue-lt">{{ $txn->type->label() }}</span>
                                @endif
                            </td>
                            <td>{{ $txn->created_at->format('d/m/Y H:i') }}</td>
                            <td>Rp {{ number_format((float)$txn->amount, 0, ',', '.') }}</td>
                            <td>
                                @if($txn->status->value === 'pending')
                                    <span class="badge bg-yellow text-yellow-fg">{{ $txn->status->label() }}</span>
                                @elseif($txn->status->value === 'verified')
                                    <span class="badge bg-green text-green-fg">{{ $txn->status->label() }}</span>
                                @else
                                    <span class="badge bg-red text-red-fg">{{ $txn->status->label() }}</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center">Belum ada transaksi.</td>
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
