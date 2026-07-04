<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun — SPK Bantuan Sosial</title>
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
        /* LEFT */
        .auth-left {
            flex: 1;
            background: linear-gradient(145deg, var(--primary) 0%, #152C49 50%, var(--secondary) 100%);
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
            width: 400px; height: 400px;
            border-radius: 50%;
            background: rgba(82,183,136,.08);
            top: -100px; right: -100px;
        }
        .auth-left::after {
            content: '';
            position: absolute;
            width: 300px; height: 300px;
            border-radius: 50%;
            background: rgba(249,199,79,.06);
            bottom: -80px; left: -80px;
        }
        .auth-pattern {
            position: absolute; inset: 0;
            background-image: radial-gradient(circle, rgba(255,255,255,.03) 1px, transparent 1px);
            background-size: 28px 28px;
        }
        .auth-brand { position: relative; z-index: 1; text-align: center; }
        .auth-logo-circle {
            width: 100px; height: 100px;
            border-radius: 50%;
            background: rgba(255,255,255,.1);
            border: 2px solid rgba(255,255,255,.15);
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 1.25rem;
            position: relative;
        }
        .auth-logo-circle::before {
            content: ''; position: absolute; inset: -6px;
            border-radius: 50%;
            border: 2px dashed rgba(249,199,79,.3);
            animation: spin 20s linear infinite;
        }
        @keyframes spin { to { transform: rotate(360deg); } }
        .auth-logo-icon { font-size: 2.8rem; color: var(--gold); }
        .auth-brand-title {
            font-size: .88rem; font-weight: 700;
            color: rgba(255,255,255,.65);
            letter-spacing: 2.5px; text-transform: uppercase;
            margin-bottom: .4rem;
        }
        .auth-brand-name {
            font-size: 1.8rem; font-weight: 900; color: #fff;
            line-height: 1.1; margin-bottom: .4rem;
        }
        .auth-brand-name span { color: var(--gold); }
        .auth-brand-sub { font-size: .78rem; color: rgba(255,255,255,.45); }
        .auth-gold-line {
            width: 50px; height: 3px;
            background: linear-gradient(90deg, var(--gold), var(--accent));
            border-radius: 2px; margin: 1rem auto;
        }
        .auth-steps { position: relative; z-index: 1; margin-top: 2.5rem; width: 100%; max-width: 300px; }
        .auth-step {
            display: flex; align-items: flex-start; gap: .75rem;
            padding: .6rem 0;
        }
        .step-num {
            width: 28px; height: 28px;
            border-radius: 50%;
            background: var(--gold);
            color: var(--primary);
            display: flex; align-items: center; justify-content: center;
            font-size: .72rem; font-weight: 800;
            flex-shrink: 0;
        }
        .step-text { font-size: .75rem; color: rgba(255,255,255,.65); font-weight: 500; padding-top: .3rem; }

        /* RIGHT */
        .auth-right {
            width: 500px;
            display: flex; align-items: flex-start; justify-content: center;
            padding: 2rem 2rem;
            background: #F0F4F8;
            overflow-y: auto;
        }
        .auth-form-wrap { width: 100%; max-width: 400px; padding: 1.5rem 0; }
        .auth-card {
            background: #fff;
            border-radius: 20px;
            padding: 1.75rem 2rem;
            box-shadow: 0 8px 32px rgba(30,58,95,.12);
            border: 1px solid #E2E8F0;
        }
        .auth-card-header { text-align: center; margin-bottom: 1.5rem; }
        .auth-card-header .header-icon {
            width: 52px; height: 52px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto .85rem;
            font-size: 1.4rem; color: #fff;
            box-shadow: 0 4px 14px rgba(30,58,95,.28);
        }
        .auth-card-header h2 { font-size: 1.25rem; font-weight: 800; color: var(--primary); margin: 0 0 .25rem; }
        .auth-card-header p { font-size: .75rem; color: #64748B; margin: 0; }

        /* Field section labels */
        .field-section {
            font-size: .65rem;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: var(--accent);
            margin: 1rem 0 .6rem;
            display: flex; align-items: center; gap: .5rem;
        }
        .field-section::after {
            content: ''; flex: 1; height: 1px; background: #E2E8F0;
        }

        .form-label { font-size: .79rem; font-weight: 600; color: #374151; margin-bottom: .3rem; }
        .form-control, .form-select {
            font-size: .84rem;
            border: 1.5px solid #E2E8F0;
            border-radius: 10px;
            padding: .55rem .85rem;
            transition: all .2s;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(30,58,95,.12);
        }
        .form-control.is-invalid { border-color: #DC2626; }
        .invalid-feedback { font-size: .73rem; }
        .input-group-text {
            background: #F8FAFC; border: 1.5px solid #E2E8F0;
            color: #94A3B8; border-radius: 10px 0 0 10px;
        }
        .input-group .form-control { border-left: none; border-radius: 0 10px 10px 0; }
        .input-group:focus-within .input-group-text { border-color: var(--primary); color: var(--primary); }
        .input-group:focus-within .form-control { border-color: var(--primary); }
        .toggle-eye {
            background: #F8FAFC; border: 1.5px solid #E2E8F0;
            border-left: none; border-radius: 0 10px 10px 0;
            color: #94A3B8; cursor: pointer; transition: all .2s;
        }
        .toggle-eye:hover { color: var(--primary); }

        .btn-register {
            width: 100%;
            padding: .7rem;
            font-size: .88rem;
            font-weight: 700;
            border-radius: 10px;
            border: none;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: #fff;
            transition: all .25s;
            box-shadow: 0 4px 14px rgba(30,58,95,.28);
        }
        .btn-register:hover {
            background: linear-gradient(135deg, #2A4F7A, #3D8B6A);
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(30,58,95,.35);
        }
        .alert { border-radius: 10px; font-size: .8rem; border: none; padding: .75rem 1rem; }
        .alert-danger { background: #FEF2F2; color: #991B1B; border-left: 4px solid #DC2626; }
        .form-text { font-size: .71rem; color: #94A3B8; }

        @media (max-width: 768px) {
            .auth-left { display: none; }
            .auth-right { width: 100%; padding: 1rem; }
        }
    </style>
</head>
<body>

    {{-- Left Panel --}}
    <div class="auth-left">
        <div class="auth-pattern"></div>
        <div class="auth-brand">
            <div class="auth-logo-circle">
                <i class="bi bi-person-plus-fill auth-logo-icon"></i>
            </div>
            <div class="auth-brand-title">Sistem Pendukung Keputusan</div>
            <div class="auth-brand-name"><span>Daftar</span> Akun</div>
            <div class="auth-gold-line"></div>
            <div class="auth-brand-sub">Kelurahan Harjamukti · Kota Cirebon</div>
        </div>
        <div class="auth-steps">
            <div class="auth-step">
                <div class="step-num">1</div>
                <div class="step-text">Isi data diri sesuai KTP (NIK & nama lengkap)</div>
            </div>
            <div class="auth-step">
                <div class="step-num">2</div>
                <div class="step-text">Masukkan email aktif & buat password yang kuat</div>
            </div>
            <div class="auth-step">
                <div class="step-num">3</div>
                <div class="step-text">Login dan ajukan permohonan bantuan sosial</div>
            </div>
            <div class="auth-step">
                <div class="step-num">4</div>
                <div class="step-text">Pantau status seleksi dan hasil kelayakan Anda</div>
            </div>
        </div>
    </div>

    {{-- Right Panel --}}
    <div class="auth-right">
        <div class="auth-form-wrap">
            <div class="auth-card">
                <div class="auth-card-header">
                    <div class="header-icon">
                        <i class="bi bi-person-plus-fill"></i>
                    </div>
                    <h2>Buat Akun Baru</h2>
                    <p>Daftar untuk mengajukan bantuan sosial secara online</p>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show mb-3">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <ul class="mb-0 ps-3 mt-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <form action="{{ route('user.register') }}" method="POST">
                    @csrf

                    <div class="field-section">Data Identitas</div>

                    <div class="row g-2">
                        {{-- NIK --}}
                        <div class="col-12">
                            <label for="nik" class="form-label">
                                NIK <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-card-text"></i></span>
                                <input type="text"
                                       id="nik"
                                       name="nik"
                                       class="form-control @error('nik') is-invalid @enderror"
                                       value="{{ old('nik') }}"
                                       placeholder="16 digit NIK KTP"
                                       maxlength="16"
                                       inputmode="numeric"
                                       pattern="\d{16}"
                                       required>
                                @error('nik')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-text">Sesuai KTP, tepat 16 digit angka</div>
                        </div>

                        {{-- Nama Lengkap --}}
                        <div class="col-12">
                            <label for="name" class="form-label">
                                Nama Lengkap <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-person"></i></span>
                                <input type="text"
                                       id="name"
                                       name="name"
                                       class="form-control @error('name') is-invalid @enderror"
                                       value="{{ old('name') }}"
                                       placeholder="Nama sesuai KTP"
                                       maxlength="100"
                                       required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="field-section">Akun & Keamanan</div>

                    {{-- Email --}}
                    <div class="mb-3">
                        <label for="email" class="form-label">
                            Email <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                            <input type="email"
                                   id="email"
                                   name="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email') }}"
                                   placeholder="email@contoh.com"
                                   autocomplete="email"
                                   required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row g-2">
                        {{-- Password --}}
                        <div class="col-12 col-sm-6">
                            <label for="password" class="form-label">
                                Password <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                <input type="password"
                                       id="password"
                                       name="password"
                                       class="form-control @error('password') is-invalid @enderror"
                                       placeholder="Min. 6 karakter"
                                       required>
                                <button class="toggle-eye btn" type="button" id="togglePassword">
                                    <i class="bi bi-eye" id="eyeIcon"></i>
                                </button>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Konfirmasi Password --}}
                        <div class="col-12 col-sm-6">
                            <label for="password_confirmation" class="form-label">
                                Konfirmasi <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                                <input type="password"
                                       id="password_confirmation"
                                       name="password_confirmation"
                                       class="form-control"
                                       placeholder="Ulangi password"
                                       required>
                                <button class="toggle-eye btn" type="button" id="toggleConfirm">
                                    <i class="bi bi-eye" id="eyeIconConfirm"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn-register">
                            <i class="bi bi-person-check me-2"></i>Daftar Sekarang
                        </button>
                    </div>

                    <p class="text-center mb-0 mt-3" style="font-size:.8rem; color:#64748B;">
                        Sudah punya akun?
                        <a href="{{ route('user.login') }}" style="color:var(--secondary); font-weight:700; text-decoration:none;">
                            Login di sini
                        </a>
                    </p>
                </form>
            </div>

            <div class="text-center mt-3" style="font-size:.72rem; color:#94A3B8;">
                &copy; {{ date('Y') }} SPK Bantuan Sosial — Kelurahan Harjamukti
            </div>
        </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function toggleVis(btnId, inputId, iconId) {
        document.getElementById(btnId).addEventListener('click', function () {
            const input = document.getElementById(inputId);
            const icon  = document.getElementById(iconId);
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('bi-eye', 'bi-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('bi-eye-slash', 'bi-eye');
            }
        });
    }
    toggleVis('togglePassword', 'password', 'eyeIcon');
    toggleVis('toggleConfirm',  'password_confirmation', 'eyeIconConfirm');

    // NIK — angka saja
    document.getElementById('nik').addEventListener('input', function () {
        this.value = this.value.replace(/\D/g, '');
    });
</script>
</body>
</html>