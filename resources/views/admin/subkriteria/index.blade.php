@extends('admin.layouts.app')

@section('title', 'Data Sub Kriteria')
@section('page-title', 'Data Sub Kriteria')
@section('breadcrumb', 'Data Sub Kriteria')

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
.btn-action {
    width: 30px; height: 30px; padding: 0;
    display: inline-flex; align-items: center; justify-content: center;
    border-radius: 8px; font-size: .78rem;
}
</style>
@endpush

@section('content')

<div class="page-header">
    <div>
        <h2 class="page-header-title">
            <i class="bi bi-node-plus me-2" style="color:var(--accent);"></i>
            Data Sub Kriteria
        </h2>
        <p class="page-header-sub">Kelola sub kriteria untuk setiap kriteria penilaian</p>
    </div>
    <a href="{{ route('admin.subkriteria.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-1"></i>Tambah Sub Kriteria
    </a>
</div>

{{-- Filter Kriteria --}}
<div class="filter-bar mb-3">
    <form method="GET" action="{{ route('admin.subkriteria.index') }}" class="row g-2 align-items-center">
        <div class="col-md-4">
            <select name="kriteria_id" class="form-select form-select-sm">
                <option value="">-- Semua Kriteria --</option>
                @foreach($kriterias as $k)
                    <option value="{{ $k->kriteria_id }}" {{ $kriteriaId == $k->kriteria_id ? 'selected' : '' }}>
                        {{ $k->nama }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-auto d-flex gap-2">
            <button class="btn btn-primary btn-sm">
                <i class="bi bi-filter me-1"></i>Filter
            </button>
            <a href="{{ route('admin.subkriteria.index') }}" class="btn btn-sm" style="background:#F1F5F9; color:var(--text-muted); border:1px solid var(--border);">
                Reset
            </a>
        </div>
    </form>
</div>

{{-- Tabel --}}
<div class="page-card">
    <div class="card-head">
        <h6 class="card-head-title">
            <i class="bi bi-table"></i> Daftar Sub Kriteria
            @if($kriteriaId)
                <span style="color:#64748B; font-weight:400; font-size:.8rem;">
                    — {{ $kriterias->firstWhere('kriteria_id', $kriteriaId)?->nama }}
                </span>
            @endif
        </h6>
    </div>

    <div class="table-responsive">
        <table class="table align-middle mb-0 tbl-navy">
            <thead>
                <tr>
                    <th style="width:70px;">No</th>
                    <th style="width:200px;">Nama Kriteria</th>
                    <th>Nama Sub Kriteria</th>
                    <th style="width:120px;">Tipe</th>
                    <th style="width:120px;">Nilai</th>
                    <th style="width:140px; text-align:center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($subKriterias as $i => $sub)
                <tr>
                    <td style="color:#94A3B8; font-size:.78rem;">{{ $i + 1 }}</td>
                    <td>
                        <span style="background:#EFF6FF; color:#1E3A5F; font-size:.7rem; font-weight:700; padding:.2rem .6rem; border-radius:20px; display:inline-block; margin-right:.4rem;">
                            {{ $sub->kriteria->kode_kriteria }}
                        </span>
                        <span style="font-weight:600; color:#1E293B; font-size:.83rem;">{{ $sub->kriteria->nama }}</span>
                    </td>
                    <td style="font-weight:600; color:#1E293B; font-size:.83rem;">{{ $sub->nama }}</td>
                    <td>
                        @if($sub->kriteria->tipe === 'benefit')
                            <span style="background:#D1FAE5; color:#065F46; font-size:.7rem; font-weight:700; padding:.25rem .75rem; border-radius:20px; display:inline-block;">Benefit</span>
                        @else
                            <span style="background:#FEE2E2; color:#991B1B; font-size:.7rem; font-weight:700; padding:.25rem .75rem; border-radius:20px; display:inline-block;">Cost</span>
                        @endif
                    </td>
                    <td>
                        <span style="background:#EFF6FF; color:#1E3A5F; font-size:.75rem; font-weight:700; padding:.25rem .75rem; border-radius:20px; display:inline-block;">
                            {{ number_format($sub->nilai, 2) }}
                        </span>
                    </td>
                    <td>
                        <div class="d-flex justify-content-center gap-1">
                            <a href="{{ route('admin.subkriteria.show', $sub->subkriteria_id) }}"
                               class="btn btn-sm btn-info btn-action" title="Detail">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('admin.subkriteria.edit', $sub->subkriteria_id) }}"
                               class="btn btn-sm btn-warning btn-action" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('admin.subkriteria.destroy', $sub->subkriteria_id) }}"
                                  method="POST"
                                  onsubmit="return confirm('Yakin ingin menghapus sub kriteria ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger btn-action" title="Hapus">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6">
                        <div class="empty-state" style="padding:3rem;">
                            <i class="bi bi-inbox" style="font-size:2rem;"></i>
                            <p>Belum ada sub kriteria.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection