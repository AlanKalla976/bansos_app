<aside class="sidebar" id="sidebar">

    {{-- ── Brand ── --}}
    <a href="{{ route('admin.dashboard') }}" class="sidebar-brand">
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
        <a href="{{ route('admin.dashboard') }}"
           class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2 nav-icon"></i>
            Dashboard
        </a>

        <div class="nav-section">
            <div class="nav-section-label">Data Master</div>
        </div>
        <a href="{{ route('admin.akunmasyarakat.index') }}"
           class="nav-link {{ request()->routeIs('admin.akunmasyarakat*') ? 'active' : '' }}">
            <i class="bi bi-people-fill nav-icon"></i>
            Akun Masyarakat
        </a>
        <a href="{{ route('admin.bantuansosial.index') }}"
           class="nav-link {{ request()->routeIs('admin.bantuansosial*') ? 'active' : '' }}">
            <i class="bi bi-gift-fill nav-icon"></i>
            Jenis Bantuan
        </a>
        <a href="{{ route('admin.kriteria.index') }}"
           class="nav-link {{ request()->routeIs('admin.kriteria*') ? 'active' : '' }}">
            <i class="bi bi-list-check nav-icon"></i>
            Kriteria
        </a>
        <a href="{{ route('admin.subkriteria.index') }}"
           class="nav-link {{ request()->routeIs('admin.subkriteria*') ? 'active' : '' }}">
            <i class="bi bi-diagram-3-fill nav-icon"></i>
            Sub Kriteria
        </a>

        <div class="nav-section">
            <div class="nav-section-label">Pengajuan</div>
        </div>
        <a href="{{ route('admin.pengajuan.index') }}"
           class="nav-link {{ request()->routeIs('admin.pengajuan*') ? 'active' : '' }}">
            <i class="bi bi-file-earmark-text-fill nav-icon"></i>
            Data Pengajuan
        </a>

        <div class="nav-section">
            <div class="nav-section-label">Penilaian & DSS</div>
        </div>
        <a href="{{ route('admin.penilaian.index') }}"
           class="nav-link {{ request()->routeIs('admin.penilaian*') ? 'active' : '' }}">
            <i class="bi bi-clipboard-data-fill nav-icon"></i>
            Input Penilaian
        </a>

        <div class="nav-section">
            <div class="nav-section-label">Hasil Akhir</div>
        </div>
        <a href="{{ route('admin.hasilakhir.index') }}"
           class="nav-link {{ request()->routeIs('admin.hasilakhir*') ? 'active' : '' }}">
            <i class="bi bi-trophy-fill nav-icon"></i>
            Hasil Kelayakan
        </a>

    </nav>

    {{-- ── Footer ── --}}
    <div class="sidebar-footer">
        <div class="sidebar-user">
            <div class="sidebar-avatar">
                {{ strtoupper(substr(Auth::guard('admin')->user()->full_name ?? 'A', 0, 1)) }}
            </div>
            <div class="sidebar-user-info">
                <div class="sidebar-user-name">
                    {{ Auth::guard('admin')->user()->full_name ?? 'Administrator' }}
                </div>
                <div class="sidebar-user-role">Administrator</div>
            </div>
            <form method="POST" action="{{ route('admin.logout') }}" class="ms-auto">
                @csrf
                <button type="submit" class="sidebar-logout-btn" title="Logout">
                    <i class="bi bi-box-arrow-right"></i>
                </button>
            </form>
        </div>
    </div>

</aside>