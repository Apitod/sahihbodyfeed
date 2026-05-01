@extends('layouts.app')

@section('title', 'Buat Transaksi Repeat Order — Admin')

@section('content')
{{-- ── Page Header ──────────────────────────────────────────────────────── --}}
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1">
                        <li class="breadcrumb-item"><a href="{{ route('admin.verifications.transactions') }}">Verifikasi Transaksi</a></li>
                        <li class="breadcrumb-item active">Buat RO</li>
                    </ol>
                </nav>
                <h2 class="page-title">Buat Transaksi Repeat Order</h2>
                <p class="text-muted small mt-1">
                    Admin membuat transaksi RO atas nama agen. Cari agen berdasarkan <strong>username</strong>.
                </p>
            </div>
        </div>
    </div>
</div>

<div class="container-xl">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">

            {{-- ── Flash / Validation Errors ───────────────────────────── --}}
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible mb-4" role="alert">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- ── STEP 1: Cari Agen ───────────────────────────────────── --}}
            <div class="card border-0 shadow-sm mb-4" id="step-search">
                <div class="card-header">
                    <h3 class="card-title">
                        <span class="badge bg-blue me-2">1</span>
                        Cari Agen Berdasarkan Username
                    </h3>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.verifications.ro.create') }}" class="row g-2 align-items-end" id="search-agent-form">
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
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="18" height="18" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="10" cy="10" r="7"/><path d="M21 21l-6 -6"/></svg>
                                Cari
                            </button>
                        </div>
                    </form>

                    {{-- Agent preview card --}}
                    @if($username)
                        @if($agent)
                            <div class="alert alert-success d-flex align-items-center gap-3 mt-3 mb-0" id="agent-found-banner" role="alert">
                                <span class="avatar avatar-sm rounded-circle bg-green-lt text-green fw-bold">
                                    {{ strtoupper(substr($agent->nama, 0, 2)) }}
                                </span>
                                <div>
                                    <div class="fw-semibold">{{ $agent->nama }}</div>
                                    <div class="small text-muted">
                                        @username: <strong>{{ $agent->user->username }}</strong>
                                        &nbsp;·&nbsp;
                                        Status:
                                        @if($agent->user?->is_active)
                                            <span class="badge bg-green-lt text-green">Aktif</span>
                                        @else
                                            <span class="badge bg-red-lt text-red">Tidak Aktif</span>
                                        @endif
                                        &nbsp;·&nbsp;
                                        Pangkat: <span class="badge bg-blue-lt text-blue">{{ $agent->status->label() }}</span>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-warning mt-3 mb-0" role="alert">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="18" height="18" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 9v4"/><path d="M10.363 3.591l-8.106 13.534a1.914 1.914 0 0 0 1.636 2.871h16.214a1.914 1.914 0 0 0 1.636 -2.87l-8.106 -13.536a1.914 1.914 0 0 0 -3.274 0z"/><path d="M12 16h.01"/></svg>
                                Agen dengan username <strong>"{{ $username }}"</strong> tidak ditemukan.
                            </div>
                        @endif
                    @endif
                </div>
            </div>

            {{-- ── STEP 2: Form Buat RO ─────────────────────────────────── --}}
            @if($agent && $agent->user?->is_active)
            <div class="card border-0 shadow-sm" id="step-form">
                <div class="card-header">
                    <h3 class="card-title">
                        <span class="badge bg-blue me-2">2</span>
                        Detail Transaksi Repeat Order
                    </h3>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.verifications.ro.store') }}" enctype="multipart/form-data" id="create-ro-form">
                        @csrf

                        {{-- Hidden: pass selected username --}}
                        <input type="hidden" name="username" value="{{ $agent->user->username }}">

                        {{-- Info baris --}}
                        <div class="row g-3 mb-4">
                            <div class="col-6 col-md-4">
                                <div class="text-muted small mb-1">Nama Agen</div>
                                <div class="fw-semibold">{{ $agent->nama }}</div>
                            </div>
                            <div class="col-6 col-md-4">
                                <div class="text-muted small mb-1">Username</div>
                                <code>{{ $agent->user->username }}</code>
                            </div>
                            <div class="col-6 col-md-4">
                                <div class="text-muted small mb-1">Nominal RO</div>
                                <div class="fw-bold text-green fs-5">Rp{{ number_format($amount, 0, ',', '.') }}</div>
                            </div>
                        </div>

                        {{-- Upload bukti bayar --}}
                        <div class="mb-4">
                            <label class="form-label required" for="proof-upload">Bukti Pembayaran</label>
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
                            <div class="text-muted small mb-1">Preview</div>
                            <img id="img-preview" src="" alt="Preview bukti bayar"
                                class="img-fluid rounded border" style="max-height:260px; object-fit:contain;">
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success px-4" id="btn-submit-ro">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="18" height="18" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10"/></svg>
                                Buat Transaksi RO
                            </button>
                            <a href="{{ route('admin.verifications.transactions') }}" class="btn btn-outline-secondary">Batal</a>
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
