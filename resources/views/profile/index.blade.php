@extends('layouts.app')

@section('title', 'Profil Pengguna')

@section('content')
<div class="page-header d-print-none">
    <div class="row align-items-center">
        <div class="col">
            <h2 class="page-title text-primary font-weight-bold">Profil Akun Anda</h2>
            <div class="text-muted small mt-1">Kelola informasi pribadi dan keamanan akun Anda.</div>
        </div>
    </div>
</div>

<div class="row row-cards">
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm text-center py-4">
            <div class="card-body">
                <span class="avatar avatar-xl bg-blue-lt rounded-circle mb-3" style="font-size: 2rem;">
                    {{ strtoupper(substr(auth()->user()->username, 0, 2)) }}
                </span>
                <h3 class="font-weight-bold mb-1">{{ $agent ? $agent->nama : auth()->user()->username }}</h3>
                <div class="text-muted mb-3">{{ auth()->user()->role->label() }}</div>
                
                @if($agent)
                <div class="mt-4 text-start bg-light p-3 rounded-3">
                    <div class="mb-2">
                        <strong>Status Kemitraan:</strong> <br>
                        <span class="badge bg-primary text-white">{{ $agent->status->label() }}</span>
                    </div>
                    <div class="mb-2">
                        <strong>Tanggal Bergabung:</strong> <br>
                        {{ $agent->joined_at->format('d F Y') }}
                    </div>
                    @if($agent->upline)
                    <div class="mb-2">
                        <strong>Sponsor (Upline):</strong> <br>
                        {{ $agent->upline->nama }}
                    </div>
                    @endif
                    @if($agent->phone)
                    <div class="mb-0">
                        <strong>Nomor WhatsApp:</strong> <br>
                        @php
                            $waNum = preg_replace('/^0/', '62', $agent->phone);
                        @endphp
                        <a href="https://wa.me/{{ $waNum }}" target="_blank" rel="noopener"
                           class="btn btn-sm btn-success mt-1 d-inline-flex align-items-center gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13"
                                 viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413Z"/>
                            </svg>
                            {{ $agent->phone }}
                        </a>
                    </div>
                    @endif
                </div>

                {{-- ── Referral Code Card ──────────────────────────────── --}}
                @if($agent->referral_code)
                <div class="mt-3 text-start border border-primary-subtle rounded-3 p-3 bg-primary-lt">
                    <div class="d-flex align-items-center mb-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon text-primary me-2" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 14a3.5 3.5 0 0 0 5 0l4 -4a3.5 3.5 0 0 0 -5 -5l-1.5 1.5" /><path d="M14 10a3.5 3.5 0 0 0 -5 0l-4 4a3.5 3.5 0 0 0 5 5l1.5 -1.5" /></svg>
                        <strong class="small text-primary">Kode Referral Anda</strong>
                    </div>

                    {{-- Copyable code --}}
                    <div class="input-group input-group-sm mb-2">
                        <input
                            id="referral-code-input"
                            type="text"
                            class="form-control font-monospace fw-bold text-center"
                            value="{{ $agent->referral_code }}"
                            readonly
                        >
                        <button
                            class="btn btn-outline-primary"
                            type="button"
                            onclick="copyToClipboard('{{ $agent->referral_code }}', this)"
                            title="Salin kode"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M8 8m0 2a2 2 0 0 1 2 -2h8a2 2 0 0 1 2 2v8a2 2 0 0 1 -2 2h-8a2 2 0 0 1 -2 -2z" /><path d="M16 8v-2a2 2 0 0 0 -2 -2h-8a2 2 0 0 0 -2 2v8a2 2 0 0 0 2 2h2" /></svg>
                        </button>
                    </div>
                    <small class="text-muted mt-2 d-block">Bagikan kode atau link ini kepada calon agen baru.</small>
                </div>
                @endif
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h3 class="card-title font-weight-bold">Keamanan & Password</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('profile.password') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label font-weight-bold small">Password Saat Ini</label>
                        <input type="password" name="current_password" class="form-control rounded-3 py-2 @error('current_password') is-invalid @enderror" required>
                        @error('current_password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label font-weight-bold small">Password Baru</label>
                            <input type="password" name="password" class="form-control rounded-3 py-2 @error('password') is-invalid @enderror" required>
                            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label font-weight-bold small">Konfirmasi Password Baru</label>
                            <input type="password" name="password_confirmation" class="form-control rounded-3 py-2" required>
                        </div>
                    </div>
                    <div class="form-footer mt-4">
                        <button type="submit" class="btn btn-primary rounded-3 px-4 shadow-sm font-weight-bold">
                            Perbarui Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
