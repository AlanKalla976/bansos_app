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

        @php
            if (request()->is('admin/petugas') || request()->is('admin/petugas/*')) {
                $currentUser = Auth::guard('petugas')->user();
            } elseif (request()->is('admin/lurah') || request()->is('admin/lurah/*')) {
                $currentUser = Auth::guard('lurah')->user();
            } else {
                $currentUser = Auth::guard('admin')->user();
            }

            $dashboardRoute = route('admin.dashboard');
            if ($currentUser) {
                if ($currentUser->role === 'petugas') {
                    $dashboardRoute = route('admin.petugas.dashboard');
                } elseif ($currentUser->role === 'lurah') {
                    $dashboardRoute = route('admin.lurah.dashboard');
                }
            }
        @endphp

        <div class="nav-section">
            <div class="nav-section-label">Utama</div>
        </div>
        <a href="{{ $dashboardRoute }}"
           class="nav-link {{ request()->routeIs('admin.dashboard') || request()->routeIs('admin.petugas.dashboard') || request()->routeIs('admin.lurah.dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2 nav-icon"></i>
            Dashboard
        </a>

        @if($currentUser && $currentUser->role === 'admin')
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
        @endif

        @if($currentUser && $currentUser->role === 'petugas')
        @php
            $menungguCount = \App\Models\Pengajuan::where('status', 'Menunggu')->count();
            $belumDijadwalkanCount = \App\Models\Penyaluran::where('status', 'Belum Dijadwalkan')->count();
        @endphp
        <div class="nav-section">
            <div class="nav-section-label">Tugas Petugas</div>
        </div>
        <a href="{{ route('admin.petugas.validasi.index') }}"
           class="nav-link {{ request()->routeIs('admin.petugas.validasi*') ? 'active' : '' }}">
            <i class="bi bi-file-earmark-check-fill nav-icon"></i>
            Validasi Berkas
            @if($menungguCount > 0)
                <span class="badge bg-warning text-dark ms-auto" style="font-size:.65rem; padding:.2rem .5rem; border-radius:10px;">
                    {{ $menungguCount }}
                </span>
            @endif
        </a>
        <a href="{{ route('admin.petugas.penyaluran.index') }}"
           class="nav-link {{ request()->routeIs('admin.petugas.penyaluran*') ? 'active' : '' }}">
            <i class="bi bi-calendar-check-fill nav-icon"></i>
            Jadwal Penyaluran
            @if($belumDijadwalkanCount > 0)
                <span class="badge bg-info text-dark ms-auto" style="font-size:.65rem; padding:.2rem .5rem; border-radius:10px;">
                    {{ $belumDijadwalkanCount }}
                </span>
            @endif
        </a>
        @php
            $belumDimonitoringCount = \App\Models\Penyaluran::where('status', 'Sudah Diambil')->whereDoesntHave('monitoring')->count();
        @endphp
        <a href="{{ route('admin.petugas.penyaluran.index', ['status' => 'Sudah Dijadwalkan']) }}"
           class="nav-link {{ request()->routeIs('admin.petugas.penyaluran*') && request('status') === 'Sudah Dijadwalkan' ? 'active' : '' }}">
            <i class="bi bi-card-checklist nav-icon"></i>
            Realisasi Pengambilan
        </a>
        <a href="{{ route('admin.petugas.monitoring.index') }}"
           class="nav-link {{ request()->routeIs('admin.petugas.monitoring*') ? 'active' : '' }}">
            <i class="bi bi-eye-fill nav-icon"></i>
            Monitoring Penyaluran
            @if($belumDimonitoringCount > 0)
                <span class="badge bg-warning text-dark ms-auto" style="font-size:.65rem; padding:.2rem .5rem; border-radius:10px;">
                    {{ $belumDimonitoringCount }}
                </span>
            @endif
        </a>
        @endif

        @if($currentUser && $currentUser->role === 'admin')
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
        @endif

        @if($currentUser && $currentUser->role === 'lurah')
        @php
            $menungguPersetujuanCount = \App\Models\HasilAkhir::where('persetujuan_status', 'Menunggu Persetujuan')->count();
        @endphp
        <div class="nav-section">
            <div class="nav-section-label">Tugas Lurah</div>
        </div>
        <a href="{{ route('admin.lurah.persetujuan.index') }}"
           class="nav-link {{ request()->routeIs('admin.lurah.persetujuan*') ? 'active' : '' }}">
            <i class="bi bi-person-check-fill nav-icon"></i>
            Persetujuan Penerima
            @if($menungguPersetujuanCount > 0)
                <span class="badge bg-warning text-dark ms-auto" style="font-size:.65rem; padding:.2rem .5rem; border-radius:10px;">
                    {{ $menungguPersetujuanCount }}
                </span>
            @endif
        </a>
        <a href="{{ route('admin.lurah.monitoring.index') }}"
           class="nav-link {{ request()->routeIs('admin.lurah.monitoring*') ? 'active' : '' }}">
            <i class="bi bi-graph-up nav-icon"></i>
            Laporan Monitoring
        </a>
        @endif

        @if($currentUser && $currentUser->role === 'admin')
        <div class="nav-section">
            <div class="nav-section-label">Hasil Akhir</div>
        </div>
        <a href="{{ route('admin.hasilakhir.index') }}"
           class="nav-link {{ request()->routeIs('admin.hasilakhir*') ? 'active' : '' }}">
            <i class="bi bi-trophy-fill nav-icon"></i>
            Hasil Kelayakan
        </a>
        @endif

    </nav>

    {{-- ── Footer ── --}}
    <div class="sidebar-footer">
        <div class="sidebar-user">
            <div class="sidebar-avatar">
                {{ strtoupper(substr($currentUser->name ?? 'A', 0, 1)) }}
            </div>
            <div class="sidebar-user-info">
                <div class="sidebar-user-name">
                    {{ $currentUser->name ?? 'Staff' }}
                </div>
                <div class="sidebar-user-role">{{ ucfirst($currentUser->role ?? '') }}</div>
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