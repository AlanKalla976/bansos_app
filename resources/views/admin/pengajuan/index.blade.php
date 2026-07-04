@extends('admin.layouts.app')

@section('title', 'Data Pengajuan')
@section('page-title', 'Data Pengajuan Bantuan Sosial')
@section('breadcrumb', 'Pengajuan')

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
}
.tbl-navy tbody tr { border-bottom: 1px solid #F1F5F9 !important; transition: background .15s; }
.tbl-navy tbody tr:last-child { border-bottom: none !important; }
.tbl-navy tbody tr:hover td { background: #F8FAFC !important; }
.tbl-navy tbody td {
    padding: .85rem 1rem !important;
    border: none !important;
    vertical-align: middle !important;
}
</style>
@endpush

@section('content')

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm mb-4" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="page-card mb-4">
    <div class="card-body-inner">
        <form method="GET" action="{{ route('admin.pengajuan.index') }}" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Cari Nama / NIK</label>
                <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Cari...">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Status</label>
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    @foreach(['Menunggu','Diverifikasi','Ditolak','Diterima'] as $st)
                        <option value="{{ $st }}" {{ request('status') === $st ? 'selected' : '' }}>{{ $st }}</option>
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
                <a href="{{ route('admin.pengajuan.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-x-circle"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<div class="d-flex gap-2 flex-wrap mb-3">
    {{-- Download semua data --}}
    <a href="{{ route('admin.pengajuan.export.excel') }}" class="btn btn-success btn-sm rounded-pill px-3">
        <i class="bi bi-file-earmark-excel-fill me-1"></i> Excel (Semua)
    </a>
    <a href="{{ route('admin.pengajuan.export.pdf') }}" class="btn btn-danger btn-sm rounded-pill px-3">
        <i class="bi bi-file-earmark-pdf-fill me-1"></i> PDF (Semua)
    </a>

    {{-- Download per jenis bantuan --}}
    <div class="dropdown">
        <button class="btn btn-outline-secondary btn-sm rounded-pill px-3 dropdown-toggle" type="button" data-bs-toggle="dropdown">
            <i class="bi bi-funnel-fill me-1"></i> Download per Jenis Bantuan
        </button>
        <ul class="dropdown-menu">
            @forelse($bantuans as $bantuan)
                <li><h6 class="dropdown-header">{{ $bantuan->nama_bantuan }}</h6></li>
                <li>
                    <a class="dropdown-item" href="{{ route('admin.pengajuan.export.excel', $bantuan->id) }}">
                        <i class="bi bi-file-earmark-excel text-success me-1"></i> Excel
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="{{ route('admin.pengajuan.export.pdf', $bantuan->id) }}">
                        <i class="bi bi-file-earmark-pdf text-danger me-1"></i> PDF
                    </a>
                </li>
                <li><hr class="dropdown-divider"></li>
            @empty
                <li><span class="dropdown-item-text text-muted small">Belum ada jenis bantuan.</span></li>
            @endforelse
        </ul>
    </div>

    <a href="{{ route('admin.pengajuan.create') }}" class="btn btn-primary btn-sm rounded-pill px-3 ms-auto">
        <i class="bi bi-plus-circle-fill me-1"></i> Tambah Pengajuan
    </a>
</div>

<div class="page-card">
    <div class="card-head">
        <h6 class="card-head-title">
            <i class="bi bi-list-ul"></i> Daftar Pengajuan
        </h6>
        <span style="background:#F1F5F9; color:#64748B; font-size:.68rem; font-weight:600; padding:.25rem .75rem; border-radius:20px;">
            {{ $pengajuans->total() }} total pengajuan
        </span>
    </div>
    <div class="card-body-inner p-0">
        @if($pengajuans->isEmpty())
            <div class="empty-state" style="padding:3rem;">
                <i class="bi bi-inbox" style="font-size:2rem;"></i>
                <p>Belum ada pengajuan yang sesuai filter.</p>
            </div>
        @else
        <div class="table-responsive">
            <table class="table align-middle mb-0 tbl-navy">
                <thead>
                    <tr>
                        <th style="width:50px;">No</th>
                        <th>Tanggal</th>
                        <th>Nama</th>
                        <th>NIK</th>
                        <th>Jenis Bantuan</th>
                        <th>Status</th>
                        <th style="width:140px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $stMap = [
                            'Menunggu'     => ['bg'=>'#FEF3C7', 'color'=>'#92400E'],
                            'Diverifikasi' => ['bg'=>'#D1FAE5', 'color'=>'#065F46'],
                            'Ditolak'      => ['bg'=>'#FEE2E2', 'color'=>'#991B1B'],
                            'Diterima'     => ['bg'=>'#DBEAFE', 'color'=>'#1E3A8A'],
                        ];
                    @endphp
                    @foreach($pengajuans as $i => $p)
                    @php
                        $st      = $p->status ?? 'Menunggu';
                        $stStyle = $stMap[$st] ?? ['bg'=>'#F1F5F9', 'color'=>'#64748B'];
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
                                {{ $st }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('admin.pengajuan.show', $p->id) }}" class="btn btn-sm btn-outline-primary" title="Lihat">
                                    <i class="bi bi-eye-fill"></i>
                                </a>
                                <a href="{{ route('admin.pengajuan.edit', $p->id) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                    <i class="bi bi-pencil-fill"></i>
                                </a>
                                <form action="{{ route('admin.pengajuan.destroy', $p->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus pengajuan ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                        <i class="bi bi-trash-fill"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-3">
            {{ $pengajuans->onEachSide(1)->links() }}
        </div>
        @endif
    </div>
</div>

@endsection