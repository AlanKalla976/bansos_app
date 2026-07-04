@extends('user.layouts.app')

@section('title', 'Pengajuan Bantuan')

@section('content')
<div class="container-fluid py-2">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="page-title mb-1"><i class="bi bi-file-earmark-text-fill me-2 text-primary"></i>Pengajuan Bantuan</h4>
            <p class="text-muted small mb-0">Daftar bantuan sosial yang tersedia. Anda dapat mengajukan permohonan bantuan secara online.</p>
        </div>
    </div>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
        @forelse($bantuans as $bantuan)
            @php
                // ✅ Status dibaca langsung dari tabel pengajuans lewat $pengajuanMap
                $submission = $pengajuanMap->get($bantuan->id);
                $hasApplied = $submission !== null;
                $status     = $hasApplied ? $submission->status : null;

                $stripColor = [
                    'Menunggu'     => '#ffc107',
                    'Diverifikasi' => '#0dcaf0',
                    'Ditolak'      => '#dc3545',
                    'Diterima'     => '#198754',
                ][$status] ?? '#e2e8f0';

                $statusBadgeClass = [
                    'Menunggu'     => 'bg-warning-subtle text-warning border-warning-subtle',
                    'Diverifikasi' => 'bg-info-subtle text-info border-info-subtle',
                    'Ditolak'      => 'bg-danger-subtle text-danger border-danger-subtle',
                    'Diterima'     => 'bg-success-subtle text-success border-success-subtle',
                ][$status] ?? 'bg-secondary-subtle text-secondary border-secondary-subtle';

                $statusIcon = [
                    'Menunggu'     => 'bi-hourglass-split',
                    'Diverifikasi' => 'bi-search',
                    'Ditolak'      => 'bi-x-circle-fill',
                    'Diterima'     => 'bi-check-circle-fill',
                ][$status] ?? 'bi-circle';
            @endphp

            <div class="col">
                <div class="card h-100 border-0 shadow-sm rounded-4 position-relative overflow-hidden hover-translate-y">

                    {{-- Strip warna atas card sesuai status pengajuan --}}
                    <div style="height: 4px; background: {{ $stripColor }};"></div>

                    <div class="card-body p-4 d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <span class="badge rounded-pill bg-light text-primary px-3 py-2 border border-primary-subtle">
                                <i class="bi bi-gift-fill me-1"></i> Bantuan Sosial
                            </span>

                            {{-- Badge status dibaca dari $submission->status (tabel pengajuans) --}}
                            @if($hasApplied)
                                <span class="badge border px-3 py-2 rounded-pill {{ $statusBadgeClass }}">
                                    <i class="bi {{ $statusIcon }} me-1"></i> {{ $status }}
                                </span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-3 py-2 rounded-pill">
                                    <i class="bi bi-circle me-1"></i> Belum Mengajukan
                                </span>
                            @endif
                        </div>

                        <h5 class="fw-bold text-dark mb-2 text-truncate-2" title="{{ $bantuan->nama_bantuan }}">
                            {{ $bantuan->nama_bantuan }}
                        </h5>

                        <p class="text-muted small mb-3 flex-grow-1 text-truncate-3">
                            {{ $bantuan->deskripsi ?? 'Tidak ada deskripsi untuk program bantuan sosial ini.' }}
                        </p>

                        {{-- Info banner kecil sesuai status --}}
                        @if($hasApplied)
                            <div class="mb-3">
                                @if($status === 'Diterima')
                                    <div class="alert alert-success rounded-3 py-2 px-3 mb-0 small d-flex align-items-center gap-2">
                                        <i class="bi bi-patch-check-fill fs-5"></i>
                                        <span>Selamat! Pengajuan Anda <strong>diterima</strong>.</span>
                                    </div>
                                @elseif($status === 'Diverifikasi')
                                    <div class="alert alert-info rounded-3 py-2 px-3 mb-0 small d-flex align-items-center gap-2">
                                        <i class="bi bi-search fs-5"></i>
                                        <span>Pengajuan sedang <strong>diverifikasi</strong> petugas.</span>
                                    </div>
                                @elseif($status === 'Ditolak')
                                    <div class="alert alert-danger rounded-3 py-2 px-3 mb-0 small d-flex align-items-center gap-2">
                                        <i class="bi bi-x-octagon-fill fs-5"></i>
                                        <span>Pengajuan <strong>ditolak</strong>. Lihat detail untuk alasan.</span>
                                    </div>
                                @elseif($status === 'Menunggu')
                                    <div class="alert alert-warning rounded-3 py-2 px-3 mb-0 small d-flex align-items-center gap-2">
                                        <i class="bi bi-hourglass-split fs-5"></i>
                                        <span>Menunggu verifikasi dari petugas.</span>
                                    </div>
                                @endif
                            </div>
                        @endif

                        <div class="mt-auto pt-3 border-top border-light d-flex justify-content-between align-items-center">
                            @if($hasApplied)
                                <span class="text-muted small">
                                    <i class="bi bi-calendar3 me-1"></i>
                                    {{ $submission->created_at->format('d M Y') }}
                                </span>
                                <a href="{{ route('user.pengajuan.show', $submission->id) }}"
                                   class="btn btn-outline-primary rounded-pill px-4 btn-sm shadow-sm">
                                    <i class="bi bi-eye-fill me-1"></i> Lihat Detail
                                </a>
                            @else
                                <span class="text-muted small">
                                    <i class="bi bi-people me-1"></i> Kuota Terbatas
                                </span>
                                <a href="{{ route('user.pengajuan.create', $bantuan->id) }}"
                                   class="btn btn-primary rounded-pill px-4 btn-sm shadow-sm">
                                    <i class="bi bi-send-plus-fill me-1"></i> Ajukan
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4 p-5 text-center bg-white">
                    <div class="my-3">
                        <i class="bi bi-folder-x text-muted display-1"></i>
                    </div>
                    <h5 class="fw-bold text-dark mt-3">Belum Ada Program Bantuan</h5>
                    <p class="text-muted small">Saat ini belum ada program bantuan sosial yang aktif atau dapat diajukan.</p>
                </div>
            </div>
        @endforelse
    </div>
</div>

<style>
    .hover-translate-y {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .hover-translate-y:hover {
        transform: translateY(-5px);
        box-shadow: 0 .5rem 1.5rem rgba(0,0,0,.08) !important;
    }
    .text-truncate-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        height: 3rem;
    }
    .text-truncate-3 {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
        height: 4.5rem;
    }
</style>
@endsection