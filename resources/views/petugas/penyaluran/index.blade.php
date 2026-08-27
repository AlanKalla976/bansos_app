@extends('admin.layouts.app')

@section('title', 'Penjadwalan Penyaluran')
@section('page-title', 'Penjadwalan Penyaluran')
@section('breadcrumb', 'Penyaluran')

@push('styles')
<style>
/* ── Table ── */
.tbl-penyaluran thead tr,
.tbl-penyaluran thead tr th {
    background: #1E3A5F !important;
    color: #fff !important;
    font-size: .75rem !important;
    font-weight: 600 !important;
    letter-spacing: .4px;
    padding: .85rem 1rem !important;
    border: none !important;
}
.tbl-penyaluran tbody tr { border-bottom: 1px solid #F1F5F9 !important; transition: background .15s; }
.tbl-penyaluran tbody tr:last-child { border-bottom: none !important; }
.tbl-penyaluran tbody tr:hover td { background: #F8FAFC !important; }
.tbl-penyaluran tbody td {
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
    <div class="col-sm-3">
        <a href="{{ route('admin.petugas.penyaluran.index', ['status'=>'Belum Dijadwalkan']) }}" class="text-decoration-none">
            <div class="stat-card {{ $statusFilter==='Belum Dijadwalkan' ? 'border-warning' : '' }}" style="{{ $statusFilter==='Belum Dijadwalkan' ? 'border-color:#F59E0B!important;' : '' }}">
                <div class="stat-icon" style="background:#FEF3C7;">
                    <i class="bi bi-calendar-x" style="color:#D97706;"></i>
                </div>
                <div>
                    <div class="stat-label">Belum Dijadwalkan</div>
                    <div class="stat-value" style="color:#D97706;">{{ $statsBelum }}</div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-sm-3">
        <a href="{{ route('admin.petugas.penyaluran.index', ['status'=>'Sudah Dijadwalkan']) }}" class="text-decoration-none">
            <div class="stat-card {{ $statusFilter==='Sudah Dijadwalkan' ? 'border-info' : '' }}" style="{{ $statusFilter==='Sudah Dijadwalkan' ? 'border-color:#0284C7!important;' : '' }}">
                <div class="stat-icon" style="background:#E0F2FE;">
                    <i class="bi bi-calendar-check" style="color:#0284C7;"></i>
                </div>
                <div>
                    <div class="stat-label">Sudah Dijadwalkan</div>
                    <div class="stat-value" style="color:#0284C7;">{{ $statsSudah }}</div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-sm-3">
        <a href="{{ route('admin.petugas.penyaluran.index', ['status'=>'Sudah Diambil']) }}" class="text-decoration-none">
            <div class="stat-card {{ $statusFilter==='Sudah Diambil' ? 'border-success' : '' }}" style="{{ $statusFilter==='Sudah Diambil' ? 'border-color:#059669!important;' : '' }}">
                <div class="stat-icon" style="background:#D1FAE5;">
                    <i class="bi bi-bag-check" style="color:#059669;"></i>
                </div>
                <div>
                    <div class="stat-label">Sudah Diambil</div>
                    <div class="stat-value" style="color:#059669;">{{ $statsDiambil }}</div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-sm-3">
        <a href="{{ route('admin.petugas.penyaluran.index', ['status'=>'Tidak Diambil']) }}" class="text-decoration-none">
            <div class="stat-card {{ $statusFilter==='Tidak Diambil' ? 'border-danger' : '' }}" style="{{ $statusFilter==='Tidak Diambil' ? 'border-color:#DC2626!important;' : '' }}">
                <div class="stat-icon" style="background:#FEE2E2;">
                    <i class="bi bi-bag-x" style="color:#DC2626;"></i>
                </div>
                <div>
                    <div class="stat-label">Tidak Diambil</div>
                    <div class="stat-value" style="color:#DC2626;">{{ $statsTidakDiambil }}</div>
                </div>
            </div>
        </a>
    </div>
</div>

{{-- ── Filter ── --}}
<div class="page-card mb-4">
    <div class="card-body-inner">
        <form method="GET" action="{{ route('admin.petugas.penyaluran.index') }}" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Cari Nama / NIK</label>
                <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Cari...">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Status Penyaluran</label>
                <select name="status" class="form-select">
                    @foreach(['Belum Dijadwalkan','Sudah Dijadwalkan','Sudah Diambil','Tidak Diambil'] as $st)
                        <option value="{{ $st }}" {{ $statusFilter === $st ? 'selected' : '' }}>{{ $st }}</option>
                    @endforeach
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
                <a href="{{ route('admin.petugas.penyaluran.index') }}" class="btn btn-outline-secondary">
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
            <i class="bi bi-calendar-check-fill"></i>
            Daftar Penyaluran &mdash; {{ $statusFilter }}
        </h6>
        <span style="background:#F1F5F9; color:#64748B; font-size:.68rem; font-weight:600; padding:.25rem .75rem; border-radius:20px;">
            {{ $penyalurans->total() }} total
        </span>
    </div>

    <div class="card-body-inner p-0">
        @if($penyalurans->isEmpty())
            <div class="empty-state" style="padding:3rem;">
                <i class="bi bi-inbox" style="font-size:2rem;"></i>
                <p>Tidak ada data dengan status <strong>{{ $statusFilter }}</strong>.</p>
            </div>
        @else
        <div class="table-responsive">
            <table class="table align-middle mb-0 tbl-penyaluran">
                <thead>
                    <tr>
                        <th style="width:50px;">No</th>
                        <th>Nama Penerima</th>
                        <th>NIK</th>
                        <th>Jenis Bantuan</th>
                        @if($statusFilter !== 'Belum Dijadwalkan')
                        <th>Tanggal Pengambilan</th>
                        <th>Waktu</th>
                        <th>Lokasi</th>
                        <th>Petugas Pembuat</th>
                        @endif
                        <th style="width:140px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($penyalurans as $i => $p)
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
                        @if($statusFilter !== 'Belum Dijadwalkan')
                        <td>
                            <i class="bi bi-calendar3 me-1"></i>{{ $p->tanggal_pengambilan?->format('d M Y') ?? '-' }}
                        </td>
                        <td>
                            <i class="bi bi-clock me-1"></i>{{ $p->waktu_mulai ? substr($p->waktu_mulai, 0, 5) : '-' }} - {{ $p->waktu_selesai ? substr($p->waktu_selesai, 0, 5) : '-' }}
                        </td>
                        <td>{{ $p->lokasi_pengambilan ?? '-' }}</td>
                        <td>{{ $p->petugas->name ?? '-' }}</td>
                        @endif
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('admin.petugas.penyaluran.edit', $p->id) }}"
                                   class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                    <i class="bi bi-calendar-event"></i>
                                    @if($statusFilter === 'Belum Dijadwalkan') Atur @else Edit @endif
                                </a>
                                @if($statusFilter === 'Sudah Dijadwalkan')
                                    <a href="{{ route('admin.petugas.penyaluran.konfirmasi.show', $p->id) }}"
                                       class="btn btn-sm btn-success rounded-pill px-3">
                                        <i class="bi bi-check2-square"></i> Konfirmasi
                                    </a>
                                @endif
                            </div>
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
