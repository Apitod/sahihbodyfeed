@extends('layouts.app')

@section('title', 'Edit Agen — ' . $agent->nama)

@section('content')
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
                <h2 class="page-title">Edit Agen</h2>
            </div>
        </div>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-12 col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form method="POST" action="{{ route('admin.agents.update', $agent) }}" id="edit-agent-form">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label required" for="edit-nama">Nama Lengkap</label>
                        <input type="text" class="form-control @error('nama') is-invalid @enderror"
                            id="edit-nama" name="nama" value="{{ old('nama', $agent->nama) }}" required>
                        @error('nama')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Username is read-only — tied to User model; change via separate profile flow. --}}
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" class="form-control" value="{{ $agent->user?->username }}" disabled>
                        <div class="form-hint">Username hanya dapat diubah melalui profil pengguna.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="edit-phone">Nomor Telepon</label>
                        <input type="text" class="form-control @error('phone') is-invalid @enderror"
                            id="edit-phone" name="phone" value="{{ old('phone', $agent->phone) }}">
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
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

                    <div class="mb-3 p-3 rounded bg-blue-lt">
                        <div class="small text-muted mb-1">Info Sistem (Read-only)</div>
                        <dl class="row g-1 small mb-0">
                            <dt class="col-5 text-muted">Kode Referral</dt>
                            <dd class="col-7"><code>{{ $agent->referral_code }}</code></dd>
                            <dt class="col-5 text-muted">Pangkat Saat Ini</dt>
                            <dd class="col-7">{{ $agent->status->label() }}</dd>
                            <dt class="col-5 text-muted">Total Poin</dt>
                            <dd class="col-7">{{ number_format($agent->total_points) }}</dd>
                        </dl>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary" id="btn-submit-edit">Simpan Perubahan</button>
                        <a href="{{ route('admin.agents.show', $agent) }}" class="btn btn-outline-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
