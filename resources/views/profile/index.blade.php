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
                    <div class="mb-0">
                        <strong>Sponsor (Upline):</strong> <br>
                        {{ $agent->upline->nama }}
                    </div>
                    @endif
                </div>
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
