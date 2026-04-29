@extends('layouts.app')

@section('title', 'Komisi Transaksi')

@section('content')
<div class="row row-cards">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Komisi Transaksi Downline</h3>
                <div class="card-actions">
                    <form action="{{ route('agent.commissions') }}" method="GET" id="filterForm">
                        <select name="generation_level" class="form-select form-select-sm" onchange="document.getElementById('filterForm').submit()">
                            <option value="">Semua Level</option>
                            <option value="1" {{ request('generation_level') == '1' ? 'selected' : '' }}>Generasi 1</option>
                            <option value="2" {{ request('generation_level') == '2' ? 'selected' : '' }}>Generasi 2</option>
                            <option value="3" {{ request('generation_level') == '3' ? 'selected' : '' }}>Generasi 3</option>
                        </select>
                    </form>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table card-table table-vcenter text-nowrap datatable">
                    <thead>
                        <tr>
                            <th>ID Transaksi</th>
                            <th>Tanggal</th>
                            <th>Tipe Komisi</th>
                            <th>Level Generasi</th>
                            <th>Nominal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($commissions as $comm)
                        <tr>
                            <td><span class="text-muted">#{{ $comm->transaction_id }}</span></td>
                            <td>{{ $comm->created_at->format('d/m/Y H:i') }}</td>
                            <td>{{ $comm->type->label() }}</td>
                            <td>Gen-{{ $comm->generation_level }}</td>
                            <td class="text-success">Rp {{ number_format((float)$comm->amount, 0, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center">Belum ada komisi transaksi.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer d-flex align-items-center">
                {{ $commissions->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
