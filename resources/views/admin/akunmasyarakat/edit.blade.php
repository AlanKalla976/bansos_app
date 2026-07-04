@extends('admin.layouts.app')

@section('title', 'Edit Akun Masyarakat')

@section('content')
<div class="container-fluid py-4">

    {{-- Header --}}
    <div class="d-flex align-items-center gap-2 mb-4">
        <a href="{{ route('admin.akunmasyarakat.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h4 class="fw-bold mb-0 text-dark">Edit Akun Masyarakat</h4>
            <small class="text-muted">Perbarui data akun <strong>{{ $akunmasyarakat->name }}</strong></small>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">

                    <form action="{{ route('admin.akunmasyarakat.update', $akunmasyarakat->users_id) }}" method="POST" novalidate>
                        @csrf
                        @method('PUT')

                        {{-- NIK --}}
                        <div class="mb-3">
                            <label for="nik" class="form-label fw-medium">
                                NIK <span class="text-danger">*</span>
                            </label>
                            <input
                                type="text"
                                id="nik"
                                name="nik"
                                value="{{ old('nik', $akunmasyarakat->nik) }}"
                                maxlength="16"
                                class="form-control @error('nik') is-invalid @enderror"
                                placeholder="16 digit NIK"
                                inputmode="numeric"
                            >
                            @error('nik')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Nama --}}
                        <div class="mb-3">
                            <label for="name" class="form-label fw-medium">
                                Nama Lengkap <span class="text-danger">*</span>
                            </label>
                            <input
                                type="text"
                                id="name"
                                name="name"
                                value="{{ old('name', $akunmasyarakat->name) }}"
                                class="form-control @error('name') is-invalid @enderror"
                                placeholder="Nama lengkap sesuai KTP"
                            >
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Email --}}
                        <div class="mb-3">
                            <label for="email" class="form-label fw-medium">
                                Email <span class="text-danger">*</span>
                            </label>
                            <input
                                type="email"
                                id="email"
                                name="email"
                                value="{{ old('email', $akunmasyarakat->email) }}"
                                class="form-control @error('email') is-invalid @enderror"
                                placeholder="contoh@email.com"
                            >
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Password baru (opsional) --}}
                        <div class="mb-1">
                            <label for="password" class="form-label fw-medium">Password Baru</label>
                            <div class="input-group">
                                <input
                                    type="password"
                                    id="password"
                                    name="password"
                                    class="form-control @error('password') is-invalid @enderror"
                                    placeholder="Kosongkan jika tidak ingin mengubah"
                                >
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password', this)">
                                    <i class="bi bi-eye"></i>
                                </button>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <p class="text-muted small mb-3">Biarkan kosong apabila tidak ingin mengubah password.</p>

                        {{-- Konfirmasi Password --}}
                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label fw-medium">Konfirmasi Password Baru</label>
                            <div class="input-group">
                                <input
                                    type="password"
                                    id="password_confirmation"
                                    name="password_confirmation"
                                    class="form-control"
                                    placeholder="Ulangi password baru"
                                >
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password_confirmation', this)">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-warning px-4">
                                <i class="bi bi-save me-1"></i> Perbarui Akun
                            </button>
                            <a href="{{ route('admin.akunmasyarakat.index') }}" class="btn btn-outline-secondary px-3">
                                Batal
                            </a>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

<script>
function togglePassword(fieldId, btn) {
    const input = document.getElementById(fieldId);
    const icon  = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('bi-eye', 'bi-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('bi-eye-slash', 'bi-eye');
    }
}
</script>
@endsection