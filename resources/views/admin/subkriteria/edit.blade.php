@extends('admin.layouts.app')

@section('title', 'Edit Sub Kriteria')

@section('content')
<div class="container-fluid">

    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('admin.subkriteria.index') }}"
           class="btn btn-outline-secondary me-3 rounded-3">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h4 class="fw-bold mb-0">
                <i class="bi bi-pencil-square me-2 text-primary"></i>Edit Sub Kriteria
            </h4>
            <small class="text-muted">Perbarui data sub kriteria</small>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger rounded-3">
            <i class="bi bi-exclamation-triangle me-2"></i>
            <ul class="mb-0 mt-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-4" style="max-width:600px">
        <div class="card-header bg-white border-bottom py-3 rounded-top-4">
            <h6 class="fw-bold mb-0 text-primary">
                <i class="bi bi-pencil me-2"></i>Form Edit Sub Kriteria
            </h6>
        </div>
        <div class="card-body p-4">
            <form action="{{ route('admin.subkriteria.update', $subkriteria->subkriteria_id) }}"
                  method="POST">
                @csrf @method('PUT')

                <div class="mb-4">
                    <label class="form-label fw-semibold">
                        Kriteria <span class="text-danger">*</span>
                    </label>
                    <select name="kriteria_id"
                            class="form-select rounded-3 @error('kriteria_id') is-invalid @enderror"
                            required>
                        <option value="">-- Pilih Kriteria --</option>
                        @foreach($kriterias as $k)
                            <option value="{{ $k->kriteria_id }}"
                                {{ old('kriteria_id', $subkriteria->kriteria_id) == $k->kriteria_id ? 'selected' : '' }}>
                                {{ $k->nama }}
                            </option>
                        @endforeach
                    </select>
                    @error('kriteria_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">
                        Nama Sub Kriteria <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="nama"
                           class="form-control rounded-3 @error('nama') is-invalid @enderror"
                           value="{{ old('nama', $subkriteria->nama) }}">
                    @error('nama')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">
                        Nilai <span class="text-danger">*</span>
                    </label>
                    <input type="number" name="nilai" step="0.01"
                           class="form-control rounded-3 @error('nilai') is-invalid @enderror"
                           value="{{ old('nilai', $subkriteria->nilai) }}">
                    @error('nilai')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">
                        Nilai numerik yang mewakili sub kriteria ini dalam perhitungan MOORA.
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary rounded-3 px-4">
                        <i class="bi bi-save me-1"></i> Update
                    </button>
                    <a href="{{ route('admin.subkriteria.index') }}"
                       class="btn btn-secondary rounded-3">Batal</a>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection