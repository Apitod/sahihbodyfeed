@extends('layouts.app')

@section('title', 'Verifikasi Agent — Admin')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="align-items-center row g-2">
            <div class="col">
                <nav aria-label="breadcrumb">
                    <ol class="mb-1 breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.verifications.transactions') }}">Verifikasi Transaksi</a></li>
                        <li class="breadcrumb-item active">Verifikasi Agent</li>
                    </ol>
                </nav>
                <h2 class="page-title">Verifikasi Agent</h2>
                <p class="mt-1 text-muted small">
                    Buat transaksi registrasi agent Rp2.650.000 untuk agent yang belum terverifikasi.
                </p>
            </div>
        </div>
    </div>
</div>

<div class="container-xl">
    <div class="justify-content-center row">
        <div class="col-12 col-lg-8">
            @if($errors->any())
                <div class="mb-4 alert alert-danger alert-dismissible" role="alert">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="shadow-sm mb-4 border-0 card">
                <div class="card-header">
                    <h3 class="card-title">
                        <span class="bg-green me-2 badge">1</span>
                        Cari Agent Berdasarkan Username
                    </h3>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.verifications.agent.create') }}" class="align-items-end row g-2">
                        <div class="col">
                            <label class="form-label required" for="username-search">Username Agent</label>
                            <input
                                type="text"
                                id="username-search"
                                name="username"
                                class="form-control"
                                value="{{ $username ?? '' }}"
                                placeholder="Ketik username agent…"
                                autocomplete="off"
                                autofocus
                            >
                        </div>
                        <div class="col-auto">
                            <button type="submit" class="btn btn-success">Cari</button>
                        </div>
                    </form>

                    @if($username)
                        @if($agent)
                            <div class="d-flex align-items-center gap-3 mt-3 mb-0 alert {{ $hasApprovedVerification || $hasPendingVerification ? 'alert-warning' : 'alert-success' }}" role="alert">
                                <span class="bg-green-lt rounded-circle text-green avatar avatar-sm fw-bold">
                                    {{ strtoupper(substr($agent->nama, 0, 2)) }}
                                </span>
                                <div>
                                    <div class="fw-semibold">{{ $agent->nama }}</div>
                                    <div class="text-muted small">
                                        @username: <strong>{{ $agent->user->username }}</strong>
                                        &nbsp;·&nbsp;
                                        Status Verifikasi:
                                        @if($hasApprovedVerification)
                                            <span class="bg-green-lt text-green badge">Sudah Verifikasi Agent</span>
                                        @elseif($hasPendingVerification)
                                            <span class="bg-azure-lt text-azure badge">Sedang Proses Verifikasi</span>
                                        @else
                                            <span class="bg-yellow-lt text-yellow badge">Belum Verifikasi Agent</span>
                                        @endif

                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="mt-3 mb-0 alert alert-warning" role="alert">
                                Agent dengan username <strong>"{{ $username }}"</strong> tidak ditemukan.
                            </div>
                        @endif
                    @endif
                </div>
            </div>

            @if($agent && ! $hasApprovedVerification && ! $hasPendingVerification)
            <div class="shadow-sm border-0 card">
                <div class="card-header">
                    <h3 class="card-title">
                        <span class="bg-green me-2 badge">2</span>
                        Detail Verifikasi Agent
                    </h3>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.verifications.agent.store') }}" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="username" value="{{ $agent->user->username }}">

                        <div class="mb-4 row g-3">
                            <div class="col-6 col-md-4">
                                <div class="mb-1 text-muted small">Nama Agent</div>
                                <div class="fw-semibold">{{ $agent->nama }}</div>
                            </div>
                            <div class="col-6 col-md-4">
                                <div class="mb-1 text-muted small">Username</div>
                                <code>{{ $agent->user->username }}</code>
                            </div>
                            <div class="col-6 col-md-4">
                                <div class="mb-1 text-muted small">Nominal</div>
                                <div class="text-green fw-bold fs-5" id="nominal-label">{{ old('payment_option', 'bayar') === 'free' ? 'Free' : 'Rp' . number_format($amount, 0, ',', '.') }}</div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label required">Opsi Verifikasi</label>
                            <div class="form-selectgroup">
                                <label class="form-selectgroup-item">
                                    <input type="radio" name="payment_option" value="bayar" class="form-selectgroup-input" onchange="syncAgentPaymentOption()" @checked(old('payment_option', 'bayar') === 'bayar')>
                                    <span class="form-selectgroup-label">Bayar</span>
                                </label>
                                <label class="form-selectgroup-item">
                                    <input type="radio" name="payment_option" value="free" class="form-selectgroup-input" onchange="syncAgentPaymentOption()" onclick="syncAgentPaymentOption()" @checked(old('payment_option') === 'free')>
                                    <span class="form-selectgroup-label">Free</span>
                                </label>
                            </div>
                            @error('payment_option')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4 {{ old('payment_option', 'bayar') === 'free' ? 'd-none' : '' }}" id="proof-wrapper">
                            <label class="form-label required" for="proof-upload">Bukti Pembayaran</label>
                            <input
                                type="file"
                                id="proof-upload"
                                name="proof_of_payment"
                                class="form-control @error('proof_of_payment') is-invalid @enderror"
                                accept="image/jpeg,image/png,image/webp,application/pdf"
                                @if(old('payment_option', 'bayar') !== 'free') required @endif
                            >
                            @error('proof_of_payment')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @else
                                <div class="form-hint" id="proof-hint">Wajib untuk opsi Bayar. JPG, PNG, WEBP, atau PDF — Maks. 5 MB.</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="px-4 btn btn-success">
                                Buat Verifikasi Agent
                            </button>
                            <a href="{{ route('admin.verifications.transactions') }}" class="btn-outline-secondary btn">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<script>
    function syncAgentPaymentOption() {
        const selected = document.querySelector('input[name="payment_option"]:checked')?.value;
        const proofWrapper = document.getElementById('proof-wrapper');
        const proofUpload = document.getElementById('proof-upload');
        const nominalLabel = document.getElementById('nominal-label');

        if (!proofWrapper || !proofUpload || !nominalLabel) return;

        if (selected === 'free') {
            proofWrapper.style.display = 'none';
            proofUpload.disabled = true;
            proofUpload.required = false;
            proofUpload.value = '';
            nominalLabel.textContent = 'Free';
            return;
        }

        proofWrapper.style.display = '';
        proofUpload.disabled = false;
        proofUpload.required = true;
        nominalLabel.textContent = 'Rp{{ number_format($amount, 0, ',', '.') }}';
    }

    syncAgentPaymentOption();
</script>
@endsection
