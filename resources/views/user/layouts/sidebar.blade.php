<aside class="sidebar" id="sidebar">

    {{-- ── Brand ── --}}
    <a href="{{ route('user.dashboard') }}" class="sidebar-brand">
        <div class="sidebar-logo">
            <img src="{{ asset('images/logo-pemkot.png') }}"
                 alt="Logo Pemkot Cirebon"
                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
            <div class="logo-fallback">
                <i class="bi bi-house-heart-fill"></i>
            </div>
        </div>
        <div class="brand-text">
            <span class="brand-name">SPK BANSOS</span>
            <span class="brand-sub">Kel. Harjamukti</span>
        </div>
    </a>

    {{-- ── Navigation ── --}}
    <nav class="sidebar-nav">

        <div class="nav-section">
            <div class="nav-section-label">Utama</div>
        </div>
        <a href="{{ route('user.dashboard') }}"
           class="nav-link {{ request()->routeIs('user.dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2 nav-icon"></i>
            Dashboard
        </a>

        <div class="nav-section">
            <div class="nav-section-label">Pengajuan</div>
        </div>
        <a href="{{ route('user.pengajuan.index') }}"
           class="nav-link {{ request()->routeIs('user.pengajuan.index') ? 'active' : '' }}">
            <i class="bi bi-plus-circle-fill nav-icon"></i>
            Ajukan Bantuan
        </a>

        <div class="nav-section">
            <div class="nav-section-label">Hasil Seleksi</div>
        </div>
        <a href="{{ route('user.hasilakhir.index') }}"
           class="nav-link {{ request()->routeIs('user.hasilakhir*') ? 'active' : '' }}">
            <i class="bi bi-trophy-fill nav-icon"></i>
            Hasil Kelayakan
        </a>

        <div class="nav-section">
            <div class="nav-section-label">Akun Saya</div>
        </div>
        <a href="{{ route('user.profile.index') }}"
           class="nav-link {{ request()->routeIs('user.profile*') ? 'active' : '' }}">
            <i class="bi bi-person-circle nav-icon"></i>
            Profil Saya
        </a>

    </nav>

    {{-- ── Footer ── --}}
    <div class="sidebar-footer">
        <div class="sidebar-user">
            <div class="sidebar-avatar">
                {{ strtoupper(substr(Auth::user()->name ?? 'W', 0, 1)) }}
            </div>
            <div class="sidebar-user-info">
                <div class="sidebar-user-name">{{ Auth::user()->name ?? 'Pengguna' }}</div>
                <div class="sidebar-user-role">Warga Masyarakat</div>
            </div>
            <form method="POST" action="{{ route('user.logout') }}" class="ms-auto">
                @csrf
                <button type="submit" class="sidebar-logout-btn" title="Logout">
                    <i class="bi bi-box-arrow-right"></i>
                </button>
            </form>
        </div>
    </div>

</aside>