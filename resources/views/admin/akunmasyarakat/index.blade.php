@extends('admin.layouts.app')

@section('title', 'Akun Masyarakat')
@section('page-title', 'Akun Masyarakat')
@section('breadcrumb', 'Akun Masyarakat')

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
.tbl-navy tbody tr:last-child { border-bottom: none !important; }
.tbl-navy tbody tr:hover td { background: #F8FAFC !important; }
.tbl-navy tbody td {
    padding: .85rem 1rem !important;
    border: none !important;
    border-top: none !important;
    vertical-align: middle !important;
}
.avatar-circle {
    width: 32px; height: 32px; border-radius: 50%;
    background: #EFF6FF; color: #1E3A5F;
    font-weight: 700; font-size: .8rem;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
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
            <i class="bi bi-people-fill me-2" style="color:var(--accent);"></i>
            Akun Masyarakat
        </h2>
        <p class="page-header-sub">Kelola data akun warga terdaftar</p>
    </div>
    <a href="{{ route('admin.akunmasyarakat.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-1"></i> Tambah Akun
    </a>
</div>

{{-- Search --}}
<div class="filter-bar mb-3">
    <form method="GET" action="{{ route('admin.akunmasyarakat.index') }}" class="d-flex gap-2">
        <input type="text" name="search" value="{{ $search }}"
               class="form-control form-control-sm"
               placeholder="Cari nama, email, atau NIK..."
               style="max-width:320px;">
        <button type="submit" class="btn btn-primary btn-sm">
            <i class="bi bi-search me-1"></i>Cari
        </button>
        @if($search)
        <a href="{{ route('admin.akunmasyarakat.index') }}"
           class="btn btn-sm" style="background:#F1F5F9; color:var(--text-muted); border:1px solid var(--border);">
            Reset
        </a>
        @endif
    </form>
</div>

{{-- Table --}}
<div class="page-card">
    <div class="card-head">
        <h6 class="card-head-title">
            <i class="bi bi-table"></i> Daftar Akun Masyarakat
        </h6>
        <span style="background:#F1F5F9; color:#64748B; font-size:.72rem; font-weight:600; padding:.25rem .75rem; border-radius:20px;">
            {{ $akun->total() }} data
        </span>
    </div>

    <div class="table-responsive">
        <table class="table align-middle mb-0 tbl-navy">
            <thead>
                <tr>
                    <th style="width:50px;">No</th>
                    <th>NIK</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Dibuat</th>
                    <th style="text-align:center; width:130px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($akun as $index => $item)
                <tr>
                    <td style="color:#94A3B8; font-size:.78rem;">
                        {{ $akun->firstItem() + $index }}
                    </td>
                    <td>
                        <code style="background:#F1F5F9; border-radius:6px; padding:.2rem .55rem; font-size:.8rem; color:#334155;">
                            {{ $item->nik }}
                        </code>
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="avatar-circle">
                                {{ strtoupper(substr($item->name, 0, 1)) }}
                            </div>
                            <span style="font-weight:600; color:#1E293B; font-size:.83rem;">{{ $item->name }}</span>
                        </div>
                    </td>
                    <td style="color:#64748B; font-size:.82rem;">{{ $item->email }}</td>
                    <td>
                        @if($item->role == 'admin')
                            <span style="background:#FEE2E2; color:#991B1B; font-size:.7rem; font-weight:700; padding:.25rem .75rem; border-radius:20px; display:inline-block;">Admin</span>
                        @elseif($item->role == 'masyarakat')
                            <span style="background:#D1FAE5; color:#065F46; font-size:.7rem; font-weight:700; padding:.25rem .75rem; border-radius:20px; display:inline-block;">Masyarakat</span>
                        @else
                            <span style="background:#F1F5F9; color:#64748B; font-size:.7rem; font-weight:700; padding:.25rem .75rem; border-radius:20px; display:inline-block;">{{ ucfirst($item->role) }}</span>
                        @endif
                    </td>
                    <td style="color:#64748B; font-size:.8rem;">
                        <i class="bi bi-calendar3 me-1"></i>{{ $item->created_at->format('d M Y') }}
                    </td>
                    <td>
                        <div class="d-flex justify-content-center gap-1">
                            <a href="{{ route('admin.akunmasyarakat.show', $item->users_id) }}"
                               class="btn btn-sm btn-info btn-action" title="Detail">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('admin.akunmasyarakat.edit', $item->users_id) }}"
                               class="btn btn-sm btn-warning btn-action" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('admin.akunmasyarakat.destroy', $item->users_id) }}"
                                  method="POST"
                                  onsubmit="return confirm('Hapus akun {{ addslashes($item->name) }}?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger btn-action" title="Hapus">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7">
                        <div class="empty-state" style="padding:3rem;">
                            <i class="bi bi-people" style="font-size:2rem;"></i>
                            <p>{{ $search ? 'Tidak ada hasil untuk "' . $search . '"' : 'Belum ada akun masyarakat.' }}</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($akun->hasPages())
    <div class="d-flex justify-content-between align-items-center px-4 py-3 border-top" style="font-size:.8rem;">
        <span style="color:#64748B;">
            Menampilkan {{ $akun->firstItem() }}–{{ $akun->lastItem() }} dari {{ $akun->total() }} akun
        </span>
        {{ $akun->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>

@endsection