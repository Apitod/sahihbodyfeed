<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
    <title>Login - Sahihbodyfeed</title>
    <link href="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta20/dist/css/tabler.min.css" rel="stylesheet" />
</head>

<body class="d-flex flex-column bg-white">
    <div class="row g-0 flex-fill">
        <div class="col-12 col-lg-6 col-xl-4 d-flex flex-column justify-content-center">
            <div class="container container-tight pt-2 pb-5 px-lg-5">
                <div class="text-center mb-4">
                    <a href="." class="navbar-brand navbar-brand-autodark">
                        <h1 class="font-weight-bold" style="letter-spacing: -1px; color: #1e293b;">SAHIHBODYFEED</h1>
                    </a>
                </div>

                @if(session('error'))
                    <div class="alert alert-danger mb-4" role="alert">
                        <div class="d-flex">
                            <div>
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24"
                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                                    <path d="M12 8v4" />
                                    <path d="M12 16h.01" />
                                </svg>
                            </div>
                            <div class="ms-2 small text-secondary">{{ session('error') }}</div>
                        </div>
                    </div>
                @endif

                @if(session('success'))
                    <div class="alert alert-success mb-4" role="alert">
                        <div class="d-flex">
                            <div>
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24"
                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M5 12l5 5l10 -10" />
                                </svg>
                            </div>
                            <div class="ms-2 small text-secondary">{{ session('success') }}</div>
                        </div>
                    </div>
                @endif

                <div class="mb-4">
                    <h2 class="h3 font-weight-bold mb-1">Selamat Datang Kembali</h2>
                    <p class="text-muted small">Silakan masuk ke akun kemitraan Anda.</p>
                </div>

                <form action="{{ route('login.post') }}" method="POST" autocomplete="off">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label font-weight-bold small">Username</label>
                        <input type="text" name="username"
                            class="form-control rounded-3 py-2 @error('username') is-invalid @enderror"
                            placeholder="Username" value="{{ old('username') }}" required>
                        @error('username')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-2">
                        <label class="form-label font-weight-bold small">Password</label>
                        <input type="password" name="password" class="form-control rounded-3 py-2"
                            placeholder="Password" autocomplete="off" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-check small text-muted">
                            <input type="checkbox" name="remember" class="form-check-input" />
                            <span class="form-check-label">Ingat saya di perangkat ini</span>
                        </label>
                    </div>
                    <div class="form-footer">
                        <button type="submit" class="btn btn-primary w-100 py-2 rounded-3 shadow-sm font-weight-bold">
                            Masuk Ke Dashboard
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <div class="col-12 col-lg-6 col-xl-8 d-none d-lg-block">
            <!-- Background Image with Gradient Overlay -->
            <div class="bg-cover h-100 min-vh-100"
                style="background-image: url('https://images.unsplash.com/photo-1557804506-669a67965ba0?ixlib=rb-1.2.1-&auto=format&fit=crop&w=1267&q=80');">
                <div class="h-100 w-100 d-flex flex-column justify-content-end p-5"
                    style="background: linear-gradient(to top, rgba(15, 23, 42, 0.9), transparent);">
                    <div class="text-white">
                        <p class="h1 font-weight-bold mb-3">Tumbuh Bersama Sahihbodyfeed.</p>
                        <p class="lead opacity-75">Sistem kemitraan cerdas untuk kesuksesan bersama di industri
                            kesehatan dan kecantikan.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>