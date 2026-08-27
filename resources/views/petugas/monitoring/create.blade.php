@extends('admin.layouts.app')

@section('title', 'Evaluasi Dampak Penyaluran')
@section('page-title', 'Evaluasi Dampak Penyaluran')
@section('breadcrumb', 'Evaluasi')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('admin.petugas.monitoring.index') }}" class="btn btn-outline-secondary me-3">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
        <h4 class="fw-bold mb-0"><i class="bi bi-shield-check-fill me-2"></i>Formulir Evaluasi Dampak Bantuan</h4>
    </div>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm mb-4" role="alert">
            <i class="bi bi-x-circle-fill me-2"></i>Terdapat kesalahan pada input Anda. Silakan periksa kembali.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        {{-- Data Penyaluran & Analisis Otomatis --}}
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 rounded-3 mb-4">
                <div class="card-header fw-bold text-white border-0" style="background: #1E3A5F;">
                    <i class="bi bi-search me-2"></i> Analisis Otomatis Aspek Penyaluran (Waktu & Sasaran)
                </div>
                <div class="card-body bg-white rounded-bottom">
                    <div class="row g-3">
                        {{-- Analisis Aspek 1: WAKTU --}}
                        <div class="col-md-6">
                            <div class="p-3 border rounded bg-light">
                                <h6 class="fw-bold text-dark"><i class="bi bi-clock-history me-1 text-primary"></i> Aspek 1: Waktu Penyaluran</h6>
                                <table class="table table-sm table-borderless mb-0 mt-2 small">
                                    <tr>
                                        <th width="120">Tgl. Rencana</th>
                                        <td>: {{ $penyaluran->tanggal_pengambilan?->format('d M Y') ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Tgl. Realisasi</th>
                                        <td>: {{ $penyaluran->tanggal_realisasi?->format('d M Y') ?? '-' }}</td>
                                    </tr>
                                </table>
                                <div class="mt-3">
                                    <span class="fw-bold">Hasil Analisis:</span>
                                    <span class="badge {{ $ketepatanWaktu === 'Tepat Waktu' ? 'bg-success' : 'bg-danger' }} fs-6 d-inline-block ms-1">
                                        {{ $ketepatanWaktu }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Analisis Aspek 2: SASARAN --}}
                        <div class="col-md-6">
                            <div class="p-3 border rounded bg-light">
                                <h6 class="fw-bold text-dark"><i class="bi bi-people-fill me-1 text-success"></i> Aspek 2: Sasaran Penerima</h6>
                                <table class="table table-sm table-borderless mb-0 mt-2 small">
                                    <tr>
                                        <th width="120">Penerima Disetujui</th>
                                        <td>: {{ $penyaluran->hasilAkhir->pengajuan->nama ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Penerima Aktual</th>
                                        <td>: {{ $penyaluran->penerima_aktual ?? '-' }}</td>
                                    </tr>
                                </table>
                                <div class="mt-3">
                                    <span class="fw-bold">Hasil Analisis:</span>
                                    <span class="badge {{ $ketepatanSasaran === 'Sesuai Sasaran' ? 'bg-success' : 'bg-danger' }} fs-6 d-inline-block ms-1">
                                        {{ $ketepatanSasaran }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Input Aspek 3: DAMPAK --}}
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header fw-bold text-white border-0 bg-warning text-dark">
                    <i class="bi bi-chat-left-heart-fill me-2"></i> Aspek 3: Evaluasi Dampak Sosial Ekonomi
                </div>
                <div class="card-body bg-white rounded-bottom">
                    <form action="{{ route('admin.petugas.monitoring.store', $penyaluran->id) }}" method="POST">
                        @csrf

                        {{-- Pilihan Dampak --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold">Dampak Bantuan Bagi Penerima <span class="text-danger">*</span></label>
                            <div class="d-flex flex-wrap gap-3">
                                @foreach(['Sangat Membantu', 'Membantu', 'Cukup Membantu', 'Tidak Membantu'] as $opt)
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="dampak" id="dampak_{{ Str::slug($opt) }}" 
                                               value="{{ $opt }}" {{ old('dampak', $penyaluran->monitoring->dampak ?? '') === $opt ? 'checked' : '' }} required>
                                        <label class="form-check-label fw-semibold" for="dampak_{{ Str::slug($opt) }}">
                                            {{ $opt }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            @error('dampak')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Keterangan Dampak --}}
                        <div class="mb-4">
                            <label for="keterangan_dampak" class="form-label fw-bold">Keterangan Dampak Penerimaan Bantuan <span class="text-danger">*</span></label>
                            <textarea name="keterangan_dampak" id="keterangan_dampak" 
                                      class="form-control @error('keterangan_dampak') is-invalid @enderror" 
                                      rows="5" placeholder="Tuliskan keterangan evaluasi dampak secara rinci. Contoh: Dampak bantuan membantu memenuhi kebutuhan pangan keluarga penerima." required>{{ old('keterangan_dampak', $penyaluran->monitoring->keterangan_dampak ?? '') }}</textarea>
                            <div class="form-text text-muted">Jelaskan secara kualitatif bagaimana bantuan sosial tersebut memengaruhi kehidupan ekonomi/sosial penerima.</div>
                            @error('keterangan_dampak')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Log Monitoring Sebelumnya (jika edit) --}}
                        @if($penyaluran->monitoring)
                            <div class="alert alert-light border small text-muted py-2 mb-4">
                                <i class="bi bi-clock-history me-1"></i> Terakhir dievaluasi oleh <strong>{{ $penyaluran->monitoring->petugas->name ?? '-' }}</strong> pada {{ $penyaluran->monitoring->tanggal_monitoring?->format('d M Y') }}.
                            </div>
                        @endif

                        <div class="d-flex justify-content-end gap-2">
                            <button type="button" class="btn btn-light" onclick="history.back()">Batal</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-1"></i> Simpan Evaluasi Dampak
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Panel Kanan --}}
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 rounded-3 mb-4">
                <div class="card-header fw-bold text-white border-0 bg-dark">
                    <i class="bi bi-journal-text me-2"></i> Rincian Penyaluran
                </div>
                <div class="card-body bg-white rounded-bottom">
                    <table class="table table-sm table-borderless mb-0 small">
                        <tr>
                            <th width="120">Nama Penerima</th>
                            <td>: {{ $penyaluran->hasilAkhir->pengajuan->nama ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>NIK</th>
                            <td>: {{ $penyaluran->hasilAkhir->pengajuan->nik ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Jenis Bantuan</th>
                            <td>: {{ $penyaluran->hasilAkhir->pengajuan->bantuanSosial->nama_bantuan ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Lokasi</th>
                            <td>: {{ $penyaluran->lokasi_pengambilan ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Realisasi Waktu</th>
                            <td>: {{ $penyaluran->waktu_realisasi ?? '-' }} WIB</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
