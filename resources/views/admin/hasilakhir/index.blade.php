@extends('admin.layouts.app')

@section('title', 'Hasil Akhir')
@section('page-title', 'Hasil Akhir')
@section('breadcrumb', 'Hasil Akhir')

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

<div class="page-header">
    <div>
        <h2 class="page-header-title">
            <i class="bi bi-trophy-fill me-2" style="color:var(--accent);"></i>
            Hasil Akhir Kelayakan
        </h2>
        <p class="page-header-sub">Hasil perhitungan MOORA — diurutkan berdasarkan ranking</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.penilaian.index') }}" class="btn btn-outline-primary">
            <i class="bi bi-calculator me-1"></i>Menu Penilaian
        </a>
        <a href="{{ route('admin.hasilakhir.export-excel', request()->only(['jenis_bantuan','status','search'])) }}"
           class="btn btn-success">
            <i class="bi bi-file-earmark-excel me-1"></i>Excel
        </a>
        <a href="{{ route('admin.hasilakhir.export-pdf', request()->only(['jenis_bantuan','status','search'])) }}"
           class="btn btn-danger">
            <i class="bi bi-file-earmark-pdf me-1"></i>PDF
        </a>
    </div>
</div>

{{-- Stat Cards --}}
<div class="row g-3 mb-4">
    @php
        $minis = [
            ['cls'=>'navy',  'icon'=>'bi-people-fill',       'val'=>$total,           'lbl'=>'Total Peserta'],
            ['cls'=>'green', 'icon'=>'bi-check-circle-fill', 'val'=>$totalLayak,      'lbl'=>'Layak'],
            ['cls'=>'red',   'icon'=>'bi-x-circle-fill',     'val'=>$totalTidakLayak, 'lbl'=>'Tidak Layak'],
        ];
    @endphp
    @foreach($minis as $m)
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-icon {{ $m['cls'] }}">
                <i class="bi {{ $m['icon'] }}"></i>
            </div>
            <div>
                <div class="stat-val">{{ number_format($m['val']) }}</div>
                <div class="stat-lbl">{{ $m['lbl'] }}</div>
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- Filter Bar --}}
<div class="filter-bar mb-3">
    <form method="GET" action="{{ route('admin.hasilakhir.index') }}">
        <div class="row g-2 align-items-end">
            <div class="col-12 col-md-4">
                <label class="form-label mb-1" style="font-size:.78rem; font-weight:600;">
                    <i class="bi bi-search me-1" style="color:var(--accent);"></i>Cari Nama / NIK
                </label>
                <input type="text" name="search" class="form-control form-control-sm"
                       value="{{ request('search') }}" placeholder="Ketik nama atau NIK...">
            </div>
            <div class="col-6 col-md-3">
                <label class="form-label mb-1" style="font-size:.78rem; font-weight:600;">
                    <i class="bi bi-basket me-1" style="color:var(--accent);"></i>Jenis Bantuan
                </label>
                <select name="jenis_bantuan" class="form-select form-select-sm">
                    <option value="">Semua Bantuan</option>
                    @foreach($jenisBantuanList as $id => $nama)
                        <option value="{{ $id }}" @selected(request('jenis_bantuan') == $id)>
                            {{ $nama }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label mb-1" style="font-size:.78rem; font-weight:600;">
                    <i class="bi bi-funnel me-1" style="color:var(--accent);"></i>Status
                </label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">Semua Status</option>
                    <option value="Layak"       @selected(request('status') === 'Layak')>Layak</option>
                    <option value="Tidak Layak" @selected(request('status') === 'Tidak Layak')>Tidak Layak</option>
                </select>
            </div>
            <div class="col-12 col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm flex-fill">
                    <i class="bi bi-search me-1"></i>Filter
                </button>
                <a href="{{ route('admin.hasilakhir.index') }}"
                   class="btn btn-sm" style="background:#F1F5F9; color:var(--text-muted); border:1px solid var(--border);">
                    <i class="bi bi-x-circle me-1"></i>Reset
                </a>
            </div>
        </div>
    </form>
</div>

{{-- Tabel Hasil --}}
<div class="page-card">
    <div class="card-head">
        <h6 class="card-head-title">
            <i class="bi bi-table"></i> Tabel Ranking Kelayakan
        </h6>
        <span style="background:#F1F5F9; color:#64748B; font-size:.72rem; font-weight:600; padding:.25rem .75rem; border-radius:20px;">
            {{ $hasilAkhirs->total() }} data
        </span>
    </div>
    <div class="table-responsive">
        <table class="table align-middle mb-0 tbl-navy">
            <thead>
                <tr>
                    <th style="width:100px;">Ranking</th>
                    <th>Nama</th>
                    <th>NIK</th>
                    <th>Jenis Bantuan</th>
                    <th style="width:160px;">Total Skor</th>
                    <th style="width:150px; text-align:center;">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($hasilAkhirs as $h)
                @php
                    // Status ditentukan oleh kuota per jenis bantuan (dihitung di controller)
                    $isLayak = $h->status_computed === 'Layak';
                @endphp
                <tr>
                    <td>
                        <span class="rank-badge {{ $h->ranking_display <= 3 ? 'rank-'.$h->ranking_display : 'rank-n' }}">
                            {{ $h->ranking_display }}
                        </span>
                    </td>
                    <td style="font-weight:600; color:#1E293B; font-size:.83rem;">
                        {{ $h->pengajuan->nama ?? '-' }}
                    </td>
                    <td style="color:#64748B; font-size:.8rem; font-family:monospace;">
                        {{ $h->pengajuan->nik ?? '-' }}
                    </td>
                    <td>
                        <span style="background:#EFF6FF; color:#1E3A5F; font-size:.7rem; font-weight:700; padding:.25rem .75rem; border-radius:20px; display:inline-block;">
                            {{ $h->pengajuan->bantuanSosial->nama_bantuan ?? '-' }}
                        </span>
                    </td>
                    <td style="font-family:monospace; font-weight:700; color:#1E293B; font-size:.78rem;">
                        {{ number_format($h->nilai_yi, 3) }}
                    </td>
                    <td style="text-align:center;">
                        @if($isLayak)
                            <span style="background:#D1FAE5; color:#065F46; font-size:.7rem; font-weight:700; padding:.28rem .75rem; border-radius:20px; display:inline-block;">Layak</span>
                        @else
                            <span style="background:#FEE2E2; color:#991B1B; font-size:.7rem; font-weight:700; padding:.28rem .75rem; border-radius:20px; display:inline-block;">Tidak Layak</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6">
                        <div class="empty-state" style="padding:3rem;">
                            <i class="bi bi-inbox" style="font-size:2rem;"></i>
                            <p>Tidak ada data yang sesuai.</p>
                            @if(request()->hasAny(['search','jenis_bantuan','status']))
                                <a href="{{ route('admin.hasilakhir.index') }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-x-circle me-1"></i>Hapus Filter
                                </a>
                            @else
                                <a href="{{ route('admin.penilaian.index') }}" class="btn btn-sm btn-primary">
                                    <i class="bi bi-calculator me-1"></i>Pergi ke Menu Penilaian
                                </a>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($hasilAkhirs->hasPages())
    <div class="d-flex justify-content-between align-items-center px-4 py-3 border-top" style="font-size:.8rem;">
        <span style="color:#64748B;">
            Menampilkan {{ $hasilAkhirs->firstItem() }}–{{ $hasilAkhirs->lastItem() }} dari {{ $hasilAkhirs->total() }} data
        </span>
        {{ $hasilAkhirs->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>

@endsection