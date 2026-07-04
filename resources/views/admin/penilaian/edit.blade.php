@extends('admin.layouts.app')

@section('title', 'Edit Penilaian')

@section('content')
<div class="container-fluid">

    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('admin.penilaian.index') }}"
           class="btn btn-outline-secondary me-3 rounded-3">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h4 class="fw-bold mb-0">
                <i class="bi bi-pencil-square me-2 text-primary"></i>Edit Penilaian
            </h4>
            <small class="text-muted">Perbarui penilaian kriteria</small>
        </div>
    </div>

    {{-- Info Pemohon --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <div class="row g-3">
                <div class="col-md-4">
                    <p class="text-muted small mb-1">Nama Pemohon</p>
                    <p class="fw-bold mb-0">{{ $pengajuan->nama }}</p>
                </div>
                <div class="col-md-4">
                    <p class="text-muted small mb-1">NIK</p>
                    <p class="fw-bold mb-0">{{ $pengajuan->nik }}</p>
                </div>
                <div class="col-md-4">
                    <p class="text-muted small mb-1">Jenis Bantuan</p>
                    <p class="fw-bold mb-0">
                        <span class="badge bg-primary rounded-pill px-3">
                            {{ $pengajuan->bantuanSosial->nama_bantuan ?? '-' }}
                        </span>
                    </p>
                </div>
            </div>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger rounded-3">
            <i class="bi bi-exclamation-triangle me-2"></i>
            <ul class="mb-0 mt-1">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.penilaian.store') }}" method="POST">
        @csrf
        <input type="hidden" name="pengajuan_id" value="{{ $pengajuan->id }}">

        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-bottom py-3 rounded-top-4">
                <h6 class="fw-bold mb-0 text-primary">
                    <i class="bi bi-list-check me-2"></i>Edit Penilaian Per Kriteria
                </h6>
            </div>
            <div class="card-body p-4">
                <div class="row g-4">
                    @foreach($kriterias as $idx => $k)
                    @php
                        $existing = $penilaians[$k->kriteria_id] ?? null;
                    @endphp
                    <div class="col-md-6">
                        <div class="card border rounded-3 h-100">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center mb-2">
                                    <span class="badge bg-primary me-2">{{ $k->kode_kriteria }}</span>
                                    <span class="fw-semibold">{{ $k->nama }}</span>
                                    <span class="badge {{ $k->tipe === 'benefit' ? 'bg-success' : 'bg-danger' }} ms-auto rounded-pill">
                                        {{ ucfirst($k->tipe) }}
                                    </span>
                                </div>

                                <input type="hidden" name="penilaian[{{ $idx }}][kriteria_id]"
                                       value="{{ $k->kriteria_id }}">

                                <select name="penilaian[{{ $idx }}][subkriteria_id]"
                                        class="form-select rounded-3 subkriteria-select"
                                        data-nilai-input="nilai_{{ $k->kriteria_id }}"
                                        required>
                                    <option value="">-- Pilih Sub Kriteria --</option>
                                    @foreach($k->subKriterias->sortByDesc('nilai') as $sub)
                                        <option value="{{ $sub->subkriteria_id }}"
                                                data-nilai="{{ $sub->nilai }}"
                                                {{ $existing && $existing->subkriteria_id == $sub->subkriteria_id ? 'selected' : '' }}>
                                            {{ $sub->nama }} (Nilai: {{ $sub->nilai }})
                                        </option>
                                    @endforeach
                                </select>

                                <input type="hidden"
                                       name="penilaian[{{ $idx }}][nilai]"
                                       id="nilai_{{ $k->kriteria_id }}"
                                       value="{{ $existing->nilai ?? '' }}">

                                <div class="mt-2 text-muted small">
                                    Nilai terpilih:
                                    <span id="label_{{ $k->kriteria_id }}" class="fw-semibold text-primary">
                                        {{ $existing->nilai ?? '-' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="card-footer bg-white border-top rounded-bottom-4 py-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary rounded-3 px-4">
                    <i class="bi bi-save me-2"></i>Update Penilaian
                </button>
                <a href="{{ route('admin.penilaian.index') }}" class="btn btn-secondary rounded-3">
                    Batal
                </a>
            </div>
        </div>
    </form>
</div>

<script>
document.querySelectorAll('.subkriteria-select').forEach(function(select) {
    select.addEventListener('change', function() {
        const inputId  = this.dataset.nilaiInput;
        const selected = this.options[this.selectedIndex];
        const nilai    = selected.dataset.nilai || '';
        const kriteriaId = inputId.replace('nilai_', '');

        document.getElementById(inputId).value = nilai;
        document.getElementById('label_' + kriteriaId).textContent = nilai ? nilai : '-';
    });
});
</script>
@endsection