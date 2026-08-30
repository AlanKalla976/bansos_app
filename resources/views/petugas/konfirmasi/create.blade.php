@extends('admin.layouts.app')

@section('title', 'Konfirmasi Pengambilan Bantuan')
@section('page-title', 'Konfirmasi Pengambilan Bantuan')
@section('breadcrumb', 'Konfirmasi')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('admin.petugas.konfirmasi.index', ['status' => 'Sudah Dijadwalkan']) }}" class="btn btn-outline-secondary me-3">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
        <h4 class="fw-bold mb-0"><i class="bi bi-check2-square me-2"></i>Konfirmasi Realisasi Pengambilan</h4>
    </div>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm mb-4" role="alert">
            <i class="bi bi-x-circle-fill me-2"></i>
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        {{-- Form Konfirmasi --}}
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header fw-bold text-white border-0 bg-success">
                    <i class="bi bi-card-checklist me-2"></i> Pencatatan Penyerahan Bantuan Sosial
                </div>
                <div class="card-body bg-white rounded-bottom">
                    <form action="{{ route('admin.petugas.konfirmasi.proses', $penyaluran->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        {{-- Identitas Target Penerima --}}
                        <div class="alert alert-info py-3 mb-4">
                            <h6 class="fw-bold mb-2"><i class="bi bi-info-circle-fill me-1"></i> Data Penerima Terdaftar (Sesuai Persetujuan Lurah)</h6>
                            <table class="table table-sm table-borderless mb-0" style="font-size: 0.85rem;">
                                <tr>
                                    <th width="150">Nama Penerima</th>
                                    <td>: <strong>{{ $penyaluran->hasilAkhir->pengajuan->nama ?? '-' }}</strong></td>
                                </tr>
                                <tr>
                                    <th>NIK</th>
                                    <td>: {{ $penyaluran->hasilAkhir->pengajuan->nik ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Jenis Bantuan</th>
                                    <td>: <span class="badge bg-primary">{{ $penyaluran->hasilAkhir->pengajuan->bantuanSosial->nama_bantuan ?? '-' }}</span></td>
                                </tr>
                                <tr>
                                    <th>Jadwal Lokasi</th>
                                    <td>: {{ $penyaluran->lokasi_pengambilan }} ({{ $penyaluran->tanggal_pengambilan?->format('d M Y') }})</td>
                                </tr>
                            </table>
                        </div>

                        {{-- Realisasi --}}
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="tanggal_realisasi" class="form-label fw-bold">Tanggal Realisasi Pengambilan <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_realisasi" id="tanggal_realisasi" 
                                       class="form-control @error('tanggal_realisasi') is-invalid @enderror" 
                                       value="{{ old('tanggal_realisasi', date('Y-m-d')) }}" required>
                                @error('tanggal_realisasi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="waktu_realisasi" class="form-label fw-bold">Waktu Realisasi Pengambilan <span class="text-danger">*</span></label>
                                <input type="time" name="waktu_realisasi" id="waktu_realisasi" 
                                       class="form-control @error('waktu_realisasi') is-invalid @enderror" 
                                       value="{{ old('waktu_realisasi', date('H:i')) }}" required>
                                @error('waktu_realisasi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Penerima Aktual --}}
                        <div class="mb-3">
                            <label for="penerima_aktual" class="form-label fw-bold">Nama Penerima Aktual <span class="text-danger">*</span></label>
                            <input type="text" name="penerima_aktual" id="penerima_aktual" 
                                   class="form-control @error('penerima_aktual') is-invalid @enderror" 
                                   placeholder="Tulis nama pengambil bantuan..." 
                                   value="{{ old('penerima_aktual', $penyaluran->hasilAkhir->pengajuan->nama ?? '') }}" required>
                            <div class="form-text text-muted">
                                * Nama harus sesuai dengan penerima terdaftar. Jika diwakilkan anggota keluarga, sertakan nama perwakilan (Contoh: "Budi Santoso (diwakilkan oleh Ani - Istri)").
                            </div>
                            @error('penerima_aktual')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Dokumentasi Penyerahan --}}
                        <div class="mb-3">
                            <label for="foto_dokumentasi" class="form-label fw-bold">Foto Bukti Penyerahan (Dokumentasi)</label>
                            <input type="file" name="foto_dokumentasi" id="foto_dokumentasi" 
                                   class="form-control @error('foto_dokumentasi') is-invalid @enderror" 
                                   accept="image/*">
                            <div class="form-text text-muted">Format gambar (JPEG, PNG, JPG), maksimal 2MB.</div>
                            @error('foto_dokumentasi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Keterangan --}}
                        <div class="mb-4">
                            <label for="keterangan" class="form-label fw-bold">Catatan / Keterangan Tambahan</label>
                            <textarea name="keterangan" id="keterangan" class="form-control @error('keterangan') is-invalid @enderror" 
                                      rows="3" placeholder="Contoh: Bantuan diterima dalam keadaan baik. Identitas KTP asli terverifikasi.">{{ old('keterangan') }}</textarea>
                            @error('keterangan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <button type="button" class="btn btn-light" onclick="history.back()">Batal</button>
                            <button type="submit" class="btn btn-success" onclick="return confirm('Apakah Anda yakin data realisasi ini sudah benar? Setelah dikonfirmasi, status penyaluran akan ditandai Sudah Diambil.')">
                                <i class="bi bi-check-circle-fill me-1"></i> Konfirmasi Sudah Diambil
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Aturan Penyerahan --}}
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header fw-bold text-white border-0 bg-dark">
                    <i class="bi bi-shield-lock-fill me-2"></i> Aturan Konfirmasi Penyerahan
                </div>
                <div class="card-body bg-white rounded-bottom">
                    <ul class="mb-0 ps-3" style="font-size: 0.9rem;">
                        <li class="mb-2"><strong>Penerima Aktual:</strong> Wajib menyertakan nama penerima terdaftar. Pengambilan perwakilan diperbolehkan dengan menyertakan keterangan hubungan keluarga.</li>
                        <li class="mb-2"><strong>Foto Bukti:</strong> Sangat disarankan untuk memfoto warga saat memegang bantuan sosial sebagai transparansi dokumentasi kelurahan.</li>
                        <li class="mb-2"><strong>Keamanan:</strong> Penyaluran yang sudah dikonfirmasi tidak dapat diedit kembali oleh Petugas demi integritas data laporan monitoring.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
