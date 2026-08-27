@extends('admin.layouts.app')

@section('title', 'Validasi Berkas Pengajuan')
@section('page-title', 'Validasi Berkas Pengajuan')
@section('breadcrumb', 'Validasi')

@push('styles')
<style>
/* ── Table ── */
.tbl-validasi thead tr,
.tbl-validasi thead tr th {
    background: #1E3A5F !important;
    color: #fff !important;
    font-size: .75rem !important;
    font-weight: 600 !important;
    letter-spacing: .4px;
    padding: .85rem 1rem !important;
    border: none !important;
}
.tbl-validasi tbody tr { border-bottom: 1px solid #F1F5F9 !important; transition: background .15s; }
.tbl-validasi tbody tr:last-child { border-bottom: none !important; }
.tbl-validasi tbody tr:hover td { background: #F8FAFC !important; }
.tbl-validasi tbody td {
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

/* ── Pagination ── */
.pagination { margin: 0; flex-wrap: wrap; gap: .25rem; }
.pagination .page-link {
    border: 1px solid #E2E8F0; color: #1E3A5F;
    font-size: .8rem; font-weight: 600; padding: .4rem .75rem;
    border-radius: 8px; margin: 0;
}
.pagination .page-link:hover { background: #F1F5F9; border-color: #CBD5E1; }
.pagination .page-item.active .page-link { background: #1E3A5F; border-color: #1E3A5F; color: #fff; }
.pagination .page-item.disabled .page-link { background: #F8FAFC; color: #CBD5E1; }
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
        <a href="{{ route('admin.petugas.validasi.index', ['status'=>'Menunggu']) }}" class="text-decoration-none">
            <div class="stat-card {{ $statusFilter==='Menunggu' ? 'border-warning' : '' }}" style="{{ $statusFilter==='Menunggu' ? 'border-color:#F59E0B!important;' : '' }}">
                <div class="stat-icon" style="background:#FEF3C7;">
                    <i class="bi bi-hourglass-split" style="color:#D97706;"></i>
                </div>
                <div>
                    <div class="stat-label">Menunggu Validasi</div>
                    <div class="stat-value" style="color:#D97706;">{{ $statsMenunggu }}</div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-sm-4">
        <a href="{{ route('admin.petugas.validasi.index', ['status'=>'Diverifikasi']) }}" class="text-decoration-none">
            <div class="stat-card {{ $statusFilter==='Diverifikasi' ? 'border-success' : '' }}" style="{{ $statusFilter==='Diverifikasi' ? 'border-color:#059669!important;' : '' }}">
                <div class="stat-icon" style="background:#D1FAE5;">
                    <i class="bi bi-patch-check-fill" style="color:#059669;"></i>
                </div>
                <div>
                    <div class="stat-label">Valid (Diverifikasi)</div>
                    <div class="stat-value" style="color:#059669;">{{ $statsDiverifikasi }}</div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-sm-4">
        <a href="{{ route('admin.petugas.validasi.index', ['status'=>'Ditolak']) }}" class="text-decoration-none">
            <div class="stat-card {{ $statusFilter==='Ditolak' ? 'border-danger' : '' }}" style="{{ $statusFilter==='Ditolak' ? 'border-color:#DC2626!important;' : '' }}">
                <div class="stat-icon" style="background:#FEE2E2;">
                    <i class="bi bi-x-circle-fill" style="color:#DC2626;"></i>
                </div>
                <div>
                    <div class="stat-label">Tidak Valid (Ditolak)</div>
                    <div class="stat-value" style="color:#DC2626;">{{ $statsDitolak }}</div>
                </div>
            </div>
        </a>
    </div>
</div>

{{-- ── Filter ── --}}
<div class="page-card mb-4">
    <div class="card-body-inner">
        <form method="GET" action="{{ route('admin.petugas.validasi.index') }}" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Cari Nama / NIK</label>
                <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Cari...">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Status</label>
                <select name="status" class="form-select">
                    @foreach(['Menunggu','Diverifikasi','Ditolak'] as $st)
                        <option value="{{ $st }}" {{ $statusFilter === $st ? 'selected' : '' }}>{{ $st === 'Diverifikasi' ? 'Valid (Diverifikasi)' : ($st === 'Ditolak' ? 'Tidak Valid (Ditolak)' : $st) }}</option>
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
                <a href="{{ route('admin.petugas.validasi.index') }}" class="btn btn-outline-secondary">
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
            <i class="bi bi-list-ul"></i>
            Daftar Pengajuan &mdash;
            @if($statusFilter === 'Menunggu') Menunggu Validasi
            @elseif($statusFilter === 'Diverifikasi') Sudah Valid
            @else Tidak Valid
            @endif
        </h6>
        <span style="background:#F1F5F9; color:#64748B; font-size:.68rem; font-weight:600; padding:.25rem .75rem; border-radius:20px;">
            {{ $pengajuans->total() }} total
        </span>
    </div>

    <div class="card-body-inner p-0">
        @if($pengajuans->isEmpty())
            <div class="empty-state" style="padding:3rem;">
                <i class="bi bi-inbox" style="font-size:2rem;"></i>
                <p>Tidak ada pengajuan dengan status <strong>{{ $statusFilter }}</strong>.</p>
            </div>
        @else
        <div class="table-responsive">
            <table class="table align-middle mb-0 tbl-validasi">
                <thead>
                    <tr>
                        <th style="width:50px;">No</th>
                        <th>Tanggal</th>
                        <th>Nama</th>
                        <th>NIK</th>
                        <th>Jenis Bantuan</th>
                        <th>Status Berkas</th>
                        @if($statusFilter !== 'Menunggu')
                        <th>Divalidasi Oleh</th>
                        <th>Tanggal Validasi</th>
                        @endif
                        <th style="width:100px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $stMap = [
                            'Menunggu'     => ['bg'=>'#FEF3C7','color'=>'#92400E'],
                            'Diverifikasi' => ['bg'=>'#D1FAE5','color'=>'#065F46'],
                            'Ditolak'      => ['bg'=>'#FEE2E2','color'=>'#991B1B'],
                        ];
                    @endphp
                    @foreach($pengajuans as $i => $p)
                    @php
                        $st      = $p->status;
                        $stStyle = $stMap[$st] ?? ['bg'=>'#F1F5F9','color'=>'#64748B'];
                    @endphp
                    <tr>
                        <td style="color:#94A3B8; font-size:.78rem;">
                            {{ $pengajuans->firstItem() + $i }}
                        </td>
                        <td style="color:#64748B; font-size:.8rem;">
                            <i class="bi bi-calendar3 me-1"></i>{{ optional($p->created_at)->format('d M Y') }}
                        </td>
                        <td class="fw-semibold">{{ $p->nama }}</td>
                        <td>{{ $p->nik }}</td>
                        <td>
                            <span style="background:#EFF6FF; color:#1E3A5F; font-size:.7rem; font-weight:700; padding:.25rem .75rem; border-radius:20px; display:inline-block;">
                                {{ $p->bantuanSosial->nama_bantuan ?? '-' }}
                            </span>
                        </td>
                        <td>
                            <span style="background:{{ $stStyle['bg'] }}; color:{{ $stStyle['color'] }}; font-size:.7rem; font-weight:700; padding:.28rem .75rem; border-radius:20px; display:inline-block;">
                                @if($st === 'Diverifikasi') ✓ Valid
                                @elseif($st === 'Ditolak') ✗ Tidak Valid
                                @else ⏳ Menunggu
                                @endif
                            </span>
                        </td>
                        @if($statusFilter !== 'Menunggu')
                        <td style="font-size:.82rem;">{{ $p->validator->name ?? '-' }}</td>
                        <td style="font-size:.82rem; color:#64748B;">
                            {{ $p->validated_at ? $p->validated_at->format('d M Y H:i') : '-' }}
                        </td>
                        @endif
                        <td>
                            <a href="{{ route('admin.petugas.validasi.show', $p->id) }}"
                               class="btn btn-sm btn-primary rounded-pill px-3"
                               title="{{ $st === 'Menunggu' ? 'Periksa & Validasi' : 'Lihat Detail' }}">
                                @if($st === 'Menunggu')
                                    <i class="bi bi-clipboard2-check-fill me-1"></i> Periksa
                                @else
                                    <i class="bi bi-eye-fill me-1"></i> Detail
                                @endif
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($pengajuans->hasPages())
        <div class="d-flex justify-content-between align-items-center px-4 py-3 border-top" style="font-size:.8rem;">
            <span style="color:#64748B;">
                Menampilkan {{ $pengajuans->firstItem() }}–{{ $pengajuans->lastItem() }} dari {{ $pengajuans->total() }}
            </span>
            {{ $pengajuans->onEachSide(1)->links('pagination::bootstrap-5') }}
        </div>
        @endif
        @endif
    </div>
</div>

@endsection
