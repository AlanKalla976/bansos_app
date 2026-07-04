@extends('user.layouts.app')

@section('title', 'Detail Pengajuan Bantuan')

@section('content')
<div class="container-fluid py-2">
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('user.pengajuan.index') }}" class="btn btn-outline-secondary rounded-circle p-2 lh-1 me-3" style="width: 40px; height: 40px;">
            <i class="bi bi-arrow-left fs-5"></i>
        </a>
        <div>
            <h4 class="page-title mb-1">Detail Pengajuan Bantuan</h4>
            <p class="text-muted small mb-0">Informasi lengkap permohonan bantuan sosial Anda beserta berkas dokumen.</p>
        </div>
    </div>

    {{-- ✅ Banner status dibaca langsung dari $pengajuan->status (kolom di tabel pengajuans) --}}
    @if($pengajuan->status === 'Diterima')
        <div class="alert alert-success border-0 rounded-4 shadow-sm p-4 mb-4">
            <div class="d-flex gap-3 align-items-start">
                <div class="bg-success bg-opacity-10 text-success rounded-circle p-2">
                    <i class="bi bi-patch-check-fill fs-4 lh-1"></i>
                </div>
                <div>
                    <h5 class="fw-bold mb-1 text-success">Pengajuan Diterima</h5>
                    <p class="mb-0 text-muted small">Selamat! Pengajuan bantuan Anda telah <strong>diterima</strong>. Petugas akan menghubungi Anda lebih lanjut.</p>
                </div>
            </div>
        </div>

    @elseif($pengajuan->status === 'Diverifikasi')
        <div class="alert alert-info border-0 rounded-4 shadow-sm p-4 mb-4">
            <div class="d-flex gap-3 align-items-start">
                <div class="bg-info bg-opacity-10 text-info rounded-circle p-2">
                    <i class="bi bi-search fs-4 lh-1"></i>
                </div>
                <div>
                    <h5 class="fw-bold mb-1 text-info">Sedang Diverifikasi</h5>
                    <p class="mb-0 text-muted small">Pengajuan Anda sedang dalam proses verifikasi oleh petugas. Mohon tunggu hasilnya.</p>
                </div>
            </div>
        </div>

    @elseif($pengajuan->status === 'Ditolak')
        <div class="alert alert-danger border-0 rounded-4 shadow-sm p-4 mb-4">
            <div class="d-flex gap-3 align-items-start">
                <div class="bg-danger bg-opacity-10 text-danger rounded-circle p-2">
                    <i class="bi bi-exclamation-triangle-fill fs-4 lh-1"></i>
                </div>
                <div>
                    <h5 class="fw-bold mb-1 text-danger">Pengajuan Ditolak</h5>
                    <p class="mb-0 text-muted small">Mohon maaf, pengajuan Anda ditolak.</p>
                    @if($pengajuan->alasan_penolakan)
                        <div class="mt-2 p-3 bg-white bg-opacity-50 border border-danger-subtle rounded-3 text-dark small">
                            {{ $pengajuan->alasan_penolakan }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

    @else
        {{-- Menunggu --}}
        <div class="alert alert-warning border-0 rounded-4 shadow-sm p-4 mb-4">
            <div class="d-flex gap-3 align-items-start">
                <div class="bg-warning bg-opacity-10 text-warning rounded-circle p-2">
                    <i class="bi bi-hourglass-split fs-4 lh-1"></i>
                </div>
                <div>
                    <h5 class="fw-bold mb-1 text-warning">Menunggu Verifikasi</h5>
                    <p class="mb-0 text-muted small">Pengajuan Anda telah diterima dan sedang menunggu verifikasi dari petugas.</p>
                </div>
            </div>
        </div>
    @endif

    <div class="row g-4">
        {{-- Data Rincian --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold text-dark mb-0">
                        <i class="bi bi-person-badge text-primary me-2"></i> Data Identitas Pemohon
                    </h5>
                    @php
                        $statusBadgeClass = [
                            'Menunggu'     => 'bg-warning-subtle text-warning border-warning-subtle',
                            'Diverifikasi' => 'bg-info-subtle text-info border-info-subtle',
                            'Ditolak'      => 'bg-danger-subtle text-danger border-danger-subtle',
                            'Diterima'     => 'bg-success-subtle text-success border-success-subtle',
                        ][$pengajuan->status] ?? 'bg-secondary-subtle text-secondary';

                        $statusIcon = [
                            'Menunggu'     => 'bi-hourglass-split',
                            'Diverifikasi' => 'bi-search',
                            'Ditolak'      => 'bi-x-circle-fill',
                            'Diterima'     => 'bi-check-circle-fill',
                        ][$pengajuan->status] ?? 'bi-circle';
                    @endphp
                    <span class="badge border px-3 py-2 rounded-pill {{ $statusBadgeClass }}">
                        <i class="bi {{ $statusIcon }} me-1"></i> {{ $pengajuan->status }}
                    </span>
                </div>
                <div class="card-body p-4">
                    <div class="table-responsive">
                        <table class="table table-borderless align-middle mb-0">
                            <tbody>
                                <tr class="border-bottom border-light">
                                    <th class="text-muted fw-normal py-3" style="width: 30%;">Program Bantuan</th>
                                    <td class="fw-bold text-dark py-3">{{ $pengajuan->bantuanSosial->nama_bantuan ?? '-' }}</td>
                                </tr>
                                <tr class="border-bottom border-light">
                                    <th class="text-muted fw-normal py-3">Nama Lengkap</th>
                                    <td class="fw-semibold text-dark py-3">{{ $pengajuan->nama }}</td>
                                </tr>
                                <tr class="border-bottom border-light">
                                    <th class="text-muted fw-normal py-3">NIK</th>
                                    <td class="fw-semibold text-dark py-3">{{ $pengajuan->nik }}</td>
                                </tr>
                                <tr class="border-bottom border-light">
                                    <th class="text-muted fw-normal py-3">No. Telepon</th>
                                    <td class="fw-semibold text-dark py-3">{{ $pengajuan->no_telepon }}</td>
                                </tr>
                                <tr class="border-bottom border-light">
                                    <th class="text-muted fw-normal py-3">Jenis Kelamin</th>
                                    <td class="fw-semibold text-dark py-3">{{ $pengajuan->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                                </tr>
                                <tr class="border-bottom border-light">
                                    <th class="text-muted fw-normal py-3">Tanggal Lahir</th>
                                    <td class="fw-semibold text-dark py-3">{{ $pengajuan->tanggal_lahir?->format('d F Y') }}</td>
                                </tr>
                                <tr class="border-bottom border-light">
                                    <th class="text-muted fw-normal py-3">Pendidikan</th>
                                    <td class="fw-semibold text-dark py-3">{{ $pengajuan->pendidikan }}</td>
                                </tr>
                                <tr>
                                    <th class="text-muted fw-normal py-3">Alamat Domisili</th>
                                    <td class="text-dark py-3">{{ $pengajuan->alamat }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Card Ekonomi --}}
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
                    <h5 class="fw-bold text-dark mb-0">
                        <i class="bi bi-cash-stack text-primary me-2"></i> Rincian Kondisi Ekonomi
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="table-responsive">
                        <table class="table table-borderless align-middle mb-0">
                            <tbody>
                                <tr class="border-bottom border-light">
                                    <th class="text-muted fw-normal py-3" style="width: 30%;">Penghasilan / Bulan</th>
                                    <td class="fw-bold text-dark py-3">
                                        {{-- ✅ Format Rp 1.500.000 tanpa desimal --}}
                                        @if($pengajuan->penghasilan !== null)
                                            Rp {{ number_format((int) $pengajuan->penghasilan, 0, ',', '.') }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr class="border-bottom border-light">
                                    <th class="text-muted fw-normal py-3">Jumlah Tanggungan</th>
                                    <td class="fw-semibold text-dark py-3">{{ $pengajuan->jumlah_tanggungan ?? '-' }} orang</td>
                                </tr>
                                <tr class="border-bottom border-light">
                                    <th class="text-muted fw-normal py-3">Pekerjaan</th>
                                    <td class="fw-semibold text-dark py-3">{{ $pengajuan->pekerjaan ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th class="text-muted fw-normal py-3">Kepemilikan Rumah</th>
                                    <td class="fw-semibold text-dark py-3">{{ $pengajuan->kepemilikan_rumah ?? '-' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Dokumen Berkas --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
                    <h5 class="fw-bold text-dark mb-0">
                        <i class="bi bi-folder-symlink text-primary me-2"></i> Dokumen Pendukung
                    </h5>
                </div>
                <div class="card-body p-4">
                    @foreach([
                        'foto_ktp'   => 'Foto KTP / Identitas',
                        'foto_kk'    => 'Foto Kartu Keluarga',
                        'foto_sktm'  => 'Foto SKTM',
                        'foto_rumah' => 'Foto Kondisi Rumah'
                    ] as $field => $label)
                        <div class="mb-4">
                            <span class="text-muted small d-block mb-2">{{ $label }}</span>
                            @if($pengajuan->$field)
                                <div class="position-relative rounded-3 overflow-hidden border border-light shadow-sm doc-thumb">
                                    <img src="{{ asset('storage/' . $pengajuan->$field) }}"
                                         alt="{{ $label }}"
                                         class="w-100 object-fit-cover"
                                         style="height: 150px;">
                                    <div class="doc-overlay position-absolute top-0 start-0 w-100 h-100 bg-dark bg-opacity-40 d-flex align-items-center justify-content-center">
                                        <a href="{{ asset('storage/' . $pengajuan->$field) }}" target="_blank"
                                           class="btn btn-light btn-sm rounded-pill px-3 fw-semibold">
                                            <i class="bi bi-fullscreen me-1"></i> Buka Gambar
                                        </a>
                                    </div>
                                </div>
                            @else
                                <div class="bg-light text-center py-4 rounded-3 text-muted border border-dashed">
                                    <i class="bi bi-image fs-3 mb-1 d-block"></i>
                                    Tidak ada file
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .doc-thumb .doc-overlay {
        opacity: 0;
        transition: opacity 0.2s ease-in-out;
    }
    .doc-thumb:hover .doc-overlay {
        opacity: 1;
    }
    .border-dashed {
        border-style: dashed !important;
        border-color: #cbd5e1 !important;
    }
</style>
@endsection