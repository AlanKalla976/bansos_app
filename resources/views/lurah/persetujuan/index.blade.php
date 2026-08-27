@extends('admin.layouts.app')

@section('title', 'Persetujuan Penerima Bantuan')
@section('page-title', 'Persetujuan Penerima Bantuan')
@section('breadcrumb', 'Persetujuan')

@push('styles')
<style>
/* ── Table ── */
.tbl-lurah thead tr,
.tbl-lurah thead tr th {
    background: linear-gradient(135deg, #1E3A5F 0%, #2D6A4F 100%) !important;
    color: #fff !important;
    font-size: .73rem !important;
    font-weight: 700 !important;
    letter-spacing: .5px;
    text-transform: uppercase;
    padding: .9rem 1rem !important;
    border: none !important;
    white-space: nowrap;
}
.tbl-lurah tbody tr { border-bottom: 1px solid #F1F5F9 !important; transition: background .15s; }
.tbl-lurah tbody tr:last-child { border-bottom: none !important; }
.tbl-lurah tbody tr:hover td { background: #F8FAFC !important; }
.tbl-lurah tbody td {
    padding: .85rem 1rem !important;
    border: none !important;
    vertical-align: middle !important;
}

/* ── Rank badge ── */
.rank-badge {
    display: inline-flex; align-items: center; justify-content: center;
    width: 32px; height: 32px; border-radius: 50%;
    font-size: .78rem; font-weight: 800;
}
.rank-1 { background: linear-gradient(135deg,#FBBF24,#F59E0B); color:#78350F; box-shadow: 0 2px 6px rgba(245,158,11,.4); }
.rank-2 { background: linear-gradient(135deg,#D1D5DB,#9CA3AF); color:#1F2937; }
.rank-3 { background: linear-gradient(135deg,#D97706,#B45309); color:#fff; }
.rank-n { background: #F1F5F9; color:#64748B; }

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

{{-- ── Header ── --}}
<div class="page-header mb-4">
    <div>
        <h2 class="page-header-title">
            <i class="bi bi-person-check-fill me-2" style="color:var(--accent);"></i>
            Persetujuan Penerima Bantuan
        </h2>
        <p class="page-header-sub">Tinjau rekomendasi MOORA dan berikan keputusan akhir penerimaan bantuan sosial</p>
    </div>
</div>

{{-- ── Statistik Cards ── --}}
<div class="row g-3 mb-4">
    <div class="col-sm-4">
        <a href="{{ route('admin.lurah.persetujuan.index', ['persetujuan_status'=>'Menunggu Persetujuan']) }}" class="text-decoration-none">
            <div class="stat-card {{ $statusFilter === 'Menunggu Persetujuan' ? 'border-warning' : '' }}"
                 style="{{ $statusFilter === 'Menunggu Persetujuan' ? 'border:2px solid #F59E0B!important;' : '' }}">
                <div class="stat-icon gold"><i class="bi bi-hourglass-split"></i></div>
                <div>
                    <div class="stat-val" style="color:#D97706;">{{ $statsMenunggu }}</div>
                    <div class="stat-lbl">Menunggu Persetujuan</div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-sm-4">
        <a href="{{ route('admin.lurah.persetujuan.index', ['persetujuan_status'=>'Disetujui']) }}" class="text-decoration-none">
            <div class="stat-card {{ $statusFilter === 'Disetujui' ? 'border-success' : '' }}"
                 style="{{ $statusFilter === 'Disetujui' ? 'border:2px solid #059669!important;' : '' }}">
                <div class="stat-icon green"><i class="bi bi-patch-check-fill"></i></div>
                <div>
                    <div class="stat-val" style="color:#059669;">{{ $statsDisetujui }}</div>
                    <div class="stat-lbl">Disetujui</div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-sm-4">
        <a href="{{ route('admin.lurah.persetujuan.index', ['persetujuan_status'=>'Ditolak']) }}" class="text-decoration-none">
            <div class="stat-card {{ $statusFilter === 'Ditolak' ? 'border-danger' : '' }}"
                 style="{{ $statusFilter === 'Ditolak' ? 'border:2px solid #DC2626!important;' : '' }}">
                <div class="stat-icon red"><i class="bi bi-x-circle-fill"></i></div>
                <div>
                    <div class="stat-val" style="color:#DC2626;">{{ $statsDitolak }}</div>
                    <div class="stat-lbl">Ditolak Lurah</div>
                </div>
            </div>
        </a>
    </div>
</div>

{{-- ── Filter Bar ── --}}
<div class="page-card mb-4">
    <div class="card-body-inner">
        <form method="GET" action="{{ route('admin.lurah.persetujuan.index') }}" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Cari Nama / NIK</label>
                <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Cari...">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Jenis Bantuan</label>
                <select name="jenis_bantuan" class="form-select">
                    <option value="">Semua Bantuan</option>
                    @foreach($jenisBantuanList as $id => $nama)
                        <option value="{{ $id }}" {{ request('jenis_bantuan') == $id ? 'selected' : '' }}>{{ $nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Status Persetujuan</label>
                <select name="persetujuan_status" class="form-select">
                    <option value="">Semua Status</option>
                    @foreach(['Menunggu Persetujuan', 'Disetujui', 'Ditolak'] as $st)
                        <option value="{{ $st }}" {{ $statusFilter === $st ? 'selected' : '' }}>{{ $st }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-fill">
                    <i class="bi bi-funnel-fill me-1"></i> Filter
                </button>
                <a href="{{ route('admin.lurah.persetujuan.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-x-circle"></i>
                </a>
            </div>
        </form>
    </div>
</div>

{{-- ── Tabel Daftar ── --}}
<div class="page-card">
    <div class="card-head">
        <h6 class="card-head-title">
            <i class="bi bi-table"></i>
            Daftar Calon Penerima &mdash; Hasil Rekomendasi MOORA
        </h6>
        <span style="background:#F1F5F9; color:#64748B; font-size:.68rem; font-weight:600; padding:.25rem .75rem; border-radius:20px;">
            {{ $hasilAkhirs->total() }} data
        </span>
    </div>

    <div class="card-body-inner p-0">
        @if($hasilAkhirs->isEmpty())
            <div class="empty-state" style="padding:3rem;">
                <i class="bi bi-inbox" style="font-size:2rem;"></i>
                <p>Belum ada data hasil MOORA yang tersedia.</p>
                <small class="text-muted">Admin perlu menjalankan perhitungan MOORA terlebih dahulu.</small>
            </div>
        @else
        <div class="table-responsive">
            <table class="table align-middle mb-0 tbl-lurah">
                <thead>
                    <tr>
                        <th style="width:60px;">Rank</th>
                        <th>Nama Calon Penerima</th>
                        <th>NIK</th>
                        <th>Jenis Bantuan</th>
                        <th style="width:120px; text-align:center;">Nilai Yi</th>
                        <th style="width:120px; text-align:center;">Rekomendasi</th>
                        <th style="width:160px; text-align:center;">Status Persetujuan</th>
                        <th style="width:100px; text-align:center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $stMap = [
                            'Menunggu Persetujuan' => ['bg'=>'#FEF3C7','color'=>'#92400E','icon'=>'bi-hourglass-split'],
                            'Disetujui'            => ['bg'=>'#D1FAE5','color'=>'#065F46','icon'=>'bi-patch-check-fill'],
                            'Ditolak'              => ['bg'=>'#FEE2E2','color'=>'#991B1B','icon'=>'bi-x-circle-fill'],
                        ];
                    @endphp
                    @foreach($hasilAkhirs as $h)
                    @php
                        $st      = $h->persetujuan_status ?? 'Menunggu Persetujuan';
                        $stStyle = $stMap[$st];
                        $rank    = $h->ranking_in_bantuan ?? $h->global_ranking ?? '-';
                        $rankClass = $rank <= 3 ? "rank-{$rank}" : 'rank-n';
                        // Status rekomendasi MOORA (Layak berdasarkan nilai_yi & kuota)
                        $isLayak = $h->status === 'Layak';
                    @endphp
                    <tr>
                        <td>
                            <span class="rank-badge {{ $rankClass }}">{{ $rank }}</span>
                        </td>
                        <td class="fw-semibold" style="color:#1E293B;">
                            {{ $h->pengajuan->nama ?? '-' }}
                        </td>
                        <td style="font-family:monospace; font-size:.8rem; color:#64748B;">
                            {{ $h->pengajuan->nik ?? '-' }}
                        </td>
                        <td>
                            <span style="background:#EFF6FF; color:#1E3A5F; font-size:.7rem; font-weight:700; padding:.25rem .75rem; border-radius:20px; display:inline-block;">
                                {{ $h->pengajuan->bantuanSosial->nama_bantuan ?? '-' }}
                            </span>
                        </td>
                        <td style="text-align:center; font-family:monospace; font-weight:700; font-size:.82rem; color:#1E293B;">
                            {{ number_format($h->nilai_yi, 4) }}
                        </td>
                        <td style="text-align:center;">
                            @if($isLayak)
                                <span class="badge-layak"><i class="bi bi-check-circle-fill"></i> Layak</span>
                            @else
                                <span class="badge-tidaklayak"><i class="bi bi-x-circle-fill"></i> Tidak Layak</span>
                            @endif
                        </td>
                        <td style="text-align:center;">
                            <span style="background:{{ $stStyle['bg'] }}; color:{{ $stStyle['color'] }}; font-size:.68rem; font-weight:700; padding:.28rem .65rem; border-radius:20px; display:inline-flex; align-items:center; gap:.3rem; white-space:nowrap;">
                                <i class="bi {{ $stStyle['icon'] }}" style="font-size:.65rem;"></i>
                                {{ $st === 'Menunggu Persetujuan' ? 'Menunggu' : $st }}
                            </span>
                        </td>
                        <td style="text-align:center;">
                            <a href="{{ route('admin.lurah.persetujuan.show', $h->hasil_id) }}"
                               class="btn btn-sm {{ $st === 'Menunggu Persetujuan' ? 'btn-warning' : 'btn-outline-secondary' }} rounded-pill px-3"
                               title="{{ $st === 'Menunggu Persetujuan' ? 'Tinjau & Putuskan' : 'Lihat Detail' }}">
                                @if($st === 'Menunggu Persetujuan')
                                    <i class="bi bi-pen-fill me-1"></i> Putuskan
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

        @if($hasilAkhirs->hasPages())
        <div class="d-flex justify-content-between align-items-center px-4 py-3 border-top" style="font-size:.8rem;">
            <span style="color:#64748B;">
                Menampilkan {{ $hasilAkhirs->firstItem() }}–{{ $hasilAkhirs->lastItem() }} dari {{ $hasilAkhirs->total() }} data
            </span>
            {{ $hasilAkhirs->onEachSide(1)->links('pagination::bootstrap-5') }}
        </div>
        @endif
        @endif
    </div>
</div>

@endsection
