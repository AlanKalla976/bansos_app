@extends('admin.layouts.app')

@section('title', 'Detail Sub Kriteria')
@section('page-title', 'Sub Kriteria')
@section('breadcrumb', 'Sub Kriteria')

@section('content')
<div class="container-fluid">

    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('admin.subkriteria.index') }}"
           class="btn btn-outline-secondary me-3 rounded-3">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h4 class="fw-bold mb-0">
                <i class="bi bi-info-circle me-2 text-primary"></i>Detail Sub Kriteria
            </h4>
            <small class="text-muted">Informasi lengkap sub kriteria</small>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4" style="max-width:600px">
        <div class="card-header bg-white border-bottom py-3 rounded-top-4">
            <h6 class="fw-bold mb-0 text-primary">
                <i class="bi bi-node-plus me-2"></i>Informasi Sub Kriteria
            </h6>
        </div>
        <div class="card-body p-0">
            <table class="table table-borderless mb-0">
                <tr class="border-bottom">
                    <th class="ps-4 py-3 text-muted fw-normal" width="180">Nama Kriteria</th>
                    <td class="py-3">
                        <span class="badge bg-primary me-1">
                            {{ $subkriteria->kriteria->kode_kriteria }}
                        </span>
                        {{ $subkriteria->kriteria->nama }}
                    </td>
                </tr>
                <tr class="border-bottom">
                    <th class="ps-4 py-3 text-muted fw-normal">Tipe Kriteria</th>
                    <td class="py-3">
                        <span class="badge {{ $subkriteria->kriteria->tipe === 'benefit' ? 'bg-success' : 'bg-danger' }} rounded-pill px-3">
                            {{ ucfirst($subkriteria->kriteria->tipe) }}
                        </span>
                    </td>
                </tr>
                <tr class="border-bottom">
                    <th class="ps-4 py-3 text-muted fw-normal">Nama Sub Kriteria</th>
                    <td class="py-3 fw-semibold">{{ $subkriteria->nama }}</td>
                </tr>
                <tr class="border-bottom">
                    <th class="ps-4 py-3 text-muted fw-normal">Nilai</th>
                    <td class="py-3">
                        <span class="badge bg-primary rounded-pill px-3 py-2 fs-6">
                            {{ number_format($subkriteria->nilai, 2) }}
                        </span>
                    </td>
                </tr>
                <tr class="border-bottom">
                    <th class="ps-4 py-3 text-muted fw-normal">Dibuat</th>
                    <td class="py-3">{{ $subkriteria->created_at->format('d/m/Y H:i') }}</td>
                </tr>
                <tr>
                    <th class="ps-4 py-3 text-muted fw-normal">Diperbarui</th>
                    <td class="py-3">{{ $subkriteria->updated_at->format('d/m/Y H:i') }}</td>
                </tr>
            </table>
        </div>
        <div class="card-footer bg-white border-top rounded-bottom-4 py-3 d-flex gap-2">
            <a href="{{ route('admin.subkriteria.edit', $subkriteria->subkriteria_id) }}"
               class="btn btn-warning rounded-3">
                <i class="bi bi-pencil me-1"></i> Edit
            </a>
            <form action="{{ route('admin.subkriteria.destroy', $subkriteria->subkriteria_id) }}"
                  method="POST"
                  onsubmit="return confirm('Yakin ingin menghapus sub kriteria ini?')">
                @csrf @method('DELETE')
                <button class="btn btn-danger rounded-3">
                    <i class="bi bi-trash me-1"></i> Hapus
                </button>
            </form>
            <a href="{{ route('admin.subkriteria.index') }}"
               class="btn btn-outline-secondary rounded-3 ms-auto">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

</div>
@endsection