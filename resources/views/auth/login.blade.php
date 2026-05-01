<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sahihbodyfeed</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/core@latest/dist/css/tabler.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    <style>
        :root {
            --brand-1: #FCE7C8;
            --brand-2: #B1C29E;
            --brand-3: #FADA7A;
            --brand-4: #F0A04B;
            --brand-gradient: linear-gradient(135deg, var(--brand-4) 0%, #d48a3a 100%);
        }

        body {
            background-color: var(--brand-1);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            font-family: 'Inter', sans-serif;
        }

        .login-container {
            width: 1000px;
            max-width: 95vw;
            height: 600px;
            background: #fff;
            display: flex;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
        }

        /* Left Side: Visual & Message */
        .login-visual {
            flex: 1;
            background: var(--brand-gradient);
            padding: 60px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            color: #fff;
            position: relative;
            overflow: hidden;
        }

        .login-visual::before {
            content: '';
            position: absolute;
            top: -20%;
            right: -20%;
            width: 400px;
            height: 400px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }

        .login-visual::after {
            content: '';
            position: absolute;
            bottom: -10%;
            left: -10%;
            width: 250px;
            height: 250px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
        }

        .brand-name {
            font-size: 1.25rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            margin-bottom: auto;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .welcome-text {
            margin-bottom: auto;
        }

        .welcome-subtitle {
            font-size: 1.1rem;
            opacity: 0.9;
            margin-bottom: 10px;
        }

        .welcome-title {
            font-size: 3.5rem;
            font-weight: 900;
            line-height: 1.1;
            letter-spacing: -0.02em;
        }

        .visual-footer {
            font-size: 0.85rem;
            opacity: 0.7;
            line-height: 1.6;
        }

        /* Right Side: Form */
        .login-form-side {
            width: 450px;
            padding: 60px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: #fff;
        }

        .form-header {
            margin-bottom: 40px;
        }

        .form-title {
            font-size: 1.75rem;
            font-weight: 800;
            color: var(--brand-4);
            margin-bottom: 8px;
        }

        .form-subtitle {
            color: #64748b;
            font-size: 0.95rem;
        }

        .form-label {
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 8px;
        }

        .form-control {
            border: 2px solid #f1f5f9;
            border-radius: 12px;
            padding: 12px 16px;
            font-size: 1rem;
            transition: all 0.2s;
        }

        .form-control:focus {
            border-color: var(--brand-4);
            box-shadow: 0 0 0 4px color-mix(in srgb, var(--brand-4) 15%, white);
        }

        .btn-login {
            background: var(--brand-4);
            color: #fff;
            border: none;
            border-radius: 12px;
            padding: 14px;
            font-size: 1rem;
            font-weight: 700;
            width: 100%;
            margin-top: 20px;
            cursor: pointer;
            transition: all 0.2s;
            box-shadow: 0 10px 15px -3px color-mix(in srgb, var(--brand-4) 30%, black);
        }

        .btn-login:hover {
            background: #d48a3a;
            transform: translateY(-2px);
        }

        .form-footer {
            margin-top: 30px;
            text-align: center;
            font-size: 0.9rem;
            color: #64748b;
        }

        .form-footer a {
            color: var(--brand-4);
            font-weight: 600;
            text-decoration: none;
        }

        .alert {
            border: none;
            border-radius: 12px;
            margin-bottom: 24px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <!-- Visual Side -->
        <div class="login-visual">
            <div class="brand-name">
                <i class="ti ti-leaf fs-1"></i>
                Sahihbodyfeed
            </div>
            
            <div class="welcome-text">
                <div class="welcome-subtitle">Senang bertemu Anda kembali</div>
                <div class="welcome-title">SELAMAT<br>DATANG</div>
            </div>

            <div class="visual-footer">
                Platform resmi keagenan Sahihbodyfeed.<br>
                Kelola jaringan dan pantau komisi Anda dengan mudah dan transparan.
            </div>
        </div>

        <!-- Form Side -->
        <div class="login-form-side">
            <div class="form-header">
                <h2 class="form-title">Login Akun</h2>
                <p class="form-subtitle">Silakan masukkan kredensial Anda untuk masuk ke dashboard.</p>
            </div>

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('login.post') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" placeholder="Masukkan username Anda" required value="{{ old('username') }}">
                </div>
                <div class="mb-4">
                    <label class="form-label">
                        Password
                        {{-- <span class="form-label-description">
                            <a href="#" class="text-muted small">Lupa password?</a>
                        </span> --}}
                    </label>
                    <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                </div>
                <div class="mb-3">
                    <label class="form-check">
                        <input type="checkbox" class="form-check-input" name="remember"/>
                        <span class="form-check-label">Ingat saya di perangkat ini</span>
                    </label>
                </div>
                
                <button type="submit" class="btn-login">
                    MASUK SEKARANG
                </button>
            </form>

            <div class="form-footer">
                Lupa akses akun? Hubungi <a href="#">Admin Center</a>
            </div>
        </div>
    </div>
</body>
</html>