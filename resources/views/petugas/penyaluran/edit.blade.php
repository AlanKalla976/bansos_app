@extends('admin.layouts.app')

@section('title', 'Atur Jadwal Penyaluran')
@section('page-title', 'Atur Jadwal Penyaluran')
@section('breadcrumb', 'Atur Jadwal')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('admin.petugas.penyaluran.index') }}" class="btn btn-outline-secondary me-3">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
        <h4 class="fw-bold mb-0"><i class="bi bi-calendar-event me-2"></i>Atur Jadwal Penyaluran Bantuan</h4>
    </div>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm mb-4" role="alert">
            <i class="bi bi-x-circle-fill me-2"></i>Terdapat kesalahan pada input Anda. Silakan periksa kembali.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        {{-- Form Penjadwalan --}}
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header fw-bold text-white border-0" style="background: #1E3A5F;">
                    <i class="bi bi-pencil-square me-2"></i> Formulir Jadwal Pengambilan
                </div>
                <div class="card-body bg-white rounded-bottom">
                    <form action="{{ route('admin.petugas.penyaluran.update', $penyaluran->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        {{-- Info Penerima --}}
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Nama Penerima</label>
                                <input type="text" class="form-control bg-light" value="{{ $penyaluran->hasilAkhir->pengajuan->nama ?? '-' }}" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">NIK Penerima</label>
                                <input type="text" class="form-control bg-light" value="{{ $penyaluran->hasilAkhir->pengajuan->nik ?? '-' }}" readonly>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Jenis Bantuan Sosial</label>
                                <input type="text" class="form-control bg-light" value="{{ $penyaluran->hasilAkhir->pengajuan->bantuanSosial->nama_bantuan ?? '-' }}" readonly>
                            </div>
                            <div class="col-md-6">
                                <label for="status" class="form-label fw-bold">Status Penyaluran <span class="text-danger">*</span></label>
                                <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                                    <option value="Sudah Dijadwalkan" {{ old('status', $penyaluran->status) === 'Sudah Dijadwalkan' || $penyaluran->status === 'Belum Dijadwalkan' ? 'selected' : '' }}>Sudah Dijadwalkan</option>
                                    <option value="Sudah Diambil" {{ old('status', $penyaluran->status) === 'Sudah Diambil' ? 'selected' : '' }}>Sudah Diambil</option>
                                    <option value="Tidak Diambil" {{ old('status', $penyaluran->status) === 'Tidak Diambil' ? 'selected' : '' }}>Tidak Diambil</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <hr class="my-4">

                        {{-- Tanggal & Waktu --}}
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="tanggal_pengambilan" class="form-label fw-bold">Tanggal Pengambilan <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_pengambilan" id="tanggal_pengambilan" 
                                       class="form-control @error('tanggal_pengambilan') is-invalid @enderror" 
                                       value="{{ old('tanggal_pengambilan', $penyaluran->tanggal_pengambilan?->format('Y-m-d')) }}" required>
                                @error('tanggal_pengambilan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="waktu_mulai" class="form-label fw-bold">Waktu Mulai <span class="text-danger">*</span></label>
                                <input type="time" name="waktu_mulai" id="waktu_mulai" 
                                       class="form-control @error('waktu_mulai') is-invalid @enderror" 
                                       value="{{ old('waktu_mulai', $penyaluran->waktu_mulai ? substr($penyaluran->waktu_mulai, 0, 5) : '') }}" required>
                                @error('waktu_mulai')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="waktu_selesai" class="form-label fw-bold">Waktu Selesai <span class="text-danger">*</span></label>
                                <input type="time" name="waktu_selesai" id="waktu_selesai" 
                                       class="form-control @error('waktu_selesai') is-invalid @enderror" 
                                       value="{{ old('waktu_selesai', $penyaluran->waktu_selesai ? substr($penyaluran->waktu_selesai, 0, 5) : '') }}" required>
                                @error('waktu_selesai')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Lokasi --}}
                        <div class="mb-3">
                            <label for="lokasi_pengambilan" class="form-label fw-bold">Lokasi Pengambilan <span class="text-danger">*</span></label>
                            <input type="text" name="lokasi_pengambilan" id="lokasi_pengambilan" 
                                   class="form-control @error('lokasi_pengambilan') is-invalid @enderror" 
                                   placeholder="Contoh: Kantor Kelurahan Harjamukti"
                                   value="{{ old('lokasi_pengambilan', $penyaluran->lokasi_pengambilan) }}" required>
                            @error('lokasi_pengambilan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Keterangan / Persyaratan --}}
                        <div class="mb-4">
                            <label for="keterangan" class="form-label fw-bold">Keterangan / Persyaratan Dokumen</label>
                            <textarea name="keterangan" id="keterangan" class="form-control @error('keterangan') is-invalid @enderror" 
                                      rows="4" placeholder="Contoh: Membawa KTP asli dan Kartu Keluarga (KK) fotokopi.">{{ old('keterangan', $penyaluran->keterangan) }}</textarea>
                            @error('keterangan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <button type="reset" class="btn btn-light">Reset</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-1"></i> Simpan Jadwal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Info Box --}}
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 rounded-3 mb-4">
                <div class="card-header fw-bold text-white border-0" style="background: #2D6A4F;">
                    <i class="bi bi-info-circle-fill me-2"></i> Aturan Bisnis Penjadwalan
                </div>
                <div class="card-body bg-white rounded-bottom">
                    <ul class="mb-0 ps-3">
                        <li class="mb-2">Hanya penerima bantuan yang telah **disetujui oleh Lurah** yang dapat dibuatkan jadwal pengambilan.</li>
                        <li class="mb-2">Pastikan lokasi dan waktu pengambilan diisi secara jelas dan akurat agar tidak membingungkan warga.</li>
                        <li class="mb-2">Ketika jadwal selesai disimpan, status otomatis berubah menjadi **Sudah Dijadwalkan** (atau status pilihan Anda).</li>
                        <li>Riwayat petugas pembuat jadwal dan waktu pembuatan akan tercatat di sistem sebagai bagian dari audit logs.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
