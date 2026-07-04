@extends('user.layouts.app')

@section('title', 'Pengajuan Berhasil')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden text-center">
                <div class="bg-success py-5 text-white position-relative">
                    <div class="d-inline-flex align-items-center justify-content-center bg-white rounded-circle shadow-sm text-success" style="width: 80px; height: 80px;">
                        <i class="bi bi-patch-check-fill display-4 lh-1"></i>
                    </div>
                    <h4 class="fw-bold mt-3 mb-1">Pengajuan Berhasil!</h4>
                    <p class="text-white text-opacity-75 small mb-0">Permohonan bantuan sosial Anda telah diterima oleh sistem.</p>
                </div>

                <div class="card-body p-4 p-md-5">
                    <p class="text-muted small mb-4">Berikut adalah rincian pengajuan bantuan sosial Anda:</p>

                    <div class="card bg-light border-0 rounded-3 mb-4 text-start">
                        <div class="card-body p-4">
                            <div class="row g-3">
                                <div class="col-12 border-bottom border-light pb-2">
                                    <span class="text-muted small d-block">Jenis Bantuan</span>
                                    <span class="fw-bold text-dark fs-5">{{ $pengajuan->bantuanSosial->nama_bantuan ?? '-' }}</span>
                                </div>
                                <div class="col-6">
                                    <span class="text-muted small d-block">Tanggal Pengajuan</span>
                                    <span class="fw-bold text-dark">{{ $pengajuan->created_at->format('d F Y') }}</span>
                                </div>
                                <div class="col-6">
                                    <span class="text-muted small d-block">Status Pengajuan</span>
                                    <span class="badge bg-warning text-dark border-0 rounded-pill px-3 py-2">
                                        <i class="bi bi-clock-history me-1"></i> {{ $pengajuan->status }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info border-0 rounded-3 text-start small mb-4" role="alert">
                        <i class="bi bi-info-circle-fill me-2 fs-6"></i>
                        Tim verifikator kami akan melakukan peninjauan terhadap kelayakan berkas Anda. Silakan cek halaman Pengajuan secara berkala untuk memantau status terbaru.
                    </div>

                    <div class="d-grid gap-2">
                        <a href="{{ route('user.pengajuan.index') }}" class="btn btn-primary rounded-pill py-2 shadow-sm fw-semibold">
                            <i class="bi bi-arrow-left-circle me-1"></i> Kembali ke Pengajuan
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
