@extends('admin.layouts.app')

@section('title', 'Edit Bantuan Sosial')
@section('page-title', 'Data Bantuan Sosial')
@section('breadcrumb', 'Jenis Bantuan')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('admin.bantuansosial.index') }}" class="btn btn-outline-secondary me-3">
            <i class="bi bi-arrow-left"></i>
        </a>
        <h4 class="fw-bold mb-0"><i class="bi bi-pencil-square me-2"></i>Edit Bantuan Sosial</h4>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.bantuansosial.update', $bantuansosial) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="nama_bantuan" class="form-label fw-semibold">Nama Bantuan <span class="text-danger">*</span></label>
                    <input type="text" name="nama_bantuan" id="nama_bantuan"
                           class="form-control @error('nama_bantuan') is-invalid @enderror"
                           value="{{ old('nama_bantuan', $bantuansosial->nama_bantuan) }}">
                    @error('nama_bantuan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="kuota" class="form-label fw-semibold">Kuota <span class="text-danger">*</span></label>
                    <input type="number" name="kuota" id="kuota" min="0"
                           class="form-control @error('kuota') is-invalid @enderror"
                           value="{{ old('kuota', $bantuansosial->kuota) }}">
                    @error('kuota')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="deskripsi" class="form-label fw-semibold">Deskripsi</label>
                    <textarea name="deskripsi" id="deskripsi" rows="4"
                              class="form-control @error('deskripsi') is-invalid @enderror">{{ old('deskripsi', $bantuansosial->deskripsi) }}</textarea>
                    @error('deskripsi')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i> Update
                    </button>
                    <a href="{{ route('admin.bantuansosial.index') }}" class="btn btn-secondary">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection