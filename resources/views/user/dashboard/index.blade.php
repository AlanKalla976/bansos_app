@extends('user.layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('breadcrumb', 'Dashboard')

@push('styles')
<style>
.step-item {
    display: flex;
    gap: 1.25rem;
    margin-bottom: 1.5rem;
}
.step-item:last-child {
    margin-bottom: 0;
}
.step-number {
    flex-shrink: 0;
    width: 2.25rem;
    height: 2.25rem;
    border-radius: 50%;
    background: #EFF6FF;
    color: #1E3A8A;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 1.5px solid #DBEAFE;
    font-size: 0.95rem;
}
.step-content {
    padding-top: 0.15rem;
}
.step-title {
    font-weight: 700;
    color: #1E3A5F;
    margin-bottom: 0.25rem;
    font-size: 0.95rem;
}
.step-desc {
    color: #64748B;
    font-size: 0.83rem;
    line-height: 1.5;
}
.doc-card {
    background: #F8FAFC;
    border: 1px solid #E2E8F0;
    border-radius: 12px;
    padding: 1.25rem;
    transition: transform 0.2s, box-shadow 0.2s;
}
.doc-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
}
.doc-icon {
    font-size: 1.75rem;
    color: #3B82F6;
    margin-bottom: 0.75rem;
}
.doc-title {
    font-weight: 700;
    color: #1E3A5F;
    font-size: 0.9rem;
    margin-bottom: 0.35rem;
}
.doc-desc {
    color: #64748B;
    font-size: 0.8rem;
    line-height: 1.4;
}
</style>
@endpush

@section('content')

{{-- Welcome Banner --}}
<div class="welcome-banner">
    <div class="welcome-banner-content">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div>
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span style="display:inline-flex; align-items:center; gap:.4rem; background:rgba(249,199,79,.2); border:1px solid rgba(249,199,79,.3); color:var(--gold); padding:.25rem .7rem; border-radius:20px; font-size:.72rem; font-weight:700;">
                        <i class="bi bi-circle-fill" style="font-size:.4rem;"></i>Akun Aktif
                    </span>
                </div>
                <h5 class="fw-bold mb-1" style="font-size:1.3rem;">
                    Selamat datang, {{ $user->name }}! 👋
                </h5>
                <p class="mb-0" style="font-size:.83rem; color:rgba(255,255,255,.65);">
                    Pantau status pengajuan bantuan sosial Anda di sini.<br>
                    <span style="font-size:.75rem; opacity:.5;">
                        {{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM Y') }}
                    </span>
                </p>
            </div>
            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('user.pengajuan.index') }}" class="btn btn-warning fw-bold px-4 py-2.5 rounded-pill shadow-sm" style="background-color: #F9C74F; border-color: #F9C74F; color: #1E3A5F; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 0.5rem; transition: transform 0.2s, box-shadow 0.2s;">
                    <i class="bi bi-plus-circle-fill"></i> Ajukan Bantuan Baru
                </a>
                <img src="{{ asset('images/logo-pemkot.png') }}"
                     alt="Logo"
                     style="width:64px; height:64px; object-fit:contain; opacity:.25; filter:brightness(10);"
                     class="d-none d-md-block">
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mt-1">
    {{-- Kolom Kiri: Tata Cara Pengajuan --}}
    <div class="col-lg-7">
        <div class="page-card h-100">
            <div class="card-head">
                <h6 class="card-head-title">
                    <i class="bi bi-info-circle-fill text-primary"></i>Tata Cara Pengajuan Bantuan Sosial
                </h6>
            </div>
            <div class="card-body-inner">
                <div class="step-item">
                    <div class="step-number">1</div>
                    <div class="step-content">
                        <div class="step-title">Pilih Jenis Bantuan Sosial</div>
                        <div class="step-desc">Masuk ke menu <strong>"Ajukan Bantuan"</strong> di sidebar kiri atau klik tombol <strong>"Ajukan Bantuan Baru"</strong> di welcome banner atas untuk melihat bantuan sosial yang sedang dibuka.</div>
                    </div>
                </div>

                <div class="step-item">
                    <div class="step-number">2</div>
                    <div class="step-content">
                        <div class="step-title">Lengkapi Formulir Pendaftaran</div>
                        <div class="step-desc">Klik tombol daftar pada bantuan sosial yang Anda inginkan, kemudian lengkapi formulir informasi profil, alamat, dan data kriteria sosial ekonomi secara jujur dan akurat.</div>
                    </div>
                </div>

                <div class="step-item">
                    <div class="step-number">3</div>
                    <div class="step-content">
                        <div class="step-title">Unggah Berkas Persyaratan</div>
                        <div class="step-desc">Unggah salinan dokumen pendukung wajib (KTP, Kartu Keluarga, SKTM, dan Foto Rumah). Pastikan dokumen terlihat jelas dan terbaca dengan baik.</div>
                    </div>
                </div>

                <div class="step-item">
                    <div class="step-number">4</div>
                    <div class="step-content">
                        <div class="step-title">Seleksi Kriteria & Verifikasi</div>
                        <div class="step-desc">Petugas kelurahan akan memverifikasi berkas Anda. Sistem pendukung keputusan (SPK) menggunakan metode AHP & MOORA secara otomatis mengkalkulasi tingkat kelayakan Anda.</div>
                    </div>
                </div>

                <div class="step-item">
                    <div class="step-number">5</div>
                    <div class="step-content">
                        <div class="step-title">Persetujuan Kelayakan oleh Lurah</div>
                        <div class="step-desc">Rekomendasi hasil seleksi kelayakan sistem diajukan kepada Lurah Kelurahan Harjamukti untuk persetujuan atau penolakan final secara resmi.</div>
                    </div>
                </div>

                <div class="step-item">
                    <div class="step-number">6</div>
                    <div class="step-content">
                        <div class="step-title">Realisasi & Evaluasi Dampak</div>
                        <div class="step-desc">Jika disetujui, Anda dapat melihat jadwal penyaluran di halaman <strong>"Status Bantuan"</strong>. Setelah menerima bantuan, Anda wajib mengunggah foto bukti penggunaan bantuan.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Kolom Kanan: Berkas Persyaratan --}}
    <div class="col-lg-5">
        <div class="page-card h-100">
            <div class="card-head">
                <h6 class="card-head-title">
                    <i class="bi bi-file-earmark-zip-fill text-warning"></i>Berkas Persyaratan yang Perlu Disiapkan
                </h6>
            </div>
            <div class="card-body-inner">
                <p class="text-muted small mb-3">Harap siapkan dokumen di bawah ini dalam format gambar (JPG, JPEG, PNG) sebelum Anda memulai pengisian form pendaftaran bantuan:</p>
                
                <div class="row g-3">
                    <div class="col-sm-6">
                        <div class="doc-card">
                            <div class="doc-icon"><i class="bi bi-card-image"></i></div>
                            <div class="doc-title">Kartu Tanda Penduduk</div>
                            <div class="doc-desc">Foto KTP asli penerima bantuan yang jelas untuk verifikasi NIK dan identitas diri.</div>
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="doc-card">
                            <div class="doc-icon"><i class="bi bi-people-fill"></i></div>
                            <div class="doc-title">Kartu Keluarga</div>
                            <div class="doc-desc">Foto KK asli yang sah untuk verifikasi hubungan anggota keluarga.</div>
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="doc-card">
                            <div class="doc-icon"><i class="bi bi-file-earmark-medical-fill"></i></div>
                            <div class="doc-title">SKTM Resmi</div>
                            <div class="doc-desc">Surat Keterangan Tidak Mampu dari kelurahan/instansi resmi setempat yang masih berlaku.</div>
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="doc-card">
                            <div class="doc-icon"><i class="bi bi-house-door-fill"></i></div>
                            <div class="doc-title">Foto Kondisi Rumah</div>
                            <div class="doc-desc">Foto bagian depan rumah tempat tinggal penerima sebagai bukti fisik kelayakan.</div>
                        </div>
                    </div>
                </div>

                <div class="alert alert-info mt-4 mb-0 small">
                    <i class="bi bi-info-circle-fill me-1"></i> <strong>Catatan Penting:</strong> Pastikan ukuran berkas masing-masing dokumen tidak melebihi <strong>2 megabytes (2MB)</strong>.
                </div>
            </div>
        </div>
    </div>
</div>

@endsection