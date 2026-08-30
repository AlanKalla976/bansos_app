@extends('admin.layouts.app')

@section('title', 'Monitoring Penyaluran')
@section('page-title', 'Monitoring Penyaluran')
@section('breadcrumb', 'Monitoring')

@push('styles')
<style>
/* ── Table ── */
.tbl-monitoring thead tr,
.tbl-monitoring thead tr th {
    background: #1E3A5F !important;
    color: #fff !important;
    font-size: .75rem !important;
    font-weight: 600 !important;
    letter-spacing: .4px;
    padding: .85rem 1rem !important;
    border: none !important;
}
.tbl-monitoring tbody tr { border-bottom: 1px solid #F1F5F9 !important; transition: background .15s; }
.tbl-monitoring tbody tr:last-child { border-bottom: none !important; }
.tbl-monitoring tbody tr:hover td { background: #F8FAFC !important; }
.tbl-monitoring tbody td {
    padding: .85rem 1rem !important;
    border: none !important;
    vertical-align: middle !important;
}

/* ── Stats cards ── */
.stat-card {
    background: #fff;
    border-radius: 16px;
    padding: 1.25rem 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    box-shadow: 0 2px 8px rgba(30,58,95,.08);
    border: 1px solid #E2E8F0;
}
.stat-icon {
    width: 52px; height: 52px;
    border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.4rem;
    flex-shrink: 0;
}
.stat-label { font-size: .73rem; font-weight: 600; color: #94A3B8; text-transform: uppercase; letter-spacing: .5px; }
.stat-value { font-size: 1.6rem; font-weight: 800; line-height: 1.1; }
</style>
@endpush

@section('content')

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm mb-4" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm mb-4" role="alert">
        <i class="bi bi-x-circle-fill me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

{{-- ── Statistik ringkasan ── --}}
<div class="row g-3 mb-4">
    <div class="col-sm-4">
        <a href="{{ route($routePrefix . '.monitoring.index', ['status'=>'Semua']) }}" class="text-decoration-none">
            <div class="stat-card {{ $statusFilter==='Semua' ? 'border-primary' : '' }}" style="{{ $statusFilter==='Semua' ? 'border-color:#1E3A5F!important;' : '' }}">
                <div class="stat-icon" style="background:#DBEAFE;">
                    <i class="bi bi-collection-fill" style="color:#1E3A5F;"></i>
                </div>
                <div>
                    <div class="stat-label">Total Penyaluran</div>
                    <div class="stat-value" style="color:#1E3A5F;">{{ $statsTotal }}</div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-sm-4">
        <a href="{{ route($routePrefix . '.monitoring.index', ['status'=>'Belum']) }}" class="text-decoration-none">
            <div class="stat-card {{ $statusFilter==='Belum' ? 'border-warning' : '' }}" style="{{ $statusFilter==='Belum' ? 'border-color:#F59E0B!important;' : '' }}">
                <div class="stat-icon" style="background:#FEF3C7;">
                    <i class="bi bi-exclamation-triangle" style="color:#D97706;"></i>
                </div>
                <div>
                    <div class="stat-label">Belum Dimonitoring</div>
                    <div class="stat-value" style="color:#D97706;">{{ $statsBelum }}</div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-sm-4">
        <a href="{{ route($routePrefix . '.monitoring.index', ['status'=>'Sudah']) }}" class="text-decoration-none">
            <div class="stat-card {{ $statusFilter==='Sudah' ? 'border-success' : '' }}" style="{{ $statusFilter==='Sudah' ? 'border-color:#059669!important;' : '' }}">
                <div class="stat-icon" style="background:#D1FAE5;">
                    <i class="bi bi-shield-fill-check" style="color:#059669;"></i>
                </div>
                <div>
                    <div class="stat-label">Sudah Dimonitoring</div>
                    <div class="stat-value" style="color:#059669;">{{ $statsSudah }}</div>
                </div>
            </div>
        </a>
    </div>
</div>

{{-- ── Filter ── --}}
<div class="page-card mb-4">
    <div class="card-body-inner">
        <form method="GET" action="{{ route($routePrefix . '.monitoring.index') }}" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Cari Nama / NIK</label>
                <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Cari...">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Status Evaluasi</label>
                <select name="status" class="form-select">
                    <option value="Semua" {{ $statusFilter === 'Semua' ? 'selected' : '' }}>Semua</option>
                    <option value="Belum" {{ $statusFilter === 'Belum' ? 'selected' : '' }}>Belum Dimonitoring</option>
                    <option value="Sudah" {{ $statusFilter === 'Sudah' ? 'selected' : '' }}>Sudah Dimonitoring</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Jenis Bantuan</label>
                <select name="bantuan_sosial_id" class="form-select">
                    <option value="">Semua Jenis Bantuan</option>
                    @foreach($bantuans as $bantuan)
                        <option value="{{ $bantuan->id }}" {{ (string) request('bantuan_sosial_id') === (string) $bantuan->id ? 'selected' : '' }}>
                            {{ $bantuan->nama_bantuan }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-fill">
                    <i class="bi bi-funnel-fill me-1"></i> Filter
                </button>
                <a href="{{ route($routePrefix . '.monitoring.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-x-circle"></i>
                </a>
            </div>
        </form>
    </div>
</div>

{{-- ── Tabel daftar ── --}}
<div class="page-card">
    <div class="card-head">
        <h6 class="card-head-title">
            <i class="bi bi-eye-fill"></i>
            Daftar Monitoring &mdash; 
            @if($statusFilter === 'Semua') Semua Realisasi Penyaluran
            @elseif($statusFilter === 'Belum') Belum Dimonitoring
            @else Sudah Dimonitoring
            @endif
        </h6>
        <span style="background:#F1F5F9; color:#64748B; font-size:.68rem; font-weight:600; padding:.25rem .75rem; border-radius:20px;">
            {{ $penyalurans->total() }} total
        </span>
    </div>

    <div class="card-body-inner p-0">
        @if($penyalurans->isEmpty())
            <div class="empty-state" style="padding:3rem;">
                <i class="bi bi-inbox" style="font-size:2rem;"></i>
                <p>Tidak ada data realisasi penyaluran yang sesuai filter.</p>
            </div>
        @else
        <div class="table-responsive">
            <table class="table align-middle mb-0 tbl-monitoring">
                <thead>
                    <tr>
                        <th style="width:50px;">No</th>
                        <th>Penerima</th>
                        <th>NIK</th>
                        <th>Jenis Bantuan</th>
                        <th>Penerima Aktual</th>
                        <th>Tanggal Realisasi</th>
                        <th>Ketepatan Waktu</th>
                        <th>Ketepatan Sasaran</th>
                        <th>Dampak Evaluasi</th>
                        <th style="width:180px; text-align:center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($penyalurans as $i => $p)
                    @php
                        $m = $p->monitoring;
                        
                        // Hitung ketepatan waktu otomatis jika belum disubmit ke DB
                        $waktuLabel = $m ? $m->ketepatan_waktu : null;
                        if (!$waktuLabel && $p->tanggal_pengambilan && $p->tanggal_realisasi) {
                            $waktuLabel = $p->tanggal_realisasi->lte($p->tanggal_pengambilan) ? 'Tepat Waktu' : 'Terlambat';
                        }
                        
                        // Hitung ketepatan sasaran otomatis jika belum disubmit ke DB
                        $sasaranLabel = $m ? $m->ketepatan_sasaran : null;
                        if (!$sasaranLabel) {
                            $namaDisetujui = trim($p->hasilAkhir->pengajuan->nama ?? '');
                            $penerimaAktual = trim($p->penerima_aktual ?? '');
                            $sasaranLabel = (strcasecmp($namaDisetujui, $penerimaAktual) === 0) ? 'Sesuai Sasaran' : 'Tidak Sesuai Sasaran';
                        }
                    @endphp
                    <tr>
                        <td style="color:#94A3B8; font-size:.78rem;">
                            {{ $penyalurans->firstItem() + $i }}
                        </td>
                        <td class="fw-semibold">{{ $p->hasilAkhir->pengajuan->nama ?? '-' }}</td>
                        <td>{{ $p->hasilAkhir->pengajuan->nik ?? '-' }}</td>
                        <td>
                            <span style="background:#EFF6FF; color:#1E3A5F; font-size:.7rem; font-weight:700; padding:.25rem .75rem; border-radius:20px; display:inline-block;">
                                {{ $p->hasilAkhir->pengajuan->bantuanSosial->nama_bantuan ?? '-' }}
                            </span>
                        </td>
                        <td>{{ $p->penerima_aktual ?? '-' }}</td>
                        <td>{{ $p->tanggal_realisasi?->format('d M Y') ?? '-' }}</td>
                        
                        {{-- Aspek 1: Waktu --}}
                        <td>
                            <span class="badge {{ $waktuLabel === 'Tepat Waktu' ? 'bg-success' : 'bg-danger' }}">
                                {{ $waktuLabel ?? '-' }}
                            </span>
                        </td>

                        {{-- Aspek 2: Sasaran --}}
                        <td>
                            <span class="badge {{ $sasaranLabel === 'Sesuai Sasaran' ? 'bg-success' : 'bg-danger' }}">
                                {{ $sasaranLabel ?? '-' }}
                            </span>
                        </td>

                        {{-- Aspek 3: Dampak --}}
                        <td>
                            @if($m)
                                <span class="badge bg-info text-dark">
                                    {{ $m->dampak }}
                                </span>
                            @else
                                <span class="text-muted small">-</span>
                            @endif
                        </td>
                        
                        <td style="text-align:center;">
                            @if(!$m)
                                <span class="badge bg-light text-dark border px-3 py-2 rounded-pill">
                                    <i class="bi bi-hourglass-split me-1"></i> Belum Ada Evaluasi
                                </span>
                            @else
                                <a href="{{ route($routePrefix . '.monitoring.create', $p->id) }}"
                                   class="btn btn-sm btn-primary rounded-pill px-3">
                                    <i class="bi bi-eye-fill me-1"></i> Lihat Detail
                                </a>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($penyalurans->hasPages())
        <div class="d-flex justify-content-between align-items-center px-4 py-3 border-top" style="font-size:.8rem;">
            <span style="color:#64748B;">
                Menampilkan {{ $penyalurans->firstItem() }}–{{ $penyalurans->lastItem() }} dari {{ $penyalurans->total() }}
            </span>
            {{ $penyalurans->onEachSide(1)->links('pagination::bootstrap-5') }}
        </div>
        @endif
        @endif
    </div>
</div>

@endsection
