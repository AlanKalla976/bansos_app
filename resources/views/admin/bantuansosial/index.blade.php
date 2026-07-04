@extends('admin.layouts.app')

@section('title', 'Data Bantuan Sosial')
@section('page-title', 'Data Bantuan Sosial')
@section('breadcrumb', 'Jenis Bantuan')

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
.tbl-navy tbody tr {
    border-bottom: 1px solid #F1F5F9 !important;
    transition: background .15s;
}
.tbl-navy tbody tr:last-child {
    border-bottom: none !important;
}
.tbl-navy tbody tr:hover td {
    background: #F8FAFC !important;
}
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
            <i class="bi bi-gift-fill me-2" style="color:var(--accent);"></i>
            Data Bantuan Sosial
        </h2>
        <p class="page-header-sub">Kelola jenis bantuan sosial yang disalurkan</p>
    </div>
    <a href="{{ route('admin.bantuansosial.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-1"></i> Tambah Bantuan
    </a>
</div>

<div class="page-card">
    <div class="card-head">
        <h6 class="card-head-title">
            <i class="bi bi-table"></i> Daftar Jenis Bantuan
        </h6>
        <span style="background:#F1F5F9; color:#64748B; font-size:.72rem; font-weight:600; padding:.25rem .75rem; border-radius:20px;">
            {{ $bantuans->total() }} data
        </span>
    </div>

    <div class="table-responsive">
        <table class="table align-middle mb-0 tbl-navy">
            <thead>
                <tr>
                    <th style="width:60px;">No</th>
                    <th>Nama Bantuan</th>
                    <th>Deskripsi</th>
                    <th style="text-align:center; width:120px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bantuans as $item)
                <tr>
                    <td style="color:#94A3B8; font-size:.78rem;">
                        {{ $loop->iteration + ($bantuans->currentPage() - 1) * $bantuans->perPage() }}
                    </td>
                    <td style="font-weight:600; color:#1E293B; font-size:.83rem;">
                        {{ $item->nama_bantuan }}
                    </td>
                    <td style="color:#64748B; font-size:.82rem;">
                        {{ Str::limit($item->deskripsi, 80, '...') }}
                    </td>
                    <td>
                        <div class="d-flex gap-1 justify-content-center">
                            <a href="{{ route('admin.bantuansosial.edit', $item) }}"
                               class="btn btn-sm btn-warning"
                               style="width:30px; height:30px; padding:0; display:inline-flex; align-items:center; justify-content:center; border-radius:8px; font-size:.78rem;"
                               title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('admin.bantuansosial.destroy', $item) }}"
                                  method="POST"
                                  onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="btn btn-sm btn-danger"
                                        style="width:30px; height:30px; padding:0; display:inline-flex; align-items:center; justify-content:center; border-radius:8px; font-size:.78rem;"
                                        title="Hapus">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4">
                        <div class="empty-state" style="padding:3rem;">
                            <i class="bi bi-gift" style="font-size:2rem;"></i>
                            <p>Belum ada data bantuan sosial.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($bantuans->hasPages())
    <div class="d-flex justify-content-between align-items-center px-4 py-3 border-top" style="font-size:.8rem;">
        <span style="color:#64748B;">
            Menampilkan {{ $bantuans->firstItem() }}–{{ $bantuans->lastItem() }} dari {{ $bantuans->total() }} data
        </span>
        {{ $bantuans->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>
@endsection