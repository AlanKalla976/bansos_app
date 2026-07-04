<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sistem Penerimaan & Monitoring Bantuan Sosial (AHP-MOORA)</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Sora:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg: #F8FAFC;
            --surface: #FFFFFF;
            --ink: #0F172A;
            --ink-soft: #475569;
            --accent: #2563EB;
            --accent-hi: #1D4ED8;
            --accent-bg: #EFF6FF;
            --emerald: #10B981;
            --emerald-bg: #D1FAE5;
            --indigo: #6366F1;
            --indigo-bg: #EEF2FF;
            --border: #E2E8F0;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        html { scroll-behavior: smooth; }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--bg);
            color: var(--ink);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
        }

        /* ── HEADER NAVIGATION ── */
        header {
            position: sticky;
            top: 0;
            z-index: 100;
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(16px);
            border-bottom: 1px solid var(--border);
            padding: 0 4rem;
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: all 0.3s;
        }

        .logo-container {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            color: var(--ink);
        }

        .logo-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: linear-gradient(135deg, var(--accent), var(--indigo));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-family: 'Sora', sans-serif;
            font-size: 1.25rem;
            box-shadow: 0 4px 10px rgba(37, 99, 235, 0.25);
        }

        .logo-text {
            font-family: 'Sora', sans-serif;
            font-weight: 700;
            font-size: 1.15rem;
            letter-spacing: -0.03em;
        }

        .logo-text span {
            color: var(--accent);
        }

        .nav-menu {
            display: flex;
            align-items: center;
            gap: 2rem;
        }

        .nav-link {
            color: var(--ink-soft);
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
            transition: color 0.2s;
        }

        .nav-link:hover {
            color: var(--accent);
        }

        .nav-btn-group {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .btn {
            padding: 8px 20px;
            border-radius: 8px;
            font-size: 0.875rem;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.2s;
            font-family: inherit;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-ghost {
            background: transparent;
            color: var(--ink-soft);
            border: 1px solid transparent;
        }

        .btn-ghost:hover {
            color: var(--ink);
            background: #F1F5F9;
        }

        .btn-outline {
            background: var(--surface);
            color: var(--ink);
            border: 1px solid var(--border);
        }

        .btn-outline:hover {
            border-color: #CBD5E1;
            background: #F8FAFC;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--accent), var(--indigo));
            color: white;
            border: none;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, var(--accent-hi), var(--indigo));
            box-shadow: 0 6px 16px rgba(37, 99, 235, 0.3);
            transform: translateY(-1px);
        }

        .btn-secondary {
            background: var(--emerald);
            color: white;
            border: none;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
        }

        .btn-secondary:hover {
            background: #0D9488;
            box-shadow: 0 6px 16px rgba(16, 185, 129, 0.3);
            transform: translateY(-1px);
        }

        /* ── HERO SECTION ── */
        .hero-section {
            padding: 8rem 4rem 6rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 4rem;
            max-width: 1400px;
            margin: 0 auto;
            width: 100%;
        }

        .hero-content {
            flex: 1.1;
            max-width: 650px;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 16px;
            border-radius: 100px;
            background: var(--accent-bg);
            border: 1px solid #BFDBFE;
            color: var(--accent);
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            margin-bottom: 2rem;
            box-shadow: var(--shadow-sm);
        }

        .hero-badge .pulse {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--accent);
            animation: pulse-animation 2s infinite;
        }

        @keyframes pulse-animation {
            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(37, 99, 235, 0.7); }
            70% { transform: scale(1); box-shadow: 0 0 0 8px rgba(37, 99, 235, 0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(37, 99, 235, 0); }
        }

        .hero-content h1 {
            font-family: 'Sora', sans-serif;
            font-size: clamp(2.5rem, 4.5vw, 3.5rem);
            font-weight: 700;
            line-height: 1.15;
            letter-spacing: -0.03em;
            margin-bottom: 1.5rem;
        }

        .hero-content h1 span {
            background: linear-gradient(135deg, var(--accent), var(--indigo));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-description {
            font-size: 1.05rem;
            color: var(--ink-soft);
            line-height: 1.75;
            margin-bottom: 2.5rem;
        }

        .hero-btn-group {
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
        }

        .btn-lg {
            padding: 14px 32px;
            font-size: 0.95rem;
            border-radius: 10px;
        }

        .hero-illustration {
            flex: 0.9;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
        }

        .illustration-card {
            background: var(--surface);
            border-radius: 24px;
            border: 1px solid var(--border);
            padding: 2.5rem;
            width: 100%;
            max-width: 480px;
            box-shadow: var(--shadow-lg);
            position: relative;
            z-index: 10;
        }

        .illustration-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border);
        }

        .illustration-title {
            font-family: 'Sora', sans-serif;
            font-weight: 600;
            font-size: 1rem;
        }

        .status-badge {
            padding: 4px 12px;
            border-radius: 100px;
            font-size: 0.75rem;
            font-weight: 700;
        }

        .status-verified {
            background: var(--emerald-bg);
            color: var(--emerald);
        }

        .list-item-ill {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.25rem;
            background: #F8FAFC;
            padding: 12px 16px;
            border-radius: 12px;
            border: 1px solid var(--border);
        }

        .list-item-ill:last-child {
            margin-bottom: 0;
        }

        .item-label {
            font-size: 0.85rem;
            color: var(--ink-soft);
            font-weight: 500;
        }

        .item-val {
            font-size: 0.85rem;
            font-weight: 700;
            color: var(--ink);
        }

        .item-val.rank {
            background: var(--accent);
            color: white;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
        }

        .floating-badge {
            position: absolute;
            background: var(--surface);
            border: 1px solid var(--border);
            padding: 12px 20px;
            border-radius: 16px;
            box-shadow: var(--shadow-md);
            display: flex;
            align-items: center;
            gap: 10px;
            z-index: 20;
        }

        .fb-1 {
            top: -20px;
            left: -20px;
            border-left: 4px solid var(--accent);
        }

        .fb-2 {
            bottom: -15px;
            right: -10px;
            border-left: 4px solid var(--indigo);
        }

        /* ── STATS SECTION ── */
        .stats-section {
            background: var(--surface);
            border-top: 1px solid var(--border);
            border-bottom: 1px solid var(--border);
            padding: 4rem;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .stat-card {
            text-align: center;
        }

        .stat-num {
            font-family: 'Sora', sans-serif;
            font-size: 2.75rem;
            font-weight: 700;
            color: var(--accent);
            margin-bottom: 6px;
            background: linear-gradient(135deg, var(--accent), var(--indigo));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .stat-title {
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--ink);
            margin-bottom: 4px;
        }

        .stat-desc {
            font-size: 0.8rem;
            color: var(--ink-soft);
        }

        /* ── METHODOLOGY SECTION ── */
        .methodology-section {
            padding: 6rem 4rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .sec-header {
            text-align: center;
            max-width: 600px;
            margin: 0 auto 4rem;
        }

        .sec-header h2 {
            font-family: 'Sora', sans-serif;
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .sec-header p {
            color: var(--ink-soft);
            line-height: 1.6;
        }

        .method-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 2.5rem;
        }

        .method-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 2.5rem;
            box-shadow: var(--shadow-sm);
            transition: all 0.3s;
        }

        .method-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-md);
        }

        .method-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .mi-ahp { background: var(--accent-bg); color: var(--accent); }
        .mi-moora { background: var(--indigo-bg); color: var(--indigo); }
        .mi-mon { background: var(--emerald-bg); color: var(--emerald); }

        .method-card h3 {
            font-family: 'Sora', sans-serif;
            font-weight: 600;
            font-size: 1.1rem;
            margin-bottom: 0.75rem;
        }

        .method-card p {
            font-size: 0.875rem;
            color: var(--ink-soft);
            line-height: 1.6;
        }

        /* ── FOOTER ── */
        footer {
            background: var(--surface);
            border-top: 1px solid var(--border);
            padding: 3rem 4rem 2rem;
            margin-top: auto;
        }

        .footer-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 2rem;
            border-bottom: 1px solid var(--border);
        }

        .footer-logo {
            display: flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            color: var(--ink);
            font-family: 'Sora', sans-serif;
            font-weight: 700;
        }

        .footer-logo-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--accent);
        }

        .footer-links {
            display: flex;
            gap: 2rem;
        }

        .footer-link {
            color: var(--ink-soft);
            text-decoration: none;
            font-size: 0.85rem;
            transition: color 0.2s;
        }

        .footer-link:hover {
            color: var(--accent);
        }

        .footer-bottom {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.8rem;
            color: var(--ink-soft);
        }

        /* ── RESPONSIVE DESIGN ── */
        @media (max-width: 1024px) {
            header { padding: 0 2rem; }
            .hero-section { padding: 6rem 2rem 4rem; flex-direction: column; text-align: center; }
            .hero-content { max-width: 100%; }
            .hero-btn-group { justify-content: center; }
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
            .method-grid { grid-template-columns: 1fr; }
            footer { padding: 2rem 2rem 1.5rem; }
            .footer-content { flex-direction: column; gap: 1.5rem; }
        }

        @media (max-width: 640px) {
            header { flex-direction: column; height: auto; padding: 1rem; gap: 1rem; }
            .nav-menu { display: none; }
            .stats-grid { grid-template-columns: 1fr; }
            .footer-bottom { flex-direction: column; gap: 10px; text-align: center; }
        }
    </style>
