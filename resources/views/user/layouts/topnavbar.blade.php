<header class="topbar" id="topbar">
    <div class="topbar-left">
        <button class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle sidebar" style="display:none;">
            <i class="bi bi-list"></i>
        </button>
        <div>
            <h1 class="page-title">@yield('page-title', 'Dashboard')</h1>
            <nav aria-label="breadcrumb" class="d-none d-sm-block">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('user.dashboard') }}" style="color:var(--text-muted); text-decoration:none; font-size:.72rem;">
                            <i class="bi bi-house me-1"></i>Beranda
                        </a>
                    </li>
                    <li class="breadcrumb-item active" style="font-size:.72rem;">
                        @yield('breadcrumb', 'Dashboard')
                    </li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="topbar-right">
        <span class="d-none d-lg-flex align-items-center gap-1" style="font-size:.75rem; color:var(--text-muted);">
            <i class="bi bi-calendar3" style="color:var(--accent);"></i>
            {{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMM Y') }}
        </span>

        <a href="{{ route('user.pengajuan.index') }}" class="topbar-icon-btn" title="Pengajuan Saya">
            <i class="bi bi-bell-fill"></i>
        </a>

        <div class="dropdown">
            <div class="topbar-profile" data-bs-toggle="dropdown" aria-expanded="false">
                <div class="topbar-avatar">
                    {{ strtoupper(substr(Auth::user()->name ?? 'W', 0, 1)) }}
                </div>
                <span class="topbar-username d-none d-sm-block">
                    {{ Auth::user()->name ?? 'Pengguna' }}
                </span>
                <i class="bi bi-chevron-down d-none d-sm-block" style="font-size:.65rem; color:var(--text-muted);"></i>
            </div>
            <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3 p-2 mt-1" style="min-width:200px;">
                <li class="px-2 py-1 mb-1">
                    <div style="font-weight:700; font-size:.82rem; color:var(--secondary);">
                        {{ Auth::user()->name ?? 'Pengguna' }}
                    </div>
                    <div style="font-size:.72rem; color:var(--text-muted);">
                        {{ Auth::user()->email ?? '' }}
                    </div>
                </li>
                <li><hr class="dropdown-divider my-1"></li>
                <li>
                    <a href="{{ route('user.dashboard') }}" class="dropdown-item rounded-2 py-2" style="font-size:.82rem;">
                        <i class="bi bi-speedometer2 me-2" style="color:var(--accent);"></i>Dashboard
                    </a>
                </li>
                <li>
                    <a href="{{ route('user.profile.index') }}" class="dropdown-item rounded-2 py-2" style="font-size:.82rem;">
                        <i class="bi bi-person-circle me-2" style="color:var(--accent);"></i>Profil Saya
                    </a>
                </li>
                <li>
                    <a href="{{ route('user.pengajuan.index') }}" class="dropdown-item rounded-2 py-2" style="font-size:.82rem;">
                        <i class="bi bi-file-earmark-text me-2" style="color:var(--accent);"></i>Pengajuan Saya
                    </a>
                </li>
                <li><hr class="dropdown-divider my-1"></li>
                <li>
                    <form method="POST" action="{{ route('user.logout') }}" class="px-0">
                        @csrf
                        <button type="submit" class="dropdown-item rounded-2 py-2" style="font-size:.82rem; color:#DC2626; border:none; background:none; width:100%; text-align:left; cursor:pointer;">
                            <i class="bi bi-box-arrow-right me-2"></i>Keluar
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</header>