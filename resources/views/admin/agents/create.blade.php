@extends('layouts.app')

@section('title', 'Tambah Agen Baru')

@section('content')
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
                <p class="text-muted small mt-1">Agen yang dibuat oleh admin langsung aktif tanpa proses verifikasi pembayaran.</p>
            </div>
        </div>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-12 col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form method="POST" action="{{ route('admin.agents.store') }}" id="create-agent-form">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label required" for="input-nama">Nama Lengkap</label>
                        <input type="text" class="form-control @error('nama') is-invalid @enderror"
                            id="input-nama" name="nama" value="{{ old('nama') }}" required autofocus>
                        @error('nama')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label required" for="input-username">Username</label>
                        <input type="text" class="form-control @error('username') is-invalid @enderror"
                            id="input-username" name="username" value="{{ old('username') }}" required>
                        @error('username')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label required" for="input-password">Password</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                id="input-password" name="password" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label required" for="input-password-confirmation">Konfirmasi Password</label>
                            <input type="password" class="form-control"
                                id="input-password-confirmation" name="password_confirmation" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="input-phone">Nomor Telepon</label>
                        <input type="text" class="form-control @error('phone') is-invalid @enderror"
                            id="input-phone" name="phone" value="{{ old('phone') }}">
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label" for="input-upline">Upline (Sponsor)</label>
                        <select class="form-select @error('upline_id') is-invalid @enderror"
                            id="input-upline" name="upline_id">
                            <option value="">— Tidak ada upline —</option>
                            @foreach($uplineAgents as $upline)
                                <option value="{{ $upline->id }}" @selected(old('upline_id') == $upline->id)>
                                    {{ $upline->nama }} ({{ $upline->referral_code }})
                                </option>
                            @endforeach
                        </select>
                        @error('upline_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary" id="btn-submit-create">
                            Buat Agen
                        </button>
                        <a href="{{ route('admin.agents.index') }}" class="btn btn-outline-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