</head>
<body>

    <!-- ── NAV HEADER ── -->
    <header>
        <a href="/" class="logo-container">
            <div class="logo-icon">B</div>
            <div class="logo-text">Bansos<span>AHP-MOORA</span></div>
        </a>

        <div class="nav-menu">
            <a href="#cara-kerja" class="nav-link">Cara Kerja</a>
            <a href="#kriteria" class="nav-link">Metode Seleksi</a>
        </div>

        <div class="nav-btn-group">
            @auth
                <a href="{{ route('user.dashboard') }}" class="btn btn-outline">Dashboard</a>
            @else
                <a href="{{ route('user.register') }}" class="btn btn-ghost">Daftar Warga</a>
                <a href="{{ route('user.login') }}" class="btn btn-primary">Login Warga</a>
            @endauth
        </div>
    </header>

    <!-- ── HERO SECTION ── -->
    <section class="hero-section">
        <div class="hero-content">
            <div class="hero-badge">
                <span class="pulse"></span>
                Sistem Pendukung Keputusan
            </div>
            <h1>Penyaluran Bansos <span>Lebih Adil</span> dan <span>Tepat Sasaran</span></h1>
            <p class="hero-description">
                Sistem penentuan kelayakan penerima bantuan sosial menggunakan perpaduan ilmiah metode <strong>AHP</strong> untuk merancang bobot kriteria secara konsisten, serta <strong>MOORA</strong> untuk melakukan pemeringkatan secara objektif, transparan, dan efisien.
            </p>
            <div class="hero-btn-group">
                @auth
                    <a href="{{ route('user.dashboard') }}" class="btn btn-lg btn-primary">Buka Dashboard Anda</a>
                @else
                    <a href="{{ route('user.login') }}" class="btn btn-lg btn-primary">Ajukan Bantuan</a>
                    <a href="{{ route('admin.login') }}" class="btn btn-lg btn-outline">Portal Administrator →</a>
                @endauth
            </div>
        </div>

        <div class="hero-illustration">
            <div class="floating-badge fb-1">
                ⚖️ <strong>AHP:</strong> Bobot Kriteria Konsisten
            </div>
            
            <div class="illustration-card">
                <div class="illustration-header">
                    <span class="illustration-title">Penerimaan Bansos</span>
                    <span class="status-badge status-verified">Terhitung MOORA</span>
                </div>
                <div class="illustration-body">
                    <div class="list-item-ill">
                        <span class="item-label">Alternatif 1 (Budi)</span>
                        <span class="item-val">Yi = 0.5280</span>
                        <span class="item-val rank">1</span>
                    </div>
                    <div class="list-item-ill">
                        <span class="item-label">Alternatif 2 (Ani)</span>
                        <span class="item-val">Yi = 0.4920</span>
                        <span class="item-val rank">2</span>
                    </div>
                    <div class="list-item-ill">
                        <span class="item-label">Alternatif 3 (Joko)</span>
                        <span class="item-val">Yi = 0.3150</span>
                        <span class="item-val" style="color:var(--ink-soft)">Rank 3</span>
                    </div>
                </div>
            </div>

            <div class="floating-badge fb-2">
                📈 <strong>MOORA:</strong> Optimasi Tepat
            </div>
        </div>
    </section>

    <!-- ── STATS SECTION ── -->
    <section class="stats-section">
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-num">100%</div>
                <div class="stat-title">Transparan</div>
                <div class="stat-desc">Perhitungan berbasis rumus keputusan ilmiah</div>
            </div>
            <div class="stat-card">
                <div class="stat-num">AHP</div>
                <div class="stat-title">Bobot Kriteria</div>
                <div class="stat-desc">Mencegah subyektifitas pemilihan kriteria penilaian</div>
            </div>
            <div class="stat-card">
                <div class="stat-num">MOORA</div>
                <div class="stat-title">Optimasi Cepat</div>
                <div class="stat-desc">Menghasilkan rangking kelayakan secara objektif</div>
            </div>
            <div class="stat-card">
                <div class="stat-num">Laravel 12</div>
                <div class="stat-title">Modern & Aman</div>
                <div class="stat-desc">Dibangun dengan arsitektur teknologi handal</div>
            </div>
        </div>
    </section>

    <!-- ── METHODOLOGY SECTION ── -->
    <section class="methodology-section" id="cara-kerja">
        <div class="sec-header">
            <h2>Alur Proses Sistem</h2>
            <p>Sistem ini dirancang untuk mewujudkan transparansi dan keakuratan data penerima manfaat melalui tiga pilar tahapan utama.</p>
        </div>

        <div class="method-grid">
            <div class="method-card">
                <div class="method-icon mi-ahp">1</div>
                <h3>Pengajuan & Berkas Warga</h3>
                <p>Warga melakukan registrasi dan mengunggah berkas persyaratan wajib (KTP, KK, Foto Rumah, dll.) serta menginput parameter kriteria kemiskinan yang sebenarnya.</p>
            </div>
            <div class="method-card">
                <div class="method-icon mi-moora">2</div>
                <h3>Kalkulasi Bobot Kriteria AHP</h3>
                <p>Sistem merancang bobot kriteria penilaian (Penghasilan, Tanggungan, Kondisi Rumah, Aset) melalui pembobotan matriks perbandingan berpasangan (Pairwise) Saaty untuk memastikan konsistensi logika.</p>
            </div>
            <div class="method-card">
                <div class="method-icon mi-mon">3</div>
                <h3>Optimasi & Rangking MOORA</h3>
                <p>Melalui proses normalisasi dan perkalian bobot kriteria, sistem MOORA menyajikan rangking kelayakan penerima bansos secara objektif (Layak/Tidak Layak) dan memonitoring progres penyaluran.</p>
            </div>
        </div>
    </section>

    <!-- ── FOOTER ── -->
    <footer>
        <div class="footer-content">
            <a href="/" class="footer-logo">
                <span class="footer-logo-dot"></span>
                Bansos AHP-MOORA
            </a>
            <div class="footer-links">
                <a href="#cara-kerja" class="footer-link">Alur Sistem</a>
                <a href="{{ route('admin.login') }}" class="footer-link">Portal Admin</a>
            </div>
        </div>
        <div class="footer-bottom">
            <span>&copy; {{ date('Y') }} Sistem Penentuan & Monitoring Bantuan Sosial. Seluruh Hak Cipta Dilindungi.</span>
            <span>Laravel 12 &middot; SPK Penentuan Bansos</span>
        </div>
    </footer>

</body>
</html>