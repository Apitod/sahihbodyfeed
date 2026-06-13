@extends('layouts.app')

@section('title', 'Buat Transaksi Repeat Order — Admin')

@section('content')
{{-- ── Page Header ──────────────────────────────────────────────────────── --}}
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="align-items-center row g-2">
            <div class="col">
                <nav aria-label="breadcrumb">
                    <ol class="mb-1 breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.verifications.transactions') }}">Verifikasi Transaksi</a></li>
                        <li class="breadcrumb-item active">Buat RO</li>
                    </ol>
                </nav>
                <h2 class="page-title">Buat Transaksi Repeat Order</h2>
                <p class="mt-1 text-muted small">
                    Admin membuat transaksi RO atas nama agen. Cari agen berdasarkan <strong>username</strong>.
                </p>
            </div>
        </div>
    </div>
</div>

<div class="container-xl">
    <div class="justify-content-center row">
        <div class="col-12 col-lg-8">

            {{-- ── Flash / Validation Errors ───────────────────────────── --}}
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

            {{-- ── STEP 1: Cari Agen ───────────────────────────────────── --}}
            <div class="shadow-sm mb-4 border-0 card" id="step-search">
                <div class="card-header">
                    <h3 class="card-title">
                        <span class="bg-blue me-2 badge">1</span>
                        Cari Agen Berdasarkan Username
                    </h3>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.verifications.ro.create') }}" class="align-items-end row g-2" id="search-agent-form">
                        <div class="col">
                            <label class="form-label required" for="username-search">Username Agen</label>
                            <input
                                type="text"
                                id="username-search"
                                name="username"
                                class="form-control"
                                value="{{ $username ?? '' }}"
                                placeholder="Ketik username agen…"
                                autocomplete="off"
                                autofocus
                            >
                        </div>
                        <div class="col-auto">
                            <button type="submit" class="btn btn-primary" id="btn-cari-agen">
                                <svg xmlns="http://www.w3.org/2000/svg" class="me-1 icon" width="18" height="18" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="10" cy="10" r="7"/><path d="M21 21l-6 -6"/></svg>
                                Cari
                            </button>
                        </div>
                    </form>

                    {{-- Agent preview card --}}
                    @if($username)
                        @if($agent)
                            <div class="d-flex align-items-center gap-3 mt-3 mb-0 alert alert-success" id="agent-found-banner" role="alert">
                                <span class="bg-green-lt rounded-circle text-green avatar avatar-sm fw-bold">
                                    {{ strtoupper(substr($agent->nama, 0, 2)) }}
                                </span>
                                <div>
                                    <div class="fw-semibold">{{ $agent->nama }}</div>
                                    <div class="text-muted small">
                                        @username: <strong>{{ $agent->user->username }}</strong>
                                        &nbsp;·&nbsp;
                                        Status:
                                        @if(! $agent->user?->is_active)
                                            <span class="bg-red-lt text-red badge">Tidak Aktif</span>
                                        @elseif($hasApprovedVerification)
                                            <span class="bg-green-lt text-green badge">Aktif</span>
                                        @else
                                            <span class="bg-yellow-lt text-yellow badge">Belum Verifikasi Agent</span>
                                        @endif
                                        &nbsp;·&nbsp;
                                        Pangkat: <span class="bg-blue-lt text-blue badge">{{ $agent->status->label() }}</span>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="mt-3 mb-0 alert alert-warning" role="alert">
                                <svg xmlns="http://www.w3.org/2000/svg" class="me-1 icon" width="18" height="18" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 9v4"/><path d="M10.363 3.591l-8.106 13.534a1.914 1.914 0 0 0 1.636 2.871h16.214a1.914 1.914 0 0 0 1.636 -2.87l-8.106 -13.536a1.914 1.914 0 0 0 -3.274 0z"/><path d="M12 16h.01"/></svg>
                                Agen dengan username <strong>"{{ $username }}"</strong> tidak ditemukan.
                            </div>
                        @endif
                    @endif
                </div>
            </div>

            {{-- ── STEP 2: Form Buat RO ─────────────────────────────────── --}}
            @if($agent && $agent->user?->is_active && ! $hasApprovedVerification)
                <div class="alert alert-warning shadow-sm" role="alert">
                    Agen belum Verifikasi Agent, tidak bisa dibuatkan Repeat Order.
                </div>
            @endif

            @if($agent && $agent->user?->is_active && $hasApprovedVerification)
            <div class="shadow-sm border-0 card" id="step-form">
                <div class="card-header">
                    <h3 class="card-title">
                        <span class="bg-blue me-2 badge">2</span>
                        Detail Transaksi Repeat Order
                    </h3>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.verifications.ro.store') }}" enctype="multipart/form-data" id="create-ro-form">
                        @csrf

                        {{-- Hidden: pass selected username --}}
                        <input type="hidden" name="username" value="{{ $agent->user->username }}">

                        {{-- Info baris --}}
                        <div class="mb-4 row g-3">
                            <div class="col-6 col-md-4">
                                <div class="mb-1 text-muted small">Nama Agen</div>
                                <div class="fw-semibold">{{ $agent->nama }}</div>
                            </div>
                            <div class="col-6 col-md-4">
                                <div class="mb-1 text-muted small">Username</div>
                                <code>{{ $agent->user->username }}</code>
                            </div>
                            <div class="col-6 col-md-4">
                                <div class="mb-1 text-muted small">Nominal RO</div>
                                <div class="text-green fw-bold fs-5">Rp{{ number_format($amount, 0, ',', '.') }}</div>
                            </div>
                        </div>

                        {{-- Upload bukti bayar --}}
                        <div class="mb-4">
                            <label class="form-label" for="proof-upload">Bukti Pembayaran</label>
                            <input
                                type="file"
                                id="proof-upload"
                                name="proof_of_payment"
                                class="form-control @error('proof_of_payment') is-invalid @enderror"
                                accept="image/jpeg,image/png,image/webp"
                            >
                            @error('proof_of_payment')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @else
                                <div class="form-hint">JPG, PNG, WEBP — Maks. 2 MB.</div>
                            @enderror
                        </div>

                        {{-- Preview gambar --}}
                        <div id="img-preview-wrap" class="mb-4 d-none">
                            <div class="mb-1 text-muted small">Preview</div>
                            <img id="img-preview" src="" alt="Preview bukti bayar"
                                class="border rounded img-fluid" style="max-height:260px; object-fit:contain;">
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="px-4 btn btn-success" id="btn-submit-ro">
                                <svg xmlns="http://www.w3.org/2000/svg" class="me-1 icon" width="18" height="18" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10"/></svg>
                                Buat Transaksi RO
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
@endsection

@push('scripts')
<script>
    // Image preview on file select
    document.getElementById('proof-upload')?.addEventListener('change', function () {
        const file = this.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = (e) => {
            const wrap = document.getElementById('img-preview-wrap');
            document.getElementById('img-preview').src = e.target.result;
            wrap.classList.remove('d-none');
        };
        reader.readAsDataURL(file);
    });
</script>
@endpush
