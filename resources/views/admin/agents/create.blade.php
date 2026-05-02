@extends('layouts.app')

@section('title', 'Tambah Agen Baru')

@section('content')
{{-- ── Page Header ──────────────────────────────────────────────────────── --}}
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1">
                        <li class="breadcrumb-item"><a href="{{ route('admin.agents.index') }}">Agen</a></li>
                        <li class="breadcrumb-item active">Tambah Baru</li>
                    </ol>
                </nav>
                <h2 class="page-title">Tambah Agen Baru</h2>
                <p class="text-muted small mt-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm text-blue me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="12" cy="12" r="9"/><path d="M12 8h.01"/><path d="M11 12h1v4h1"/>
                    </svg>
                    Agen yang dibuat oleh admin langsung aktif tanpa verifikasi pembayaran. Registrasi mandiri tidak tersedia.
                </p>
            </div>
        </div>
    </div>
</div>

<form method="POST" action="{{ route('admin.agents.store') }}" id="create-agent-form">
@csrf
<div class="row g-4">

    {{-- ── LEFT COLUMN: Credentials + Personal Data ── --}}
    <div class="col-12 col-lg-7">

        {{-- Credentials --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header">
                <h3 class="card-title">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2 text-blue" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="12" cy="7" r="4"/><path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"/>
                    </svg>
                    Kredensial Akun
                </h3>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label required" for="input-username">Username</label>
                    <input type="text" class="form-control @error('username') is-invalid @enderror"
                        id="input-username" name="username" value="{{ old('username') }}"
                        required autofocus autocomplete="off" placeholder="Contoh: budi.santoso">
                    @error('username')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label required" for="input-password">Password</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror"
                            id="input-password" name="password" required autocomplete="new-password">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-hint">Minimal 8 karakter.</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label required" for="input-password-confirmation">Konfirmasi Password</label>
                        <input type="password" class="form-control"
                            id="input-password-confirmation" name="password_confirmation" required autocomplete="new-password">
                    </div>
                </div>
            </div>
        </div>

        {{-- Personal Data --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header">
                <h3 class="card-title">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2 text-green" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M7 15l3.5 -3.5l2 2l2.5 -2.5l3 3"/>
                    </svg>
                    Data Pribadi
                </h3>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label required" for="input-nama">Nama Lengkap</label>
                    <input type="text" class="form-control @error('nama') is-invalid @enderror"
                        id="input-nama" name="nama" value="{{ old('nama') }}" required placeholder="Nama sesuai KTP">
                    @error('nama')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label" for="input-no-telp">No. Telepon</label>
                        <input type="text" class="form-control @error('no_telp') is-invalid @enderror"
                            id="input-no-telp" name="no_telp" value="{{ old('no_telp') }}" placeholder="08xxxxxxxxxx">
                        @error('no_telp')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        {{-- Upline selector --}}
                        <label class="form-label" for="input-upline">Upline (Sponsor)</label>
                        <select class="form-select @error('upline_id') is-invalid @enderror"
                            id="input-upline" name="upline_id">
                            <option value="">— Tidak ada upline —</option>
                            @foreach($uplineAgents as $upline)
                                <option value="{{ $upline->id }}" @selected(old('upline_id') == $upline->id)>
                                    {{ $upline->nama }} ({{ $upline->user?->username ?? 'Tidak ada username' }})
                                </option>
                            @endforeach
                        </select>
                        @error('upline_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="mt-3">
                    <label class="form-label" for="input-alamat">Alamat</label>
                    <textarea class="form-control @error('alamat') is-invalid @enderror"
                        id="input-alamat" name="alamat" rows="3"
                        placeholder="Alamat lengkap agen…">{{ old('alamat') }}</textarea>
                    @error('alamat')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Bank Data --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header">
                <h3 class="card-title">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2 text-yellow" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 21l18 0"/><path d="M3 10l18 0"/><path d="M5 6l7 -3l7 3"/><path d="M4 10l0 11"/><path d="M20 10l0 11"/><path d="M8 14l0 3"/><path d="M12 14l0 3"/><path d="M16 14l0 3"/>
                    </svg>
                    Data Bank (Untuk Komisi)
                </h3>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label" for="input-bank-name">Nama Bank</label>
                        <input type="text" class="form-control @error('bank_name') is-invalid @enderror"
                            id="input-bank-name" name="bank_name" value="{{ old('bank_name') }}"
                            placeholder="Contoh: BCA, BRI, Mandiri, BNI…">
                        @error('bank_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="input-bank-account">Nomor Rekening</label>
                        <input type="text" class="form-control @error('bank_account') is-invalid @enderror"
                            id="input-bank-account" name="bank_account" value="{{ old('bank_account') }}"
                            placeholder="0123456789">
                        @error('bank_account')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="input-bank-account-name">Nama Pemilik Rekening</label>
                        <input type="text" class="form-control @error('bank_account_name') is-invalid @enderror"
                            id="input-bank-account-name" name="bank_account_name" value="{{ old('bank_account_name') }}"
                            placeholder="Nama sesuai rekening">
                        @error('bank_account_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

    </div>{{-- /left column --}}

    {{-- ── RIGHT COLUMN: NIK + Submit ── --}}
    <div class="col-12 col-lg-5">
        <div class="card border-0 shadow-sm h-auto">
            <div class="card-header">
                <h3 class="card-title">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2 text-purple" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/><rect x="3" y="4" width="18" height="16" rx="2"/><circle cx="9" cy="10" r="2"/><path d="M15 8h2"/><path d="M15 12h2"/><path d="M7 16h10"/>
                    </svg>
                    Identitas (NIK)
                </h3>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label" for="input-nik">Nomor NIK (KTP)</label>
                    <input type="text" class="form-control @error('nik') is-invalid @enderror"
                        id="input-nik" name="nik"
                        value="{{ old('nik') }}"
                        maxlength="16"
                        inputmode="numeric"
                        pattern="[0-9]{0,16}"
                        placeholder="16 digit nomor NIK"
                        oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0,16)">
                    @error('nik')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-hint">Nomor Induk Kependudukan, maksimal 16 angka.</div>
                </div>
                <div id="nik-counter" class="text-end small text-muted mb-0">
                    <span id="nik-char-count">0</span>/16 digit
                </div>
            </div>
        </div>

        {{-- Submit card --}}
        <div class="card border-0 shadow-sm mt-4">
            <div class="card-body">
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary" id="btn-submit-create">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14"/><path d="M5 12l14 0"/>
                        </svg>
                        Buat Agen
                    </button>
                    <a href="{{ route('admin.agents.index') }}" class="btn btn-outline-secondary">Batal</a>
                </div>
                <p class="text-muted small mt-3 mb-0 text-center">
                    Agen akan langsung aktif setelah dibuat oleh admin.
                </p>
            </div>
        </div>

    </div>{{-- /right column --}}

</div>{{-- /row --}}
</form>

@push('scripts')
<script>
    // NIK digit counter
    const nikInput = document.getElementById('input-nik');
    const nikCount = document.getElementById('nik-char-count');
    if (nikInput && nikCount) {
        nikInput.addEventListener('input', function () {
            nikCount.textContent = this.value.length;
        });
        // Init on page load (old value)
        nikCount.textContent = nikInput.value.length;
    }
</script>
@endpush
@endsection
