@extends('user.layouts.app')

@section('title', 'Status Penerimaan Bantuan')
@section('page-title', 'Status & Jadwal Bantuan Anda')
@section('breadcrumb', 'Status Bantuan')

@push('styles')
<style>
.status-card {
    background: #fff;
    border-radius: 16px;
    border: 1px solid #E2E8F0;
    box-shadow: 0 4px 12px rgba(30,58,95,.04);
    transition: all .2s ease-in-out;
}
.status-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(30,58,95,.08);
}
.status-header {
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid #F1F5F9;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: .5rem;
}
.status-body {
    padding: 1.5rem;
}
.info-label {
    font-size: .75rem;
    font-weight: 600;
    color: #94A3B8;
    text-transform: uppercase;
    letter-spacing: .5px;
    margin-bottom: .25rem;
}
.info-value {
    font-size: .95rem;
    color: #1E293B;
    font-weight: 500;
}
</style>
@endpush

@section('content')
<div class="container-fluid py-4">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm mb-4" role="alert">
            <i class="bi bi-x-circle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="page-header mb-4">
        <div>
            <h2 class="page-header-title text-primary fw-bold">
                <i class="bi bi-calendar-check-fill me-2"></i>
                Status & Jadwal Bantuan Sosial
            </h2>
            <p class="text-muted">Pantau status pengajuan bantuan sosial, hasil persetujuan Lurah, dan jadwal pengambilan bantuan Anda di sini.</p>
        </div>
    </div>

    @if($pengajuans->isEmpty())
        <div class="card shadow-sm border-0 rounded-3 text-center p-5">
            <div class="card-body">
                <i class="bi bi-inbox text-muted display-1"></i>
                <h4 class="mt-4 fw-bold text-secondary">Belum Ada Pengajuan Bantuan</h4>
                <p class="text-muted">Anda belum mendaftarkan pengajuan bantuan sosial apapun saat ini.</p>
                <a href="{{ route('user.pengajuan.index') }}" class="btn btn-primary mt-2">
                    <i class="bi bi-plus-circle me-1"></i> Ajukan Bantuan Sekarang
                </a>
            </div>
        </div>
    @else
        <div class="row g-4">
            @foreach($pengajuans as $pengajuan)
                @php
                    $hasilAkhir = $pengajuan->hasilAkhir;
                    $penyaluran = $hasilAkhir?->penyaluran;
                    
                    $isDisetujuiLurah = $hasilAkhir && $hasilAkhir->persetujuan_status === 'Disetujui';
                    $isDitolakLurah = $hasilAkhir && $hasilAkhir->persetujuan_status === 'Ditolak';
                @endphp

                <div class="col-12">
                    <div class="status-card">
                        {{-- Card Header --}}
                        <div class="status-header">
                            <div>
                                <span class="badge bg-primary text-white mb-1" style="font-size: .7rem;">
                                    ID Pengajuan: #{{ $pengajuan->id }}
                                </span>
                                <h5 class="fw-bold text-dark mb-0">
                                    {{ $pengajuan->bantuanSosial->nama_bantuan ?? '-' }}
                                </h5>
                            </div>

                            <div>
                                {{-- Status Badges --}}
                                @if($isDisetujuiLurah)
                                    @if(!$penyaluran || $penyaluran->status === 'Belum Dijadwalkan')
                                        <span class="badge bg-warning text-dark fs-6 px-3 py-2 rounded-pill">
                                            <i class="bi bi-hourglass-split me-1"></i> Disetujui - Menunggu Jadwal Penyaluran
                                        </span>
                                    @elseif($penyaluran->status === 'Sudah Dijadwalkan')
                                        <span class="badge bg-success text-white fs-6 px-3 py-2 rounded-pill">
                                            <i class="bi bi-check-circle-fill me-1"></i> Siap Diambil
                                        </span>
                                    @elseif($penyaluran->status === 'Sudah Diambil')
                                        <span class="badge bg-info text-dark fs-6 px-3 py-2 rounded-pill">
                                            <i class="bi bi-bag-check-fill me-1"></i> Sudah Diambil
                                        </span>
                                    @elseif($penyaluran->status === 'Tidak Diambil')
                                        <span class="badge bg-danger text-white fs-6 px-3 py-2 rounded-pill">
                                            <i class="bi bi-bag-x-fill me-1"></i> Tidak Diambil
                                        </span>
                                    @endif
                                @elseif($isDitolakLurah)
                                    <span class="badge bg-danger text-white fs-6 px-3 py-2 rounded-pill">
                                        <i class="bi bi-x-circle-fill me-1"></i> Ditolak oleh Lurah
                                    </span>
                                @else
                                    {{-- Mengikuti status pengajuan biasa --}}
                                    @if($pengajuan->status === 'Menunggu')
                                        <span class="badge bg-warning text-dark fs-6 px-3 py-2 rounded-pill">
                                            <i class="bi bi-hourglass me-1"></i> Menunggu Validasi Berkas
                                        </span>
                                    @elseif($pengajuan->status === 'Diverifikasi')
                                        <span class="badge bg-info text-dark fs-6 px-3 py-2 rounded-pill">
                                            <i class="bi bi-patch-check me-1"></i> Berkas Valid - Sedang Seleksi
                                        </span>
                                    @elseif($pengajuan->status === 'Ditolak')
                                        <span class="badge bg-danger text-white fs-6 px-3 py-2 rounded-pill">
                                            <i class="bi bi-file-earmark-x me-1"></i> Berkas Tidak Valid / Ditolak Petugas
                                        </span>
                                    @else
                                        <span class="badge bg-secondary text-white fs-6 px-3 py-2 rounded-pill">
                                            {{ $pengajuan->status }}
                                        </span>
                                    @endif
                                @endif
                            </div>
                        </div>

                        {{-- Card Body --}}
                        <div class="status-body">
                            <div class="row g-3">
                                {{-- Jika Ditolak Berkas / Petugas --}}
                                @if($pengajuan->status === 'Ditolak')
                                    <div class="col-12">
                                        <div class="alert alert-danger mb-0">
                                            <h6 class="fw-bold"><i class="bi bi-exclamation-triangle-fill me-2"></i>Berkas Anda Dinyatakan Tidak Valid oleh Petugas</h6>
                                            <p class="mb-0 mt-1 small"><strong>Alasan Penolakan:</strong> {{ $pengajuan->alasan_penolakan ?? 'Berkas tidak lengkap atau tidak sesuai persyaratan.' }}</p>
                                        </div>
                                    </div>
                                @endif

                                {{-- Jika Ditolak Lurah --}}
                                @if($isDitolakLurah)
                                    <div class="col-12">
                                        <div class="alert alert-danger mb-0">
                                            <h6 class="fw-bold"><i class="bi bi-exclamation-triangle-fill me-2"></i>Pengajuan Anda Ditolak oleh Lurah</h6>
                                            <p class="mb-0 mt-1 small"><strong>Alasan Penolakan:</strong> {{ $hasilAkhir->alasan_penolakan_lurah ?? 'Tidak memenuhi kuota atau kriteria kelayakan lingkungan.' }}</p>
                                        </div>
                                    </div>
                                @endif

                                {{-- Detail Informasi Penyaluran --}}
                                @if($isDisetujuiLurah && $penyaluran)
                                    @if($penyaluran->status === 'Sudah Dijadwalkan')
                                        <div class="col-md-6 col-lg-3">
                                            <div class="info-label">Jenis Bantuan</div>
                                            <div class="info-value">{{ $pengajuan->bantuanSosial->nama_bantuan }}</div>
                                        </div>
                                        <div class="col-md-6 col-lg-3">
                                            <div class="info-label">Nilai / Deskripsi Bantuan</div>
                                            <div class="info-value text-secondary">{{ $pengajuan->bantuanSosial->deskripsi ?? 'Tersedia di lokasi pengambilan' }}</div>
                                        </div>
                                        <div class="col-md-6 col-lg-3">
                                            <div class="info-label">Tanggal Pengambilan</div>
                                            <div class="info-value fw-bold text-primary">
                                                <i class="bi bi-calendar3 me-1"></i>
                                                {{ $penyaluran->tanggal_pengambilan?->format('d M Y') }}
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-lg-3">
                                            <div class="info-label">Waktu Pengambilan</div>
                                            <div class="info-value fw-bold">
                                                <i class="bi bi-clock me-1"></i>
                                                {{ $penyaluran->waktu_mulai ? substr($penyaluran->waktu_mulai, 0, 5) : '-' }} - {{ $penyaluran->waktu_selesai ? substr($penyaluran->waktu_selesai, 0, 5) : '-' }} WIB
                                            </div>
                                        </div>
                                        <div class="col-md-12 col-lg-6 mt-3">
                                            <div class="info-label">Lokasi Pengambilan</div>
                                            <div class="info-value">
                                                <i class="bi bi-geo-alt-fill text-danger me-1"></i>
                                                {{ $penyaluran->lokasi_pengambilan }}
                                            </div>
                                        </div>
                                        <div class="col-md-12 col-lg-6 mt-3">
                                            <div class="info-label">Persyaratan / Keterangan</div>
                                            <div class="info-value text-muted bg-light p-2 rounded border border-light" style="font-size: 0.85rem;">
                                                {{ $penyaluran->keterangan ?: 'Membawa KTP asli.' }}
                                            </div>
                                        </div>
                                    @elseif($penyaluran->status === 'Sudah Diambil')
                                        <div class="col-12">
                                            <div class="alert alert-success d-flex align-items-center mb-3" role="alert">
                                                <i class="bi bi-check-circle-fill me-3 fs-4"></i>
                                                <div>
                                                    <h6 class="fw-bold mb-1">Bantuan Sosial Telah Diterima</h6>
                                                    <span class="small text-muted">
                                                        Bantuan jenis **{{ $pengajuan->bantuanSosial->nama_bantuan }}** telah sukses diambil pada: 
                                                        <strong>{{ $penyaluran->updated_at->format('d M Y') }}</strong> pukul <strong>{{ $penyaluran->updated_at->format('H:i') }} WIB</strong>.
                                                    </span>
                                                </div>
                                            </div>

                                            <div class="card border-0 rounded-3 shadow-sm" style="background: #F8FAFC; border: 1px solid #E2E8F0 !important;">
                                                <div class="card-body p-4">
                                                    <h6 class="fw-bold text-primary mb-3">
                                                        <i class="bi bi-chat-left-heart-fill me-2"></i>Evaluasi Dampak Bantuan (Umpan Balik Penerima)
                                                    </h6>
                                                    @if($penyaluran->monitoring)
                                                        <div class="bg-white p-3 rounded border border-light shadow-sm">
                                                            <div class="d-flex align-items-center mb-2">
                                                                 <span class="badge bg-success me-2" style="font-size: 0.8rem;">
                                                                     <i class="bi bi-emoji-smile-fill me-1"></i>{{ $penyaluran->monitoring->dampak }}
                                                                 </span>
                                                                 <small class="text-muted">Dikirim pada {{ $penyaluran->monitoring->tanggal_monitoring?->format('d M Y') }}</small>
                                                            </div>
                                                            <p class="mb-2 text-dark" style="font-size: 0.9rem; font-style: italic;">
                                                                "{{ $penyaluran->monitoring->keterangan_dampak }}"
                                                            </p>
                                                            @if($penyaluran->monitoring->foto_penggunaan)
                                                                <div class="mt-3">
                                                                    <span class="d-block small text-muted fw-bold mb-1"><i class="bi bi-image me-1"></i>Foto Bukti Penggunaan Bantuan:</span>
                                                                    <a href="{{ asset('storage/' . $penyaluran->monitoring->foto_penggunaan) }}" target="_blank">
                                                                        <img src="{{ asset('storage/' . $penyaluran->monitoring->foto_penggunaan) }}" class="img-thumbnail" style="max-height: 150px; border-radius: 8px;">
                                                                    </a>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    @else
                                                        <form action="{{ route('user.statusbantuan.evaluasi', $penyaluran->id) }}" method="POST" enctype="multipart/form-data">
                                                            @csrf
                                                            <div class="mb-3">
                                                                <label for="dampak_{{ $penyaluran->id }}" class="form-label small fw-bold">Bagaimana dampak bantuan ini bagi kehidupan Anda? <span class="text-danger">*</span></label>
                                                                <select name="dampak" id="dampak_{{ $penyaluran->id }}" class="form-select" required>
                                                                    <option value="">-- Pilih Penilaian --</option>
                                                                    <option value="Sangat Membantu">Sangat Membantu</option>
                                                                    <option value="Membantu">Membantu</option>
                                                                    <option value="Cukup Membantu">Cukup Membantu</option>
                                                                    <option value="Tidak Membantu">Tidak Membantu</option>
                                                                </select>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="keterangan_dampak_{{ $penyaluran->id }}" class="form-label small fw-bold">Keterangan / Ulasan Tambahan: <span class="text-danger">*</span></label>
                                                                <textarea name="keterangan_dampak" id="keterangan_dampak_{{ $penyaluran->id }}" class="form-control" rows="3" placeholder="Tuliskan pengalaman atau masukan Anda setelah menerima bantuan ini..." required></textarea>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="foto_penggunaan_{{ $penyaluran->id }}" class="form-label small fw-bold">Foto Bukti Penggunaan Bantuan: <span class="text-danger">*</span></label>
                                                                <input type="file" name="foto_penggunaan" id="foto_penggunaan_{{ $penyaluran->id }}" class="form-control" accept="image/*" required>
                                                                <div class="form-text text-muted" style="font-size: 0.75rem;">
                                                                    * Unggah foto bukti penggunaan bantuan sosial (Contoh: foto sembako yang dibeli, struk belanja, kwitansi sekolah, dll.). Maksimal 2MB.
                                                                </div>
                                                            </div>
                                                            <button type="submit" class="btn btn-primary btn-sm px-4 rounded-pill">
                                                                <i class="bi bi-send-fill me-1"></i> Kirim Umpan Balik
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @elseif($penyaluran->status === 'Tidak Diambil')
                                        <div class="col-12">
                                            <div class="alert alert-warning mb-0">
                                                <h6 class="fw-bold"><i class="bi bi-exclamation-circle-fill me-2"></i>Bantuan Tidak Diambil</h6>
                                                <p class="mb-0 mt-1 small">Jadwal pengambilan bantuan sosial telah terlewati dan Anda tercatat tidak mengambil bantuan pada tanggal {{ $penyaluran->tanggal_pengambilan?->format('d M Y') ?? '-' }}. Silakan hubungi petugas kelurahan untuk informasi lebih lanjut.</p>
                                            </div>
                                        </div>
                                    @endif
                                @elseif($isDisetujuiLurah && (!$penyaluran || $penyaluran->status === 'Belum Dijadwalkan'))
                                    <div class="col-12">
                                        <div class="alert alert-warning d-flex align-items-center mb-0" role="alert">
                                            <i class="bi bi-clock-history me-3 fs-4"></i>
                                            <div>
                                                <h6 class="fw-bold mb-1">Menunggu Penjadwalan Petugas</h6>
                                                <span class="small">Selamat! Pengajuan bantuan Anda telah disetujui Lurah. Saat ini petugas kelurahan sedang mempersiapkan jadwal dan lokasi pembagian bantuan sosial. Mohon periksa halaman ini secara berkala.</span>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    {{-- Info pengajuan biasa --}}
                                    <div class="col-md-6 col-lg-4">
                                        <div class="info-label">Tanggal Pengajuan</div>
                                        <div class="info-value">{{ $pengajuan->created_at->format('d M Y') }}</div>
                                    </div>
                                    <div class="col-md-6 col-lg-4">
                                        <div class="info-label">Tahapan Proses</div>
                                        <div class="info-value">
                                            @if($pengajuan->status === 'Menunggu')
                                                Pengecekan berkas oleh Petugas
                                            @elseif($pengajuan->status === 'Diverifikasi')
                                                Seleksi kriteria kelayakan (AHP & MOORA) oleh Admin
                                            @else
                                                -
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

</div>
@endsection
