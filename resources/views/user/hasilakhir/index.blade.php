@extends('user.layouts.app')

@section('title', 'Hasil Penilaian')
@section('page-title', 'Hasil Kelayakan')
@section('breadcrumb', 'Hasil Kelayakan')

@push('styles')
<style>
.tbl-green thead tr,
.tbl-green thead tr th {
    background: #2d6a4f !important;
    color: #fff !important;
    font-size: .75rem !important;
    font-weight: 600 !important;
    letter-spacing: .4px;
    padding: .85rem 1rem !important;
    border: none !important;
    border-bottom: none !important;
}
.tbl-green tbody tr { border-bottom: 1px solid #F1F5F9 !important; transition: background .15s; }
.tbl-green tbody tr:last-child { border-bottom: none !important; }
.tbl-green tbody tr:hover td { background: #F8FAFC !important; }
.tbl-green tbody td {
    padding: .85rem 1rem !important;
    border: none !important;
    border-top: none !important;
    vertical-align: middle !important;
}
</style>
@endpush

@section('content')
<div class="container py-4">

    <div class="mb-4">
        <h4 class="fw-bold text-dark">
            <i class="bi bi-trophy me-2 text-primary"></i>Hasil Penilaian Kelayakan
        </h4>
        <p class="text-muted mb-0">
            Hasil penilaian kelayakan penerima bantuan sosial menggunakan metode MOORA.
        </p>
    </div>

    {{-- ══════════════════════════════════════════
         STATUS 1: BELUM MENGAJUKAN
    ══════════════════════════════════════════ --}}
    @if($status === 'belum_mengajukan')
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body py-5 text-center">
            <div class="mb-4">
                <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex
                            align-items-center justify-content-center mb-3"
                     style="width:80px;height:80px;">
                    <i class="bi bi-file-earmark-x fs-2 text-warning"></i>
                </div>
                <h5 class="fw-bold text-dark">Anda Belum Mengajukan Bantuan</h5>
                <p class="text-muted mb-4">
                    Untuk melihat hasil penilaian, Anda harus mengajukan bantuan sosial
                    terlebih dahulu dan menunggu proses verifikasi serta penilaian dari admin.
                </p>
                <a href="{{ route('user.pengajuan.index') }}"
                   class="btn btn-primary btn-lg rounded-3 px-5">
                    <i class="bi bi-send me-2"></i>Ajukan Bantuan Sekarang
                </a>
            </div>

            <hr class="my-4">

            <div class="row g-3 justify-content-center">
                <div class="col-md-3">
                    <div class="bg-light rounded-3 p-3">
                        <i class="bi bi-1-circle-fill text-primary fs-4 d-block mb-2"></i>
                        <p class="small fw-semibold mb-0">Ajukan Bantuan</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="bg-light rounded-3 p-3">
                        <i class="bi bi-2-circle-fill text-primary fs-4 d-block mb-2"></i>
                        <p class="small fw-semibold mb-0">Tunggu Verifikasi</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="bg-light rounded-3 p-3">
                        <i class="bi bi-3-circle-fill text-primary fs-4 d-block mb-2"></i>
                        <p class="small fw-semibold mb-0">Admin Lakukan Penilaian</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="bg-light rounded-3 p-3">
                        <i class="bi bi-4-circle-fill text-primary fs-4 d-block mb-2"></i>
                        <p class="small fw-semibold mb-0">Lihat Hasil di Sini</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════
         STATUS 2: SUDAH MENGAJUKAN TAPI BELUM DINILAI
    ══════════════════════════════════════════ --}}
    @elseif($status === 'belum_dinilai')
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body py-5 text-center">
            <div class="bg-info bg-opacity-10 rounded-circle d-inline-flex
                        align-items-center justify-content-center mb-3"
                 style="width:80px;height:80px;">
                <i class="bi bi-hourglass-split fs-2 text-info"></i>
            </div>
            <h5 class="fw-bold text-dark">Pengajuan Sedang Diproses</h5>
            <p class="text-muted mb-4">
                Pengajuan Anda sudah diterima. Hasil penilaian akan muncul di sini
                setelah admin menyelesaikan proses verifikasi dan perhitungan MOORA.
            </p>

            {{-- Info pengajuan --}}
            <div class="card border rounded-3 mx-auto mb-4" style="max-width:400px;">
                <div class="card-body text-start p-3">
                    <p class="small text-muted mb-1">Status Pengajuan</p>
                    @php
                        $badgeStatus = match($pengajuanUser->status) {
                            'Menunggu'     => ['class' => 'bg-warning text-dark', 'icon' => 'bi-hourglass-split'],
                            'Diverifikasi' => ['class' => 'bg-success',           'icon' => 'bi-check-circle'],
                            'Ditolak'      => ['class' => 'bg-danger',            'icon' => 'bi-x-circle'],
                            'Diterima'     => ['class' => 'bg-primary',           'icon' => 'bi-award'],
                            default        => ['class' => 'bg-secondary',         'icon' => 'bi-dash'],
                        };
                    @endphp
                    <span class="badge {{ $badgeStatus['class'] }} rounded-pill px-3 py-2 mb-3">
                        <i class="bi {{ $badgeStatus['icon'] }} me-1"></i>
                        {{ $pengajuanUser->status }}
                    </span>
                    <table class="table table-borderless small mb-0">
                        <tr>
                            <th class="text-muted fw-normal ps-0" width="120">Nama</th>
                            <td class="fw-semibold">{{ $pengajuanUser->nama }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted fw-normal ps-0">NIK</th>
                            <td>{{ $pengajuanUser->nik }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted fw-normal ps-0">Tgl. Pengajuan</th>
                            <td>{{ $pengajuanUser->created_at->format('d/m/Y') }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <a href="{{ route('user.pengajuan.index') }}"
               class="btn btn-outline-primary rounded-3">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Pengajuan
            </a>
        </div>
    </div>

    {{-- ══════════════════════════════════════════
         STATUS 3: SUDAH DINILAI — TAMPILKAN HASIL
    ══════════════════════════════════════════ --}}
    @elseif($status === 'sudah_dinilai')

    {{-- Card hasil milik user sendiri --}}
    <div class="card border-0 rounded-4 mb-4 text-white"
         style="background: linear-gradient(135deg, #1e3a5f, #2d6a4f);">
        <div class="card-body p-4">
            <p class="small opacity-75 mb-2 fw-semibold">
                <i class="bi bi-person-check me-1"></i> HASIL PENILAIAN ANDA
            </p>
            <div class="row align-items-center g-3">
                <div class="col">
                    <h5 class="fw-bold mb-1">{{ $hasilSendiri->pengajuan->nama ?? '-' }}</h5>
                    <p class="mb-0 opacity-75 small">
                        <i class="bi bi-gift me-1"></i>
                        {{ $hasilSendiri->pengajuan->bantuanSosial->nama_bantuan ?? '-' }}
                        &nbsp;|&nbsp;
                        <i class="bi bi-card-text me-1"></i>
                        NIK: {{ $hasilSendiri->pengajuan->nik ?? '-' }}
                    </p>
                </div>
                <div class="col-auto text-center">
                    <p class="small opacity-75 mb-0">Ranking</p>
                    <h2 class="fw-bold mb-0">#{{ $hasilSendiri->ranking }}</h2>
                </div>
                <div class="col-auto text-center">
                    <p class="small opacity-75 mb-0">Nilai Yi</p>
                    <p class="fw-bold mb-0 font-monospace">
                        {{ number_format($hasilSendiri->nilai_yi, 3) }}
                    </p>
                </div>
                <div class="col-auto">
                    {{-- ✅ Threshold kelayakan: Nilai Yi > 0,35 --}}
                    @if($hasilSendiri->nilai_yi > 0.35)
                        <span class="badge bg-success bg-opacity-75 rounded-pill px-3 py-2 fs-6">
                            <i class="bi bi-check-circle me-1"></i>Layak
                        </span>
                    @else
                        <span class="badge bg-danger bg-opacity-75 rounded-pill px-3 py-2 fs-6">
                            <i class="bi bi-x-circle me-1"></i>Tidak Layak
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Search --}}
    <div class="filter-bar mb-3">
        <form method="GET" action="{{ route('user.hasilakhir.index') }}" class="d-flex gap-2">
            <input type="text" name="search"
                   class="form-control form-control-sm"
                   placeholder="Cari nama atau NIK..."
                   value="{{ request('search') }}"
                   style="max-width: 320px;">
            <button type="submit" class="btn btn-primary btn-sm">
                <i class="bi bi-search"></i> Cari
            </button>
            @if(request('search'))
                <a href="{{ route('user.hasilakhir.index') }}" class="btn btn-sm" style="background:#F1F5F9; color:var(--text-muted); border:1px solid var(--border);">
                    Reset
                </a>
            @endif
        </form>
    </div>

    {{-- Tabel semua hasil --}}
    <div class="page-card mb-4">
        <div class="card-head">
            <h6 class="card-head-title">
                <div class="card-icon" style="background:#DBEAFE; color:#1E3A5F;">
                    <i class="bi bi-table"></i>
                </div>
                Tabel Ranking Semua Peserta
            </h6>
            @if(request('search'))
                <small class="text-muted">
                    Pencarian: <strong>"{{ request('search') }}"</strong>
                    — {{ $hasilAkhirs->count() }} data
                </small>
            @else
                <small class="text-muted">Total {{ $hasilAkhirs->count() }} peserta</small>
            @endif
        </div>
        <div class="table-responsive">
            <table class="table align-middle mb-0 tbl-green">
                <thead>
                    <tr>
                        <th style="width: 100px;">Ranking</th>
                        <th>Nama</th>
                        <th>NIK</th>
                        <th>Jenis Bantuan</th>
                        <th style="width: 140px;">Nilai Yi</th>
                        <th style="width: 150px; text-align: center;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($hasilAkhirs as $h)
                    @php
                        $isSendiri = $hasilSendiri && $h->hasil_id === $hasilSendiri->hasil_id;
                        // ✅ Threshold kelayakan: Nilai Yi > 0,35
                        $isLayak = $h->nilai_yi > 0.35;
                    @endphp
                    <tr style="{{ $isSendiri ? 'background: rgba(82, 183, 136, 0.08); font-weight: 600;' : '' }}">
                        <td class="ps-4">
                            <span class="rank-badge {{ $h->ranking <= 3 ? 'rank-'.$h->ranking : 'rank-n' }}">{{ $h->ranking }}</span>
                        </td>
                        <td style="font-weight:600; color:#1E293B; font-size:.83rem;">
                            {{ $h->pengajuan->nama ?? '-' }}
                            @if($isSendiri)
                                <span class="badge bg-primary rounded-pill ms-1" style="font-size:0.65rem;">Anda</span>
                            @endif
                        </td>
                        <td style="color:#64748B; font-size:.8rem; font-family:monospace;">
                            {{ $h->pengajuan->nik ?? '-' }}
                        </td>
                        <td>
                            <span style="background:#EFF6FF; color:#1E3A5F; font-size:.7rem; font-weight:700; padding:.25rem .75rem; border-radius:20px; display:inline-block;">
                                {{ $h->pengajuan->bantuanSosial->nama_bantuan ?? '-' }}
                            </span>
                        </td>
                        <td style="font-family:monospace; font-weight:700; color:#1E293B; font-size:.78rem;">
                            {{ number_format($h->nilai_yi, 3) }}
                        </td>
                        <td style="text-align:center;">
                            @if($isLayak)
                                <span style="background:#D1FAE5; color:#065F46; font-size:.7rem; font-weight:700; padding:.28rem .75rem; border-radius:20px; display:inline-block;">Layak</span>
                            @else
                                <span style="background:#FEE2E2; color:#991B1B; font-size:.7rem; font-weight:700; padding:.28rem .75rem; border-radius:20px; display:inline-block;">Tidak Layak</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6">
                            <div class="empty-state">
                                <i class="bi bi-inbox"></i>
                                <p>
                                    @if(request('search'))
                                        Tidak ditemukan hasil untuk <strong>"{{ request('search') }}"</strong>.
                                    @else
                                        Belum ada hasil penilaian.
                                    @endif
                                </p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($hasilAkhirs->isNotEmpty())
        <div class="card-footer bg-white border-top py-3 px-4">
            <small class="text-muted">
                <i class="bi bi-info-circle me-1"></i>
                <span class="text-success fw-semibold">
                    {{ $hasilAkhirs->where('nilai_yi', '>', 0.35)->count() }} Layak
                </span>
                &nbsp;|&nbsp;
                <span class="text-danger fw-semibold">
                    {{ $hasilAkhirs->where('nilai_yi', '<=', 0.35)->count() }} Tidak Layak
                </span>
            </small>
        </div>
        @endif
    </div>

    @endif

</div>
@endsection