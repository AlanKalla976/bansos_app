@extends('admin.layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('breadcrumb', 'Dashboard')

@push('styles')
<style>
.welcome-banner {
    background: linear-gradient(135deg, var(--primary) 0%, #0F2540 55%, var(--secondary-dark) 100%);
    border-radius: var(--radius-lg); padding: 1.75rem 2rem;
    color: #fff; position: relative; overflow: hidden;
    margin-bottom: 1.5rem; box-shadow: var(--shadow-md);
}
.welcome-banner::before {
    content: ''; position: absolute;
    width: 350px; height: 350px; border-radius: 50%;
    background: rgba(255,255,255,.04); right: -80px; top: -120px;
}
.welcome-banner::after {
    content: ''; position: absolute;
    width: 200px; height: 200px; border-radius: 50%;
    background: rgba(249,199,79,.06); right: 120px; bottom: -60px;
}
.welcome-pattern {
    position: absolute; inset: 0;
    background-image: radial-gradient(circle, rgba(255,255,255,.025) 1px, transparent 1px);
    background-size: 24px 24px;
}
.welcome-content { position: relative; z-index: 1; }
.welcome-title   { font-size: 1.4rem; font-weight: 800; margin: 0 0 .35rem; }
.welcome-sub     { font-size: .83rem; color: rgba(255,255,255,.65); margin: 0; }
.welcome-badge {
    display: inline-flex; align-items: center; gap: .4rem;
    background: rgba(249,199,79,.2); border: 1px solid rgba(249,199,79,.3);
    color: var(--gold); padding: .3rem .75rem; border-radius: 20px;
    font-size: .72rem; font-weight: 700; margin-bottom: .75rem; letter-spacing: .5px;
}
.info-row {
    display: flex; justify-content: space-between; align-items: center;
    padding: .6rem 0; border-bottom: 1px solid #F1F5F9; font-size: .82rem;
}
.info-row:last-child { border-bottom: none; }
.info-label { color: var(--text-muted); }
.info-value {
    font-weight: 600; color: var(--text); max-width: 160px;
    text-align: right; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.status-online {
    display: inline-flex; align-items: center; gap: .35rem;
    background: #D1FAE5; color: #065F46; border-radius: 20px;
    padding: .2rem .65rem; font-size: .7rem; font-weight: 700;
}
.tbl-navy thead tr,
.tbl-navy thead tr th {
    background: #1E3A5F !important;
    color: #fff !important;
    font-size: .75rem !important;
    font-weight: 600 !important;
    letter-spacing: .4px;
    padding: .85rem 1rem !important;
    border: none !important;
    border-bottom: none !important;
}
.tbl-navy tbody tr { border-bottom: 1px solid #F1F5F9 !important; transition: background .15s; }
.tbl-navy tbody tr:last-child { border-bottom: none !important; }
.tbl-navy tbody tr:hover td { background: #F8FAFC !important; }
.tbl-navy tbody td {
    padding: .85rem 1rem !important;
    border: none !important;
    border-top: none !important;
    vertical-align: middle !important;
}
</style>
@endpush

@section('content')

{{-- Welcome Banner --}}
<div class="welcome-banner">
    <div class="welcome-pattern"></div>
    <div class="welcome-content">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div>
                <div class="welcome-badge">
                    <i class="bi bi-circle-fill" style="font-size:.45rem;"></i>Sistem Aktif
                </div>
                <h2 class="welcome-title">Selamat datang, {{ $admin->full_name }} ({{ ucfirst($admin->role) }})! 👋</h2>
                <p class="welcome-sub">
                    Sistem Pendukung Keputusan Bantuan Sosial — Kelurahan Harjamukti<br>
                    <span style="font-size:.75rem; opacity:.5;">
                        {{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM Y') }}
                    </span>
                </p>
            </div>
            <img src="{{ asset('images/logo-pemkot.png') }}"
                 alt="Logo"
                 style="width:72px; height:72px; object-fit:contain; opacity:.25; filter:brightness(10);"
                 class="d-none d-md-block">
        </div>
    </div>
</div>

@if($admin->role === 'petugas')
{{-- ====================================================================== --}}
{{-- ── DASHBOARD PETUGAS ── --}}
{{-- ====================================================================== --}}
<h4 class="fw-bold mb-3 text-primary"><i class="bi bi-briefcase-fill me-2"></i>Ringkasan Tugas Petugas</h4>

<div class="row g-3 mb-4">
    {{-- Aspek Validasi --}}
    <div class="col-md-4">
        <div class="stat-card border-warning">
            <div class="stat-icon orange"><i class="bi bi-hourglass-split"></i></div>
            <div>
                <div class="stat-val text-warning">{{ number_format($stats['menunggu_validasi']) }}</div>
                <div class="stat-lbl">Menunggu Validasi</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card border-success">
            <div class="stat-icon green"><i class="bi bg-success bi-patch-check-fill text-white"></i></div>
            <div>
                <div class="stat-val text-success">{{ number_format($stats['pengajuan_valid']) }}</div>
                <div class="stat-lbl">Pengajuan Valid (Diverifikasi)</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card border-danger">
            <div class="stat-icon red"><i class="bi bi-x-circle-fill"></i></div>
            <div>
                <div class="stat-val text-danger">{{ number_format($stats['pengajuan_tidak_valid']) }}</div>
                <div class="stat-lbl">Pengajuan Tidak Valid (Ditolak)</div>
            </div>
        </div>
    </div>
</div>

<h5 class="fw-bold mb-3 text-primary"><i class="bi bi-calendar3 me-2"></i>Status Penjadwalan & Penyaluran</h5>
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="stat-card border-warning">
            <div class="stat-icon yellow"><i class="bi bi-calendar-x"></i></div>
            <div>
                <div class="stat-val text-warning">{{ number_format($stats['belum_dijadwalkan']) }}</div>
                <div class="stat-lbl">Belum Dijadwalkan</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card border-info">
            <div class="stat-icon blue"><i class="bi bi-calendar-check"></i></div>
            <div>
                <div class="stat-val text-info">{{ number_format($stats['sudah_dijadwalkan']) }}</div>
                <div class="stat-lbl">Sudah Dijadwalkan</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card border-success">
            <div class="stat-icon green"><i class="bi bi-bag-check"></i></div>
            <div>
                <div class="stat-val text-success">{{ number_format($stats['bantuan_diambil']) }}</div>
                <div class="stat-lbl">Bantuan Sudah Diambil</div>
            </div>
        </div>
    </div>
</div>

<h5 class="fw-bold mb-3 text-primary"><i class="bi bi-bar-chart-fill me-2"></i>Statistik Ketepatan Penyaluran</h5>
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="stat-card border-success">
            <div class="stat-icon green"><i class="bi bi-clock-fill"></i></div>
            <div>
                <div class="stat-val text-success">{{ number_format($stats['tepat_waktu']) }}</div>
                <div class="stat-lbl">Tepat Waktu</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card border-danger">
            <div class="stat-icon red"><i class="bi bi-clock-history"></i></div>
            <div>
                <div class="stat-val text-danger">{{ number_format($stats['terlambat']) }}</div>
                <div class="stat-lbl">Terlambat</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card border-success">
            <div class="stat-icon green"><i class="bi bi-person-check-fill"></i></div>
            <div>
                <div class="stat-val text-success">{{ number_format($stats['sesuai_sasaran']) }}</div>
                <div class="stat-lbl">Sesuai Sasaran</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card border-danger">
            <div class="stat-icon red"><i class="bi bi-person-x-fill"></i></div>
            <div>
                <div class="stat-val text-danger">{{ number_format($stats['tidak_sesuai']) }}</div>
                <div class="stat-lbl">Tidak Sesuai Sasaran</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-12">
        <div class="page-card">
            <div class="card-head">
                <h6 class="card-head-title"><i class="bi bi-info-circle-fill"></i> Tautan Navigasi Petugas</h6>
            </div>
            <div class="card-body-inner">
                <div class="d-flex gap-3">
                    <a href="{{ route('admin.petugas.validasi.index') }}" class="btn btn-warning flex-fill py-3">
                        <i class="bi bi-file-earmark-check-fill fs-5"></i> Validasi Berkas Pengajuan
                    </a>
                    <a href="{{ route('admin.petugas.penyaluran.index') }}" class="btn btn-primary flex-fill py-3">
                        <i class="bi bi-calendar-event fs-5"></i> Atur Jadwal Penyaluran
                    </a>
                    <a href="{{ route('admin.petugas.monitoring.index') }}" class="btn btn-success flex-fill py-3">
                        <i class="bi bi-eye-fill fs-5"></i> Evaluasi & Monitoring Dampak
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@elseif($admin->role === 'lurah')
{{-- ====================================================================== --}}
{{-- ── DASHBOARD LURAH ── --}}
{{-- ====================================================================== --}}
<h4 class="fw-bold mb-3 text-primary"><i class="bi bi-person-check-fill me-2"></i>Persetujuan Calon Penerima Bantuan</h4>
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="stat-card border-primary">
            <div class="stat-icon navy"><i class="bi bi-people-fill"></i></div>
            <div>
                <div class="stat-val text-primary">{{ number_format($stats['total_calon']) }}</div>
                <div class="stat-lbl">Total Calon Penerima</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card border-warning">
            <div class="stat-icon gold"><i class="bi bi-hourglass-split"></i></div>
            <div>
                <div class="stat-val text-warning">{{ number_format($stats['menunggu_setuju']) }}</div>
                <div class="stat-lbl">Menunggu Persetujuan</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card border-success">
            <div class="stat-icon green"><i class="bi bi-check-circle-fill"></i></div>
            <div>
                <div class="stat-val text-success">{{ number_format($stats['disetujui']) }}</div>
                <div class="stat-lbl">Disetujui</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card border-danger">
            <div class="stat-icon red"><i class="bi bi-x-circle-fill"></i></div>
            <div>
                <div class="stat-val text-danger">{{ number_format($stats['ditolak']) }}</div>
                <div class="stat-lbl">Ditolak</div>
            </div>
        </div>
    </div>
</div>

<h5 class="fw-bold mb-3 text-primary"><i class="bi bi-graph-up me-2"></i>Monitoring & Ketepatan Penyaluran</h5>
<div class="row g-3 mb-4">
    <div class="col-md-3 col-6">
        <div class="stat-card">
            <div class="stat-icon blue"><i class="bi bi-bag-check-fill"></i></div>
            <div>
                <div class="stat-val">{{ number_format($stats['total_penyaluran']) }}</div>
                <div class="stat-lbl">Total Penyaluran</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="stat-card">
            <div class="stat-icon green"><i class="bi bi-check-lg"></i></div>
            <div>
                <div class="stat-val text-success">{{ number_format($stats['tepat_waktu']) }}</div>
                <div class="stat-lbl">Tepat Waktu</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="stat-card">
            <div class="stat-icon red"><i class="bi bi-clock-history"></i></div>
            <div>
                <div class="stat-val text-danger">{{ number_format($stats['terlambat']) }}</div>
                <div class="stat-lbl">Terlambat</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="stat-card">
            <div class="stat-icon green"><i class="bi bi-person-check-fill"></i></div>
            <div>
                <div class="stat-val text-success">{{ number_format($stats['sesuai_sasaran']) }}</div>
                <div class="stat-lbl">Sesuai Sasaran</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    {{-- Chart Ringkasan Dampak --}}
    <div class="col-lg-6">
        <div class="page-card h-100">
            <div class="card-head">
                <h6 class="card-head-title"><i class="bi bi-heart-fill text-danger"></i> Ringkasan Dampak Bantuan</h6>
            </div>
            <div class="card-body-inner">
                <div style="position:relative; height:240px;">
                    <canvas id="chartDampak"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Info Box navigasi --}}
    <div class="col-lg-6">
        <div class="page-card h-100">
            <div class="card-head">
                <h6 class="card-head-title"><i class="bi bi-info-circle-fill"></i> Tautan Navigasi Lurah</h6>
            </div>
            <div class="card-body-inner">
                <p class="text-muted">Sebagai Lurah Kelurahan Harjamukti, Anda memiliki wewenang untuk meninjau data perankingan kelayakan (MOORA) dan memberikan persetujuan final.</p>
                <div class="d-grid gap-2 mt-4">
                    <a href="{{ route('admin.lurah.persetujuan.index') }}" class="btn btn-primary btn-lg py-3">
                        <i class="bi bi-person-check-fill me-2 fs-5"></i> Masuk Menu Persetujuan Penerima
                    </a>
                    <a href="{{ route('admin.lurah.monitoring.index') }}" class="btn btn-outline-primary btn-lg py-3">
                        <i class="bi bi-graph-up me-2 fs-5"></i> Masuk Menu Laporan Monitoring
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@else
{{-- ====================================================================== --}}
{{-- ── DASHBOARD ADMIN (Default) ── --}}
{{-- ====================================================================== --}}
<div class="row g-3 mb-4">
    @php
        $cards = [
            ['icon'=>'bi-people-fill',           'cls'=>'navy',   'val'=>$totalMasyarakat, 'lbl'=>'Total Masyarakat'],
            ['icon'=>'bi-file-earmark-text-fill', 'cls'=>'blue',   'val'=>$totalPengajuan,  'lbl'=>'Total Pengajuan'],
            ['icon'=>'bi-basket-fill',            'cls'=>'teal',   'val'=>$totalBPNT,       'lbl'=>'Total BPNT'],
            ['icon'=>'bi-cash-coin',              'cls'=>'orange', 'val'=>$totalBLT,        'lbl'=>'Total BLT'],
            ['icon'=>'bi-house-heart-fill',       'cls'=>'purple', 'val'=>$totalPKH,        'lbl'=>'Total PKH'],
            ['icon'=>'bi-check-circle-fill',      'cls'=>'green',  'val'=>$totalLayak,      'lbl'=>'Total Layak'],
            ['icon'=>'bi-x-circle-fill',          'cls'=>'red',    'val'=>$totalTidakLayak, 'lbl'=>'Tidak Layak'],
        ];
    @endphp
    @foreach($cards as $c)
    <div class="col-6 col-sm-4 col-xl">
        <div class="stat-card">
            <div class="stat-icon {{ $c['cls'] }}">
                <i class="bi {{ $c['icon'] }}"></i>
            </div>
            <div>
                <div class="stat-val">{{ number_format($c['val']) }}</div>
                <div class="stat-lbl">{{ $c['lbl'] }}</div>
            </div>
        </div>
    </div>
    @endforeach
</div>

<div class="page-card mb-4">
    <div class="card-head">
        <h6 class="card-head-title">
            <i class="bi bi-bar-chart-line-fill"></i>Monitoring Penerima Bantuan
        </h6>
        <form method="GET" action="{{ route('admin.dashboard') }}" class="d-flex gap-2 align-items-center flex-wrap">
            <select name="tahun" class="form-select form-select-sm rounded-3"
                    style="width:auto; font-size:.8rem;" onchange="this.form.submit()">
                @foreach($tahunList as $t)
                    <option value="{{ $t }}" @selected($filterTahun == $t)>{{ $t }}</option>
                @endforeach
            </select>
            <select name="jenis_bantuan" class="form-select form-select-sm rounded-3"
                    style="width:auto; font-size:.8rem;" onchange="this.form.submit()">
                <option value="semua" @selected($filterBantuan == 'semua')>Semua Bantuan</option>
                @foreach($jenisBantuanList as $bantuan)
                    <option value="{{ $bantuan->id }}" @selected($filterBantuan == $bantuan->id)>
                        {{ $bantuan->nama_bantuan }}
                    </option>
                @endforeach
            </select>
        </form>
    </div>
    <div class="card-body-inner">
        @if(count($chartData['labels']) > 0)
        <div style="position:relative; height:260px;">
            <canvas id="chartMonitoring"></canvas>
        </div>
        <div class="d-flex gap-4 justify-content-center mt-3 flex-wrap" style="font-size:.74rem;">
            <span class="d-flex align-items-center gap-1">
                <span style="display:inline-block; width:14px; height:14px; border-radius:4px; background:#2D6A4F;"></span> Layak
            </span>
            <span class="d-flex align-items-center gap-1">
                <span style="display:inline-block; width:14px; height:14px; border-radius:4px; background:#DC2626;"></span> Tidak Layak
            </span>
        </div>
        @else
        <div class="empty-state">
            <i class="bi bi-bar-chart-line"></i>
            <p>Belum ada data hasil akhir untuk ditampilkan.</p>
            <a href="{{ route('admin.penilaian.index') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-calculator me-1"></i>Mulai Penilaian
            </a>
        </div>
        @endif
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="page-card h-100">
            <div class="card-head">
                <h6 class="card-head-title">
                    <i class="bi bi-trophy-fill"></i>5 Hasil Seleksi Terbaru
                </h6>
                <a href="{{ route('admin.hasilakhir.index') }}" class="btn btn-outline-primary btn-sm rounded-3">
                    <i class="bi bi-arrow-right me-1"></i>Lihat Semua
                </a>
            </div>
            <div class="table-responsive">
                <table class="table align-middle mb-0 tbl-navy">
                    <thead>
                        <tr>
                            <th style="width:40px;">No</th>
                            <th>Nama Pemohon</th>
                            <th>Jenis Bantuan</th>
                            <th>Total Skor</th>
                            <th style="text-align:center;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($hasilTerbaru as $i => $h)
                        @php
                            $kuota     = $h->pengajuan->bantuanSosial->kuota ?? 0;
                            $isLayak   = $h->ranking <= $kuota;
                        @endphp
                        <tr>
                            <td style="color:#94A3B8; font-size:.78rem;">{{ $i + 1 }}</td>
                            <td style="font-weight:600; color:#1E293B; font-size:.83rem;">
                                {{ $h->pengajuan->user->name ?? $h->pengajuan->nama ?? '-' }}
                            </td>
                            <td>
                                <span style="background:#EFF6FF; color:#1E3A5F; font-size:.7rem; font-weight:700; padding:.25rem .75rem; border-radius:20px; display:inline-block;">
                                    {{ $h->pengajuan->bantuanSosial->nama_bantuan ?? '-' }}
                                </span>
                            </td>
                            <td style="font-family:monospace; font-weight:700; font-size:.78rem; color:#2D6A4F;">
                                {{ number_format($h->nilai_yi, 3) }}
                            </td>
                            <td style="text-align:center;">
                                @if($isLayak)
                                    <span style="background:#D1FAE5; color:#065F46; font-size:.7rem; font-weight:700; padding:.28rem .75rem; border-radius:20px; display:inline-block;">
                                        Layak
                                    </span>
                                @else
                                    <span style="background:#FEE2E2; color:#991B1B; font-size:.7rem; font-weight:700; padding:.28rem .75rem; border-radius:20px; display:inline-block;">
                                        Tidak Layak
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5">
                                <div class="empty-state" style="padding:2rem;">
                                    <i class="bi bi-inbox" style="font-size:2rem;"></i>
                                    <p>Belum ada hasil seleksi.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="page-card h-100">
            <div class="card-head">
                <h6 class="card-head-title">
                    <i class="bi bi-info-circle-fill"></i>Info Sistem
                </h6>
            </div>
            <div class="card-body-inner">
                @foreach([
                    ['Versi Aplikasi', '1.0.0'],
                    ['Metode DSS',     'AHP + MOORA'],
                    ['Framework',      'Laravel 10'],
                    ['Bootstrap',      '5.3.3'],
                    ['Login sebagai',  $admin->email],
                ] as [$lbl, $val])
                <div class="info-row">
                    <span class="info-label">{{ $lbl }}</span>
                    <span class="info-value" title="{{ $val }}">{{ $val }}</span>
                </div>
                @endforeach
                <div class="info-row">
                    <span class="info-label">Status Sistem</span>
                    <span class="status-online">
                        <i class="bi bi-circle-fill" style="font-size:.4rem;"></i>Online
                    </span>
                </div>

                <div class="mt-3 d-grid gap-2">
                    <a href="{{ route('admin.pengajuan.index') }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-file-earmark-text me-1"></i>Kelola Pengajuan
                    </a>
                    <a href="{{ route('admin.penilaian.index') }}" class="btn btn-success btn-sm">
                        <i class="bi bi-calculator me-1"></i>Proses Penilaian
                    </a>
                    <a href="{{ route('admin.hasilakhir.export-excel') }}" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-file-earmark-excel me-1"></i>Export Excel
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
@if($admin->role === 'lurah')
(function () {
    const ctx = document.getElementById('chartDampak');
    if (!ctx) return;
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Sangat Membantu', 'Membantu', 'Cukup Membantu', 'Tidak Membantu'],
            datasets: [{
                data: [
                    {{ $stats['dampak_sangat'] }},
                    {{ $stats['dampak_membantu'] }},
                    {{ $stats['dampak_cukup'] }},
                    {{ $stats['dampak_tidak'] }}
                ],
                backgroundColor: [
                    '#059669', // green
                    '#0284C7', // blue
                    '#F59E0B', // orange/yellow
                    '#DC2626'  // red
                ],
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        boxWidth: 12,
                        font: { size: 11 }
                    }
                }
            }
        }
    });
})();
@endif

@if($admin->role === 'admin' && count($chartData['labels']) > 0)
(function () {
    const ctx = document.getElementById('chartMonitoring');
    if (!ctx) return;
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: @json($chartData['labels']),
            datasets: [
                {
                    label: 'Layak',
                    data: @json($chartData['layak']),
                    backgroundColor: 'rgba(45,106,79,.80)',
                    borderRadius: 8, borderSkipped: false,
                },
                {
                    label: 'Tidak Layak',
                    data: @json($chartData['tidakLayak']),
                    backgroundColor: 'rgba(220,38,38,.70)',
                    borderRadius: 8, borderSkipped: false,
                }
            ]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1E3A5F',
                    titleColor: '#fff', bodyColor: 'rgba(255,255,255,.8)',
                    padding: 10, cornerRadius: 8,
                    callbacks: { label: ctx => ` ${ctx.dataset.label}: ${ctx.parsed.y} orang` }
                }
            },
            scales: {
                x: { grid: { display: false }, ticks: { font: { size: 11 }, color: '#64748B' } },
                y: { beginAtZero: true, ticks: { stepSize: 1, font: { size: 11 }, color: '#64748B' }, grid: { color: '#F1F5F9' } }
            }
        }
    });
})();
@endif
</script>
@endpush