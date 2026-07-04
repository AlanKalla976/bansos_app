<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin — SPK Bantuan Sosial</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/logo.svg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --primary:   #1E3A5F;
            --secondary: #2D6A4F;
            --accent:    #52B788;
            --gold:      #F9C74F;
        }
        * { box-sizing: border-box; }
        body {
            font-family: 'Inter', system-ui, sans-serif;
            min-height: 100vh;
            margin: 0;
            display: flex;
            background: #F0F4F8;
            -webkit-font-smoothing: antialiased;
        }

        /* ── LEFT PANEL (branding) ── */
        .auth-left {
            flex: 1;
            background: linear-gradient(145deg, var(--primary) 0%, #0D2137 40%, var(--secondary) 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 3rem 2.5rem;
            position: relative;
            overflow: hidden;
        }
        .auth-left::before {
            content: '';
            position: absolute;
            width: 500px; height: 500px;
            border-radius: 50%;
            background: rgba(82,183,136,.08);
            top: -150px; right: -150px;
        }
        .auth-left::after {
            content: '';
            position: absolute;
            width: 350px; height: 350px;
            border-radius: 50%;
            background: rgba(249,199,79,.06);
            bottom: -100px; left: -100px;
        }
        .auth-pattern {
            position: absolute;
            inset: 0;
            background-image:
                radial-gradient(circle, rgba(255,255,255,.03) 1px, transparent 1px);
            background-size: 28px 28px;
        }
        .auth-brand {
            position: relative;
            z-index: 1;
            text-align: center;
        }
        .auth-logo-circle {
            width: 110px; height: 110px;
            border-radius: 50%;
            background: rgba(255,255,255,.1);
            border: 2px solid rgba(255,255,255,.15);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            backdrop-filter: blur(8px);
            position: relative;
        }
        .auth-logo-circle::before {
            content: '';
            position: absolute;
            inset: -6px;
            border-radius: 50%;
            border: 2px dashed rgba(249,199,79,.3);
            animation: spin 20s linear infinite;
        }
        @keyframes spin { to { transform: rotate(360deg); } }
        .auth-logo-icon {
            font-size: 3rem;
            color: var(--gold);
        }
        .auth-brand-title {
            font-size: 1rem;
            font-weight: 700;
            color: rgba(255,255,255,.7);
            letter-spacing: 3px;
            text-transform: uppercase;
            margin-bottom: .5rem;
        }
        .auth-brand-name {
            font-size: 2rem;
            font-weight: 900;
            color: #fff;
            line-height: 1.1;
            margin-bottom: .5rem;
        }
        .auth-brand-name span { color: var(--gold); }
        .auth-brand-sub {
            font-size: .82rem;
            color: rgba(255,255,255,.5);
            font-weight: 400;
        }
        .auth-gold-line {
            width: 60px; height: 4px;
            background: linear-gradient(90deg, var(--gold), var(--accent));
            border-radius: 2px;
            margin: 1.25rem auto;
        }
        .auth-features {
            position: relative;
            z-index: 1;
            margin-top: 3rem;
            width: 100%;
            max-width: 320px;
        }
        .auth-feature-item {
            display: flex;
            align-items: center;
            gap: .75rem;
            padding: .65rem 1rem;
            border-radius: 10px;
            background: rgba(255,255,255,.06);
            border: 1px solid rgba(255,255,255,.08);
            margin-bottom: .6rem;
            backdrop-filter: blur(4px);
        }
        .auth-feature-item i {
            color: var(--accent);
            font-size: 1.1rem;
            flex-shrink: 0;
        }
        .auth-feature-item span {
            font-size: .78rem;
            color: rgba(255,255,255,.7);
            font-weight: 500;
        }

        /* ── RIGHT PANEL (form) ── */
        .auth-right {
            width: 480px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2.5rem 2rem;
            background: #F0F4F8;
            overflow-y: auto;
        }
        .auth-form-wrap {
            width: 100%;
            max-width: 380px;
        }
        .auth-card {
            background: #fff;
            border-radius: 20px;
            padding: 2rem 2rem;
            box-shadow: 0 8px 32px rgba(30,58,95,.12);
            border: 1px solid #E2E8F0;
        }
        .auth-card-header {
            text-align: center;
            margin-bottom: 1.75rem;
        }
        .auth-card-header .header-icon {
            width: 56px; height: 56px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 1.5rem;
            color: #fff;
            box-shadow: 0 4px 16px rgba(30,58,95,.3);
        }
        .auth-card-header h2 {
            font-size: 1.35rem;
            font-weight: 800;
            color: var(--primary);
            margin: 0 0 .3rem;
        }
        .auth-card-header p {
            font-size: .78rem;
            color: #64748B;
            margin: 0;
        }

        /* Form styles */
        .form-label {
            font-size: .8rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: .35rem;
        }
        .form-control {
            font-size: .85rem;
            border: 1.5px solid #E2E8F0;
            border-radius: 10px;
            padding: .6rem .9rem;
            transition: all .2s;
        }
        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(30,58,95,.12);
        }
        .input-group-text {
            background: #F8FAFC;
            border: 1.5px solid #E2E8F0;
            color: #94A3B8;
            border-radius: 10px 0 0 10px;
        }
        .input-group .form-control {
            border-left: none;
            border-radius: 0 10px 10px 0;
        }
        .input-group:focus-within .input-group-text {
            border-color: var(--primary);
            color: var(--primary);
        }
        .input-group:focus-within .form-control {
            border-color: var(--primary);
        }

        .btn-login {
            width: 100%;
            padding: .7rem;
            font-size: .9rem;
            font-weight: 700;
            border-radius: 10px;
            border: none;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: #fff;
            letter-spacing: .3px;
            transition: all .25s;
            box-shadow: 0 4px 14px rgba(30,58,95,.3);
        }
        .btn-login:hover {
            background: linear-gradient(135deg, #2A4F7A, #3D8B6A);
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(30,58,95,.35);
        }
        .btn-login:active { transform: translateY(0); }

        .alert {
            border-radius: 10px;
            font-size: .82rem;
            border: none;
            padding: .75rem 1rem;
        }
        .alert-success {
            background: #ECFDF5;
            color: #065F46;
            border-left: 4px solid var(--accent);
        }
        .alert-danger {
            background: #FEF2F2;
            color: #991B1B;
            border-left: 4px solid #DC2626;
        }

        .form-check-input:checked {
            background-color: var(--primary);
            border-color: var(--primary);
        }
        .toggle-eye {
            background: #F8FAFC;
            border: 1.5px solid #E2E8F0;
            border-left: none;
            border-radius: 0 10px 10px 0;
            color: #94A3B8;
            cursor: pointer;
            transition: all .2s;
        }
        .toggle-eye:hover { color: var(--primary); background: #EEF4FF; }

        .divider-text {
            position: relative;
            text-align: center;
            margin: 1.5rem 0;
        }
        .divider-text::before {
            content: '';
            position: absolute;
            top: 50%; left: 0; right: 0;
            height: 1px;
            background: #E2E8F0;
        }
        .divider-text span {
            position: relative;
            background: #fff;
            padding: 0 .75rem;
            font-size: .72rem;
            color: #94A3B8;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .auth-left { display: none; }
            .auth-right { width: 100%; padding: 1.5rem 1rem; }
        }
    </style>
</head>
<body>

    {{-- Left Panel --}}
    <div class="auth-left">
        <div class="auth-pattern"></div>
        <div class="auth-brand">
            <div class="auth-logo-circle">
                <i class="bi bi-house-heart-fill auth-logo-icon"></i>
            </div>
            <div class="auth-brand-title">Sistem Pendukung Keputusan</div>
            <div class="auth-brand-name"><span>Bantuan</span> Sosial</div>
            <div class="auth-gold-line"></div>
            <div class="auth-brand-sub">Kelurahan Harjamukti · Kota Cirebon</div>
        </div>
        <div class="auth-features">
            <div class="auth-feature-item">
                <i class="bi bi-shield-check-fill"></i>
                <span>Sistem berbasis Metode AHP & MOORA</span>
            </div>
            <div class="auth-feature-item">
                <i class="bi bi-people-fill"></i>
                <span>Penentuan kelayakan penerima bantuan</span>
            </div>
            <div class="auth-feature-item">
                <i class="bi bi-graph-up-arrow"></i>
                <span>Proses seleksi transparan & akuntabel</span>
            </div>
        </div>
    </div>

    {{-- Right Panel (Form) --}}
    <div class="auth-right">
        <div class="auth-form-wrap">
            <div class="auth-card">
                <div class="auth-card-header">
                    <div class="header-icon">
                        <i class="bi bi-shield-lock-fill"></i>
                    </div>
                    <h2>Login Admin</h2>
                    <p>Masuk ke panel administrator SPK Bantuan Sosial</p>
                </div>

                {{-- Alerts --}}
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show mb-3">
                        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show mb-3">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ $errors->first() }}
                        <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <form action="{{ route('admin.login') }}" method="POST" autocomplete="off">
                    @csrf

                    {{-- Email --}}
                    <div class="mb-3">
                        <label for="email" class="form-label">
                            <i class="bi bi-envelope me-1" style="color:var(--accent);"></i>Email
                        </label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                            <input type="email"
                                   id="email"
                                   name="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email') }}"
                                   placeholder="admin@email.com"
                                   autocomplete="email"
                                   required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Password --}}
                    <div class="mb-3">
                        <label for="password" class="form-label">
                            <i class="bi bi-lock me-1" style="color:var(--accent);"></i>Password
                        </label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock"></i></span>
                            <input type="password"
                                   id="password"
                                   name="password"
                                   class="form-control @error('password') is-invalid @enderror"
                                   placeholder="Masukkan password"
                                   autocomplete="current-password"
                                   required>
                            <button class="toggle-eye btn" type="button" id="togglePassword">
                                <i class="bi bi-eye" id="eyeIcon"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Remember --}}
                    <div class="mb-4 d-flex align-items-center justify-content-between">
                        <div class="form-check mb-0">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember">
                            <label class="form-check-label" for="remember" style="font-size:.8rem; color:#64748B;">
                                Ingat saya
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="btn-login">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Masuk Sebagai Admin
                    </button>
                </form>

                <div class="divider-text">
                    <span>atau</span>
                </div>
                <div class="text-center">
                    <a href="{{ route('user.login') }}" style="font-size:.78rem; color:var(--secondary); font-weight:600; text-decoration:none;">
                        <i class="bi bi-people me-1"></i>Login sebagai Masyarakat
                    </a>
                </div>
            </div>

            <div class="text-center mt-3" style="font-size:.72rem; color:#94A3B8;">
                &copy; {{ date('Y') }} SPK Bantuan Sosial — Kelurahan Harjamukti
            </div>
        </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.getElementById('togglePassword').addEventListener('click', function () {
        const pwd  = document.getElementById('password');
        const icon = document.getElementById('eyeIcon');
        if (pwd.type === 'password') {
            pwd.type = 'text';
            icon.classList.replace('bi-eye', 'bi-eye-slash');
        } else {
            pwd.type = 'password';
            icon.classList.replace('bi-eye-slash', 'bi-eye');
        }
    });
</script>
</body>
</html>