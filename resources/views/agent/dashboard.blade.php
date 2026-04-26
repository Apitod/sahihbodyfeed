@extends('layouts.app')

@section('title', 'Agent Dashboard')

@section('content')
<div class="row row-cards">
    <!-- Status Card -->
    <div class="col-sm-6 col-lg-3">
        <div class="card card-sm border-0 shadow-sm">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <span class="bg-primary text-white avatar shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" /><path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" /></svg>
                        </span>
                    </div>
                    <div class="col">
                        <div class="font-weight-medium">Pangkat Anda</div>
                        <div class="text-primary small font-weight-bold">{{ auth()->user()->agent->status->label() }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Points Card -->
    <div class="col-sm-6 col-lg-3">
        <div class="card card-sm border-0 shadow-sm">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <span class="bg-blue text-white avatar shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M6 5a2 2 0 0 1 2 -2h8a2 2 0 0 1 2 2v14a2 2 0 0 1 -2 2h-8a2 2 0 0 1 -2 -2v-14z" /><path d="M12 12m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" /><path d="M12 7v2" /><path d="M12 15v2" /><path d="M17 12h-2" /><path d="M9 12h-2" /></svg>
                        </span>
                    </div>
                    <div class="col">
                        <div class="font-weight-medium">Total Poin</div>
                        <div class="text-blue small font-weight-bold">{{ auth()->user()->agent->total_points }} Poin RO</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Commission Card -->
    <div class="col-sm-6 col-lg-3">
        <div class="card card-sm border-0 shadow-sm">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <span class="bg-green text-white avatar shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M17 8v-3a1 1 0 0 0 -1 -1h-10a2 2 0 0 0 0 4h12a1 1 0 0 1 1 1v3a1 1 0 0 1 -1 1h-12a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2" /><path d="M10 12h4" /></svg>
                        </span>
                    </div>
                    <div class="col">
                        <div class="font-weight-medium">Akumulasi Komisi</div>
                        <div class="text-green small font-weight-bold">Rp {{ number_format((float)$stats['total_commissions'], 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Downlines Card -->
    <div class="col-sm-6 col-lg-3">
        <div class="card card-sm border-0 shadow-sm">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <span class="bg-purple text-white avatar shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 7m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" /><path d="M5 17m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" /><path d="M12 12m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" /><path d="M7 8l4 3" /><path d="M7 16l4 -3" /></svg>
                        </span>
                    </div>
                    <div class="col">
                        <div class="font-weight-medium">Direct Downline</div>
                        <div class="text-purple small font-weight-bold">{{ $stats['total_downlines'] }} Member</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if($stats['pending_payouts'] > 0)
<div class="alert alert-important alert-warning mt-3 border-0 shadow-sm rounded-3">
    <div class="d-flex">
        <div>
            <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 9v4" /><path d="M12 17h.01" /><path d="M5 19h14a2 2 0 0 0 1.84 -2.75l-7.1 -12.25a2 2 0 0 0 -3.5 0l-7.1 12.25a2 2 0 0 0 1.75 2.75" /></svg>
        </div>
        <div>
            Ada <strong>{{ $stats['pending_payouts'] }} Matching Reward</strong> yang sedang tertahan (Pending) karena peringkat Anda belum mencukupi. Tingkatkan poin Anda untuk mencairkannya!
        </div>
    </div>
</div>
@endif

<div class="row row-cards mt-3">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent">
                <h3 class="card-title font-weight-bold">Link Referral Saya</h3>
            </div>
            <div class="card-body">
                <p class="text-secondary small mb-3">Bagikan link ini untuk mengundang agen baru ke jaringan Anda.</p>
                <div class="input-group">
                    <input type="text" class="form-control bg-light border-0" value="{{ route('register') }}?ref={{ auth()->user()->username }}" readonly id="refLink">
                    <button class="btn btn-primary" onclick="navigator.clipboard.writeText(document.getElementById('refLink').value); alert('Link disalin!')">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M8 8m0 2a2 2 0 0 1 2 -2h8a2 2 0 0 1 2 2v8a2 2 0 0 1 -2 2h-8a2 2 0 0 1 -2 -2z" /><path d="M16 8v-2a2 2 0 0 0 -2 -2h-8a2 2 0 0 0 -2 2v8a2 2 0 0 0 2 2h2" /></svg>
                        Copy
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
