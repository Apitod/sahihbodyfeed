@extends('layouts.app')

@section('title', 'Edit Agen — ' . $agent->nama)

@section('content')
{{-- ── Page Header ──────────────────────────────────────────────────────── --}}
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1">
                        <li class="breadcrumb-item"><a href="{{ route('admin.agents.index') }}">Agen</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.agents.show', $agent) }}">{{ $agent->nama }}</a></li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </nav>
                <h2 class="page-title">Edit Agen — <span class="text-muted">{{ $agent->nama }}</span></h2>
            </div>
        </div>
    </div>
</div>

<form method="POST" action="{{ route('admin.agents.update', $agent) }}"
      enctype="multipart/form-data" id="edit-agent-form">
@csrf
@method('PUT')

<div class="row g-4">

    {{-- ── LEFT COLUMN ── --}}
    <div class="col-12 col-lg-7">

        {{-- Read-only system info --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header">
                <h3 class="card-title">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2 text-secondary" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="12" cy="12" r="9"/><path d="M12 8h.01"/><path d="M11 12h1v4h1"/>
                    </svg>
                    Info Sistem (Read-only)
                </h3>
            </div>
            <div class="card-body">
                <div class="row g-2">
                    <div class="col-6 col-md-3">
                        <div class="text-muted small">Username</div>
                        <div class="fw-semibold">{{ $agent->user?->username ?? '—' }}</div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="text-muted small">Kode Referral</div>
                        <code>{{ $agent->referral_code }}</code>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="text-muted small">Pangkat</div>
                        <span class="badge bg-blue-lt text-blue">{{ $agent->status->label() }}</span>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="text-muted small">Total Poin</div>
                        <div class="fw-semibold">{{ number_format($agent->total_points) }}</div>
                    </div>
                </div>
                <div class="form-hint mt-2">Username hanya dapat diubah melalui profil pengguna.</div>
            </div>
        </div>

        {{-- Personal Data --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header">
                <h3 class="card-title">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2 text-green" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="12" cy="7" r="4"/><path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"/>
                    </svg>
                    Data Pribadi
                </h3>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label required" for="edit-nama">Nama Lengkap</label>
                    <input type="text" class="form-control @error('nama') is-invalid @enderror"
                        id="edit-nama" name="nama" value="{{ old('nama', $agent->nama) }}" required>
                    @error('nama')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label" for="edit-no-telp">No. Telepon</label>
                        <input type="text" class="form-control @error('no_telp') is-invalid @enderror"
                            id="edit-no-telp" name="no_telp" value="{{ old('no_telp', $agent->no_telp) }}"
                            placeholder="08xxxxxxxxxx">
                        @error('no_telp')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="edit-upline">Upline (Sponsor)</label>
                        <select class="form-select @error('upline_id') is-invalid @enderror"
                            id="edit-upline" name="upline_id">
                            <option value="">— Tidak ada upline —</option>
                            @foreach($uplineAgents as $upline)
                                <option value="{{ $upline->id }}"
                                    @selected(old('upline_id', $agent->upline_id) == $upline->id)>
                                    {{ $upline->nama }} ({{ $upline->referral_code }})
                                </option>
                            @endforeach
                        </select>
                        @error('upline_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="mt-3">
                    <label class="form-label" for="edit-alamat">Alamat</label>
                    <textarea class="form-control @error('alamat') is-invalid @enderror"
                        id="edit-alamat" name="alamat" rows="3">{{ old('alamat', $agent->alamat) }}</textarea>
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
                    Data Bank
                </h3>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label" for="edit-bank-name">Nama Bank</label>
                        <input type="text" class="form-control @error('bank_name') is-invalid @enderror"
                            id="edit-bank-name" name="bank_name"
                            value="{{ old('bank_name', $agent->bank_name) }}"
                            placeholder="Contoh: BCA, BRI, Mandiri…">
                        @error('bank_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="edit-bank-account">Nomor Rekening</label>
                        <input type="text" class="form-control @error('bank_account') is-invalid @enderror"
                            id="edit-bank-account" name="bank_account"
                            value="{{ old('bank_account', $agent->bank_account) }}"
                            placeholder="0123456789">
                        @error('bank_account')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="edit-bank-account-name">Nama Pemilik Rekening</label>
                        <input type="text" class="form-control @error('bank_account_name') is-invalid @enderror"
                            id="edit-bank-account-name" name="bank_account_name"
                            value="{{ old('bank_account_name', $agent->bank_account_name) }}"
                            placeholder="Sesuai nama di rekening">
                        @error('bank_account_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

    </div>{{-- /left --}}

    {{-- ── RIGHT COLUMN: KTP + Submit ── --}}
    <div class="col-12 col-lg-5">

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header">
                <h3 class="card-title">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2 text-purple" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/><rect x="3" y="4" width="18" height="16" rx="2"/><circle cx="9" cy="10" r="2"/><path d="M15 8h2"/><path d="M15 12h2"/><path d="M7 16h10"/>
                    </svg>
                    Foto KTP
                </h3>
            </div>
            <div class="card-body d-flex flex-column align-items-center">

                {{-- Show existing KTP or placeholder --}}
                <div id="ktp-preview-container" class="w-100 mb-3 rounded border border-2 border-dashed d-flex align-items-center justify-content-center"
                    style="min-height: 200px; background: var(--tblr-gray-100); overflow: hidden; cursor: pointer;"
                    onclick="document.getElementById('input-foto-ktp').click()">

                    @if($agent->foto_ktp)
                        <img id="ktp-preview-img"
                            src="{{ asset('storage/' . $agent->foto_ktp) }}"
                            alt="Foto KTP {{ $agent->nama }}"
                            class="img-fluid rounded"
                            style="max-height: 220px; object-fit: contain;">
                    @else
                        <img id="ktp-preview-img" src="" alt="Preview KTP" class="img-fluid rounded d-none" style="max-height: 220px; object-fit: contain;">
                        <div id="ktp-placeholder" class="text-center text-muted py-4 px-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg mb-2 text-muted" width="48" height="48" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" fill="none">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M15 8h.01"/><rect x="3" y="6" width="18" height="12" rx="3"/><path d="M3 13l4 -4a3 5 0 0 1 3 0l4 4"/><path d="M13 12l2 -2a3 5 0 0 1 3 0l2 2"/>
                            </svg>
                            <p class="mb-1 fw-medium">Belum ada foto KTP</p>
                            <p class="small mb-0">Klik untuk upload</p>
                        </div>
                    @endif
                </div>

                <input type="file" class="d-none @error('foto_ktp') is-invalid @enderror"
                    id="input-foto-ktp" name="foto_ktp" accept="image/jpeg,image/png">
                @error('foto_ktp')
                    <div class="text-danger small mb-2">{{ $message }}</div>
                @enderror

                <button type="button" class="btn btn-outline-secondary btn-sm w-100"
                    onclick="document.getElementById('input-foto-ktp').click()">
                    {{ $agent->foto_ktp ? 'Ganti Foto KTP' : 'Upload Foto KTP' }}
                </button>
                <p class="text-muted small mt-2 mb-0 text-center">JPG, PNG — Maks. 2 MB. Biarkan kosong untuk tidak mengubah foto.</p>
            </div>
        </div>

        {{-- Submit --}}
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary" id="btn-submit-edit">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10"/>
                        </svg>
                        Simpan Perubahan
                    </button>
                    <a href="{{ route('admin.agents.show', $agent) }}" class="btn btn-outline-secondary">Batal</a>
                </div>
            </div>
        </div>

    </div>{{-- /right --}}

</div>{{-- /row --}}
</form>

@push('scripts')
<script>
    document.getElementById('input-foto-ktp').addEventListener('change', function (e) {
        const file = e.target.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = function (ev) {
            const img = document.getElementById('ktp-preview-img');
            const placeholder = document.getElementById('ktp-placeholder');
            img.src = ev.target.result;
            img.classList.remove('d-none');
            if (placeholder) placeholder.classList.add('d-none');
        };
        reader.readAsDataURL(file);
    });
</script>
@endpush
@endsection
