@extends('layouts.app')

@section('title', 'Matching Rewards Log')

@section('content')
<div class="row row-cards">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Matching Reward Saya (sebagai Sponsor)</h3>
            </div>
            <div class="card-body">
                <p class="text-muted">Ini adalah log pencairan reward sebesar 100% dari reward yang diklaim oleh downline langsung (Generasi 1) Anda. Sesuai sistem, komisi matching akan <strong>PENDING</strong> (tertahan) jika Anda sendiri belum berada di level/mengklaim reward yang sama.</p>
            </div>
            <div class="table-responsive">
                <table class="table card-table table-vcenter text-nowrap datatable">
                    <thead>
                        <tr>
                            <th>Downline</th>
                            <th>Reward Terklaim</th>
                            <th>Waktu Klaim</th>
                            <th>Nominal Matching</th>
                            <th>Status Pencairan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($matchingLogs as $log)
                        <tr>
                            <td>{{ $log->downline->nama }} <br> <small class="text-muted">{{ $log->downline->user->username }}</small></td>
                            <td>{{ $log->reward->name }}</td>
                            <td>{{ $log->created_at->format('d/m/Y H:i') }}</td>
                            <td>Rp {{ number_format((float)$log->amount, 0, ',', '.') }}</td>
                            <td>
                                @if($log->status->value === 'pending')
                                    <span class="badge bg-yellow text-yellow-fg">Tertahan (Pending)</span>
                                @else
                                    <span class="badge bg-green text-green-fg">Cair</span> <br>
                                    <small>{{ $log->paid_at->format('d/m/Y H:i') }}</small>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center">Belum ada log matching reward.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer d-flex align-items-center">
                {{ $matchingLogs->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
