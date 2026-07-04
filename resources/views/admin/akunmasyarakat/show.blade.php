@extends('admin.layouts.app')

@section('title', 'Detail Akun Masyarakat')

@section('content')
<div class="container-fluid py-4">

    {{-- Header --}}
    <div class="d-flex align-items-center gap-2 mb-4">
        <a href="{{ route('admin.akunmasyarakat.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h4 class="fw-bold mb-0 text-dark">Detail Akun Masyarakat</h4>
            <small class="text-muted">Informasi lengkap akun warga</small>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card shadow-sm border-0">

                {{-- Avatar Header --}}
                <div class="card-body text-center py-4 border-bottom">
                    <div class="avatar-lg mx-auto mb-3">
                        {{ strtoupper(substr($akunmasyarakat->name, 0, 1)) }}
                    </div>
                    <h5 class="fw-bold mb-0">{{ $akunmasyarakat->name }}</h5>
                    <span class="badge bg-primary-subtle text-primary mt-1">Masyarakat</span>
                </div>

                {{-- Detail --}}
                <div class="card-body p-0">
                    <table class="table mb-0">
                        <tbody>
                            <tr>
                                <td class="ps-4 text-muted fw-medium" style="width: 40%;">NIK</td>
                                <td>
                                    <code class="bg-light rounded px-2 py-1 text-dark">
                                        {{ $akunmasyarakat->nik }}
                                    </code>
                                </td>
                            </tr>
                            <tr>
                                <td class="ps-4 text-muted fw-medium">Nama Lengkap</td>
                                <td>{{ $akunmasyarakat->name }}</td>
                            </tr>
                            <tr>
                                <td class="ps-4 text-muted fw-medium">Email</td>
                                <td>{{ $akunmasyarakat->email }}</td>
                            </tr>
                            <tr>
                                <td class="ps-4 text-muted fw-medium">Role</td>
                                <td>
                                    <span class="badge bg-success-subtle text-success text-capitalize">
                                        {{ $akunmasyarakat->role }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td class="ps-4 text-muted fw-medium">Terdaftar</td>
                                <td>{{ $akunmasyarakat->created_at->translatedFormat('d F Y, H:i') }}</td>
                            </tr>
                            <tr>
                                <td class="ps-4 text-muted fw-medium">Diperbarui</td>
                                <td>{{ $akunmasyarakat->updated_at->translatedFormat('d F Y, H:i') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- Actions --}}
                <div class="card-footer bg-white d-flex gap-2 py-3">
                    <a href="{{ route('admin.akunmasyarakat.edit', $akunmasyarakat->users_id) }}"
                       class="btn btn-warning btn-sm px-3">
                        <i class="bi bi-pencil me-1"></i> Edit
                    </a>
                    <form action="{{ route('admin.akunmasyarakat.destroy', $akunmasyarakat->users_id) }}"
                          method="POST"
                          onsubmit="return confirm('Hapus akun {{ addslashes($akunmasyarakat->name) }}?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm px-3">
                            <i class="bi bi-trash me-1"></i> Hapus
                        </button>
                    </form>
                    <a href="{{ route('admin.akunmasyarakat.index') }}" class="btn btn-outline-secondary btn-sm ms-auto px-3">
                        Kembali
                    </a>
                </div>

            </div>
        </div>
    </div>
</div>

<style>
.avatar-lg {
    width: 72px;
    height: 72px;
    border-radius: 50%;
    background: #e8f0fe;
    color: #3b5bdb;
    font-weight: 800;
    font-size: 1.75rem;
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>
@endsection