@extends('admin.layouts.app')

@section('title', 'Detail Evaluasi Dampak')
@section('page-title', 'Detail Evaluasi Dampak')
@section('breadcrumb', 'Detail Evaluasi')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route($routePrefix . '.monitoring.index') }}" class="btn btn-outline-secondary me-3">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
        <h4 class="fw-bold mb-0"><i class="bi bi-shield-check-fill me-2"></i>Detail Evaluasi Dampak Bantuan</h4>
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

            {{-- Detail Aspek 3: DAMPAK --}}
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header fw-bold text-white border-0 bg-info">
                    <i class="bi bi-chat-left-heart-fill me-2"></i> Aspek 3: Umpan Balik Evaluasi Dampak
                </div>
                <div class="card-body bg-white rounded-bottom">
                    @if($penyaluran->monitoring)
                        {{-- Nilai Dampak --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold d-block">Tingkat Dampak Bantuan</label>
                            @php
                                $dampak = $penyaluran->monitoring->dampak;
                                $badgeClass = 'bg-secondary';
                                if ($dampak === 'Sangat Membantu' || $dampak === 'Membantu') {
                                    $badgeClass = 'bg-success';
                                } elseif ($dampak === 'Cukup Membantu') {
                                    $badgeClass = 'bg-warning text-dark';
                                } elseif ($dampak === 'Tidak Membantu') {
                                    $badgeClass = 'bg-danger';
                                }
                            @endphp
                            <span class="badge {{ $badgeClass }} fs-6 px-3 py-2 rounded-pill">
                                <i class="bi bi-emoji-smile-fill me-1"></i> {{ $dampak }}
                            </span>
                        </div>

                        {{-- Keterangan Dampak --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold">Ulasan / Keterangan Dampak</label>
                            <div class="p-3 bg-light rounded border border-light" style="font-size: 0.95rem; font-style: italic; color: #334155; line-height: 1.6;">
                                "{{ $penyaluran->monitoring->keterangan_dampak }}"
                            </div>
                        </div>

                        {{-- Foto Penggunaan Utama --}}
                        @if($penyaluran->monitoring->foto_penggunaan)
                            <div class="mb-4">
                                <label class="form-label fw-bold d-block">Foto Bukti Penggunaan Bantuan</label>
                                <div class="bg-light p-2 rounded text-center border">
                                    <a href="{{ asset('storage/' . $penyaluran->monitoring->foto_penggunaan) }}" target="_blank">
                                        <img src="{{ asset('storage/' . $penyaluran->monitoring->foto_penggunaan) }}" class="img-fluid rounded" style="max-height: 400px; box-shadow: 0 4px 10px rgba(0,0,0,0.08);">
                                    </a>
                                    <div class="form-text mt-2 text-muted">* Klik gambar untuk memperbesar</div>
                                </div>
                            </div>
                        @endif

                        {{-- Metadata Pengiriman --}}
                        <div class="alert alert-light border small text-muted py-2 mb-0">
                            <i class="bi bi-info-circle me-1"></i> Dikirim secara mandiri oleh penerima bantuan pada {{ $penyaluran->monitoring->tanggal_monitoring?->format('d M Y') ?? $penyaluran->monitoring->created_at->format('d M Y') }}.
                        </div>
                    @else
                        <div class="alert alert-warning mb-0">
                            <i class="bi bi-exclamation-triangle me-2"></i> Penerima bantuan belum mengirimkan umpan balik evaluasi dampak untuk penyaluran ini.
                        </div>
                    @endif
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
                        @if($penyaluran->monitoring && $penyaluran->monitoring->foto_penggunaan)
                        <tr>
                            <td colspan="2" class="pt-3 border-top">
                                <span class="d-block small text-muted fw-bold mb-1"><i class="bi bi-image me-1"></i>Foto Bukti Penggunaan Bantuan:</span>
                                <a href="{{ asset('storage/' . $penyaluran->monitoring->foto_penggunaan) }}" target="_blank">
                                    <img src="{{ asset('storage/' . $penyaluran->monitoring->foto_penggunaan) }}" class="img-thumbnail w-100" style="max-height: 250px; object-fit: cover; border-radius: 8px;">
                                </a>
                            </td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
