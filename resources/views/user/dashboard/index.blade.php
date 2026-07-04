@extends('user.layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('breadcrumb', 'Dashboard')

@push('styles')
<style>
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
    <div class="welcome-banner-content">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div>
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span style="display:inline-flex; align-items:center; gap:.4rem; background:rgba(249,199,79,.2); border:1px solid rgba(249,199,79,.3); color:var(--gold); padding:.25rem .7rem; border-radius:20px; font-size:.72rem; font-weight:700;">
                        <i class="bi bi-circle-fill" style="font-size:.4rem;"></i>Akun Aktif
                    </span>
                </div>
                <h5 class="fw-bold mb-1" style="font-size:1.3rem;">
                    Selamat datang, {{ $user->name }}! 👋
                </h5>
                <p class="mb-0" style="font-size:.83rem; color:rgba(255,255,255,.65);">
                    Pantau status pengajuan bantuan sosial Anda di sini.<br>
                    <span style="font-size:.75rem; opacity:.5;">
                        {{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM Y') }}
                    </span>
                </p>
            </div>
            <img src="{{ asset('images/logo-pemkot.png') }}"
                 alt="Logo"
                 style="width:64px; height:64px; object-fit:contain; opacity:.25; filter:brightness(10);"
                 class="d-none d-md-block">
        </div>
    </div>
</div>

{{-- Stat Cards --}}
<div class="row g-3 mb-4">
    @php
        $statCards = [
            ['blue',   'bi-file-earmark-text-fill', $totalPengajuan,      'Total Pengajuan'],
            ['yellow', 'bi-hourglass-split',         $pengajuanMenunggu,   'Menunggu Verifikasi'],
            ['green',  'bi-check-circle-fill',       $pengajuanVerifikasi, 'Diverifikasi'],
            ['red',    'bi-x-circle-fill',           $pengajuanDitolak,    'Ditolak'],
        ];
    @endphp
    @foreach($statCards as [$cls, $icon, $val, $lbl])
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-icon {{ $cls }}">
                <i class="bi {{ $icon }}"></i>
            </div>
            <div>
                <div class="stat-val">{{ $val }}</div>
                <div class="stat-lbl">{{ $lbl }}</div>
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- Riwayat Pengajuan --}}
<div class="page-card">
    <div class="card-head">
        <h6 class="card-head-title">
            <i class="bi bi-clock-history"></i>Riwayat Pengajuan
        </h6>
        <span style="background:#F1F5F9; color:#64748B; font-size:.68rem; font-weight:600; padding:.25rem .75rem; border-radius:20px;">
            {{ $riwayatPengajuan->count() }} pengajuan
        </span>
    </div>
    <div class="card-body-inner p-0">
        @if($riwayatPengajuan->isEmpty())
        <div class="empty-state" style="padding:3rem;">
            <i class="bi bi-inbox" style="font-size:2rem;"></i>
            <p>Belum ada pengajuan tercatat.</p>
        </div>
        @else
        <div class="table-responsive">
            <table class="table align-middle mb-0 tbl-navy">
                <thead>
                    <tr>
                        <th style="width:50px;">No</th>
                        <th>Tanggal</th>
                        <th>Jenis Bantuan</th>
                        <th>Status Verifikasi</th>
                        <th>Kelengkapan Dokumen</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($riwayatPengajuan as $i => $p)
                    @php
                        $st     = $p->status ?? 'Menunggu';
                        $stMap  = [
                            'Menunggu'     => ['bg'=>'#FEF3C7', 'color'=>'#92400E'],
                            'Diverifikasi' => ['bg'=>'#D1FAE5', 'color'=>'#065F46'],
                            'Ditolak'      => ['bg'=>'#FEE2E2', 'color'=>'#991B1B'],
                            'Diterima'     => ['bg'=>'#DBEAFE', 'color'=>'#1E3A8A'],
                        ];
                        $stStyle = $stMap[$st] ?? ['bg'=>'#F1F5F9', 'color'=>'#64748B'];
                    @endphp
                    <tr>
                        <td style="color:#94A3B8; font-size:.78rem;">{{ $i + 1 }}</td>
                        <td style="color:#64748B; font-size:.8rem;">
                            <i class="bi bi-calendar3 me-1"></i>
                            {{ \Carbon\Carbon::parse($p->created_at)->format('d M Y') }}
                        </td>
                        <td>
                            <span style="background:#EFF6FF; color:#1E3A5F; font-size:.7rem; font-weight:700; padding:.25rem .75rem; border-radius:20px; display:inline-block;">
                                {{ $p->bantuanSosial->nama_bantuan ?? '-' }}
                            </span>
                        </td>
                        <td>
                            <span style="background:{{ $stStyle['bg'] }}; color:{{ $stStyle['color'] }}; font-size:.7rem; font-weight:700; padding:.28rem .75rem; border-radius:20px; display:inline-block;">
                                {{ $st }}
                            </span>
                        </td>
                        <td style="color:#64748B; font-size:.8rem;">
                            {{ $p->kelengkapan_dokumen ?? '-' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>

@endsection