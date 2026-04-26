@extends('layouts.app')

@section('title', 'Klaim Reward')

@section('content')
<div class="row row-cards">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Progress & Klaim Reward</h3>
            </div>
            <div class="card-body">
                <p>Total Poin Anda Saat Ini: <strong class="text-primary" style="font-size: 1.5rem;">{{ $agent->total_points }} Poin</strong></p>
            </div>
            <div class="table-responsive">
                <table class="table card-table table-vcenter text-nowrap datatable">
                    <thead>
                        <tr>
                            <th>Level Reward</th>
                            <th>Syarat Poin</th>
                            <th>Nilai Reward</th>
                            <th>Status Klaim</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rewards as $reward)
                        <tr>
                            <td>{{ $reward->name }}</td>
                            <td>{{ $reward->required_points }} Poin</td>
                            <td>Rp {{ number_format((float)$reward->reward_value, 0, ',', '.') }}</td>
                            <td>
                                @if(isset($claims[$reward->id]))
                                    @if($claims[$reward->id]->status->value === 'pending')
                                        <span class="badge bg-yellow text-yellow-fg">Menunggu Verifikasi</span>
                                    @elseif($claims[$reward->id]->status->value === 'approved')
                                        <span class="badge bg-green text-green-fg">Terklaim</span>
                                    @else
                                        <span class="badge bg-red text-red-fg">Ditolak</span>
                                    @endif
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if(isset($claims[$reward->id]) && in_array($claims[$reward->id]->status->value, ['pending', 'approved']))
                                    <button class="btn btn-sm btn-secondary" disabled>Sudah Diajukan</button>
                                @else
                                    <form action="{{ route('agent.rewards.claim', $reward->id) }}" method="POST">
                                        @csrf
                                        @if($agent->total_points >= $reward->required_points)
                                            <button class="btn btn-sm btn-primary" onclick="return confirm('Ajukan klaim reward ini sekarang?');">Klaim Sekarang</button>
                                        @else
                                            <button class="btn btn-sm btn-outline-secondary" disabled title="Poin belum mencukupi">Poin Kurang</button>
                                        @endif
                                    </form>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
