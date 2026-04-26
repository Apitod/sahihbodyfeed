<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <title>Registrasi Agen Baru - Sahihbodyfeed</title>
    <link href="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta20/dist/css/tabler.min.css" rel="stylesheet"/>
</head>
<body class="d-flex flex-column bg-white">
    <div class="row g-0 flex-fill">
        <div class="col-12 col-lg-6 col-xl-4 d-flex flex-column justify-content-center">
            <div class="container container-tight pt-5 pb-5 px-lg-5">
                <div class="text-center mb-4">
                    <a href="." class="navbar-brand navbar-brand-autodark">
                        <h1 class="font-weight-bold" style="letter-spacing: -1px; color: #1e293b;">SAHIHBODYFEED</h1>
                    </a>
                </div>

                <div class="mb-4">
                    <h2 class="h3 font-weight-bold mb-1">Registrasi Agen Baru</h2>
                    <p class="text-muted small">Bergabunglah dengan ekosistem bisnis Sahihbodyfeed.</p>
                </div>

                <div class="card bg-primary-lt border-0 mb-4 rounded-3">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center mb-2">
                            <span class="badge bg-primary text-white me-2">Wajib</span>
                            <span class="font-weight-bold small text-primary">Biaya Registrasi: Rp 2.650.000</span>
                        </div>
                        <p class="text-secondary small mb-0">Transfer ke: <strong>BCA 123456789 a.n PT Sahihbodyfeed</strong></p>
                    </div>
                </div>
                
                <form action="{{ route('register.post') }}" method="POST" enctype="multipart/form-data" autocomplete="off">
                    @csrf
                    
                    @if($referrer)
                        <div class="mb-3">
                            <label class="form-label font-weight-bold small">Sponsor (Upline)</label>
                            <div class="form-control-plaintext bg-light px-3 py-2 rounded-3 small">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-inline me-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" /><path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" /></svg>
                                <strong>{{ $referrer->nama }}</strong> <span class="text-muted">(@ {{ $referrer->user->username }})</span>
                            </div>
                            <input type="hidden" name="referral_agent_id" value="{{ $referrer->id }}">
                        </div>
                    @endif

                    <div class="mb-3">
                        <label class="form-label font-weight-bold small">Nama Lengkap</label>
                        <input type="text" name="nama" class="form-control rounded-3 py-2 @error('nama') is-invalid @enderror" value="{{ old('nama') }}" placeholder="Contoh: Budi Santoso" required>
                        @error('nama') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label font-weight-bold small">Username Login</label>
                        <input type="text" name="username" class="form-control rounded-3 py-2 @error('username') is-invalid @enderror" value="{{ old('username') }}" placeholder="budi_agen" required>
                        @error('username') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label font-weight-bold small">Password</label>
                            <input type="password" name="password" class="form-control rounded-3 py-2 @error('password') is-invalid @enderror" required>
                            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label font-weight-bold small">Konfirmasi</label>
                            <input type="password" name="password_confirmation" class="form-control rounded-3 py-2" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label font-weight-bold small">Bukti Transfer Pembayaran</label>
                        <input type="file" name="proof_of_payment" class="form-control rounded-3 py-2 @error('proof_of_payment') is-invalid @enderror" accept="image/*" required>
                        <small class="text-muted small mt-1 d-block">Unggah foto/screenshot bukti transfer Anda.</small>
                        @error('proof_of_payment') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-footer">
                        <button type="submit" class="btn btn-primary w-100 py-2 rounded-3 shadow-sm font-weight-bold">
                            Kirim Selesaikan Pendaftaran
                        </button>
                    </div>
                </form>

                <div class="text-center text-secondary mt-4 small">
                    Sudah punya akun? <a href="{{ route('login') }}" class="text-primary font-weight-bold">Masuk di sini</a>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-6 col-xl-8 d-none d-lg-block">
            <div class="bg-cover h-100 min-vh-100" style="background-image: url('https://images.unsplash.com/photo-1522071820081-009f0129c71c?ixlib=rb-1.2.1-&auto=format&fit=crop&w=1350&q=80');">
                <div class="h-100 w-100 d-flex flex-column justify-content-end p-5" style="background: linear-gradient(to top, rgba(15, 23, 42, 0.9), transparent);">
                    <div class="text-white">
                        <p class="h1 font-weight-bold mb-3">Mulai Perjalanan Anda.</p>
                        <p class="lead opacity-75">Jadilah bagian dari jaringan agen kecantikan dan kesehatan nomor satu di Indonesia.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
