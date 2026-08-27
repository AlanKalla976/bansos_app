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
                        <a href="{{ route('admin.dashboard') }}" style="color:var(--text-muted); text-decoration:none; font-size:.72rem;">
                            <i class="bi bi-house me-1"></i>Admin
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
        <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3 p-2" style="min-width:280px; margin-top:.5rem;">
            <li class="px-2 pb-2">
                <div class="d-flex align-items-center justify-content-between">
                    <span style="font-weight:700; font-size:.85rem; color:var(--primary);">Notifikasi</span>
                    <span class="badge rounded-pill" style="background:var(--primary); font-size:.65rem;">Baru</span>
                </div>
            </li>
            <li><hr class="dropdown-divider my-1"></li>
            <li>
                <a href="{{ route('admin.pengajuan.index') }}" class="dropdown-item rounded-2 py-2">
                    <div class="d-flex align-items-start gap-2">
                        <div style="width:32px; height:32px; border-radius:8px; background:#DBEAFE; color:var(--primary); display:flex; align-items:center; justify-content:center; font-size:.85rem; flex-shrink:0;">
                            <i class="bi bi-file-earmark-text-fill"></i>
                        </div>
                        <div>
                            <div style="font-size:.78rem; font-weight:600; color:var(--text);">Pengajuan Menunggu</div>
                            <div style="font-size:.7rem; color:var(--text-muted);">Ada pengajuan yang perlu diverifikasi</div>
                        </div>
                    </div>
                </a>
            </li>
            <li><hr class="dropdown-divider my-1"></li>
            <li>
                <a href="{{ route('admin.pengajuan.index') }}" class="dropdown-item text-center rounded-2 py-2" style="font-size:.75rem; color:var(--primary); font-weight:600;">
                    Lihat semua notifikasi
                </a>
            </li>
        </ul>

        @php
            if (request()->is('admin/petugas') || request()->is('admin/petugas/*')) {
                $currentUser = Auth::guard('petugas')->user();
            } elseif (request()->is('admin/lurah') || request()->is('admin/lurah/*')) {
                $currentUser = Auth::guard('lurah')->user();
            } else {
                $currentUser = Auth::guard('admin')->user();
            }
        @endphp

        <div class="dropdown">
            <div class="topbar-profile" data-bs-toggle="dropdown" aria-expanded="false">
                <div class="topbar-avatar">
                    {{ strtoupper(substr($currentUser->name ?? 'A', 0, 1)) }}
                </div>
                <span class="topbar-username d-none d-sm-block">
                    {{ $currentUser->name ?? 'Staff' }}
                </span>
                <i class="bi bi-chevron-down d-none d-sm-block" style="font-size:.65rem; color:var(--text-muted);"></i>
            </div>
            <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3 p-2 mt-1" style="min-width:200px;">
                <li class="px-2 py-1 mb-1">
                    <div style="font-weight:700; font-size:.82rem; color:var(--primary);">
                        {{ $currentUser->name ?? 'Staff' }}
                    </div>
                    <div style="font-size:.72rem; color:var(--text-muted);">
                        {{ $currentUser->email ?? '' }}
                    </div>
                </li>
                <li><hr class="dropdown-divider my-1"></li>
                @php
                    $dashboardRoute = route('admin.dashboard');
                    if ($currentUser) {
                        if ($currentUser->role === 'petugas') {
                            $dashboardRoute = route('admin.petugas.dashboard');
                        } elseif ($currentUser->role === 'lurah') {
                            $dashboardRoute = route('admin.lurah.dashboard');
                        }
                    }
                @endphp
                <li>
                    <a href="{{ $dashboardRoute }}" class="dropdown-item rounded-2 py-2" style="font-size:.82rem;">
                        <i class="bi bi-speedometer2 me-2" style="color:var(--accent);"></i>Dashboard
                    </a>
                </li>
                <li><hr class="dropdown-divider my-1"></li>
                <li>
                    <form method="POST" action="{{ route('admin.logout') }}" class="px-0">
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