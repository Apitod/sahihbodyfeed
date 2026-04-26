@extends('layouts.app')

@section('title', 'Verifikasi Klaim Reward')

@section('content')
<div class="row row-cards">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Verifikasi Klaim Reward (Pencairan)</h3>
            </div>
            <div class="table-responsive">
                <table class="table card-table table-vcenter text-nowrap datatable">
                    <thead>
                        <tr>
                            <th>Klaim ID</th>
                            <th>Agen</th>
                            <th>Reward Diminta</th>
                            <th>Nilai Reward</th>
                            <th>Poin Agen Valid?</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($claims as $claim)
                        <tr>
                            <td>#{{ $claim->id }} <br> <small>{{ $claim->created_at->format('d/m/Y') }}</small></td>
                            <td>{{ $claim->agent->nama }} <br> <small>{{ $claim->agent->user->username }}</small></td>
                            <td>{{ $claim->reward->name }}</td>
                            <td>Rp {{ number_format((float)$claim->reward->reward_value, 0, ',', '.') }}</td>
                            <td>
                                @if($claim->agent->total_points >= $claim->reward->required_points)
                                    <span class="text-success"><svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg> {{ $claim->agent->total_points }} / {{ $claim->reward->required_points }}</span>
                                @else
                                    <!-- Edge case safeguard -->
                                    <span class="text-danger">TIDAK VALID ({{ $claim->agent->total_points }})</span>
                                @endif
                            </td>
                            <td>
                                @if($claim->status->value === 'pending')
                                    <span class="badge bg-yellow text-yellow-fg">{{ $claim->status->label() }}</span>
                                @elseif($claim->status->value === 'approved')
                                    <span class="badge bg-green text-green-fg">{{ $claim->status->label() }}</span>
                                @else
                                    <span class="badge bg-red text-red-fg">{{ $claim->status->label() }}</span>
                                @endif
                            </td>
                            <td>
                                @if($claim->status->value === 'pending')
                                <div class="d-flex gap-2">
                                    <form action="{{ route('admin.rewards.approve', $claim->id) }}" method="POST">
                                        @csrf
                                        <button class="btn btn-sm btn-success" onclick="return confirm('WARNING: Ini akan mengubah setelan Level Agen di web, membayarkan Reward agen ini, DAN memeriksa Kasus A/B untuk Upline-nya/Sponsor. Anda yakin sudah membayarkan Rp{{ number_format((float)$claim->reward->reward_value, 0, ',', '.') }} ke agen ini?');">Approve & Cairkan</button>
                                    </form>
                                    <form action="{{ route('admin.rewards.reject', $claim->id) }}" method="POST">
                                        @csrf
                                        <button class="btn btn-sm btn-danger" onclick="return confirm('Tolak klaim ini?');">Tolak</button>
                                    </form>
                                </div>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">Tidak ada antrian klaim reward.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer d-flex align-items-center">
                {{ $claims->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
