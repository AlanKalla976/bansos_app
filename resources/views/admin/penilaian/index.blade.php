@extends('admin.layouts.app')

@section('title', 'Penilaian MOORA')
@section('page-title', 'Penilaian')
@section('breadcrumb', 'Penilaian')

@push('styles')
<style>
.tbl-navy thead tr,
.tbl-navy thead tr th {
    background: #1E3A5F !important;
    color: #fff !important;
    font-size: .75rem !important;
    font-weight: 600 !important;
    letter-spacing: .4px;
    padding: .85rem 1rem !important;
    border: none !important;
    border-bottom: none !important;
}
.tbl-navy tbody tr { border-bottom: 1px solid #F1F5F9 !important; transition: background .15s; }
.tbl-navy tbody tr:last-child { border-bottom: none !important; }
.tbl-navy tbody tr:hover td { background: #F8FAFC !important; }
.tbl-navy tbody td {
    padding: .85rem 1rem !important;
    border: none !important;
    border-top: none !important;
    vertical-align: middle !important;
}
.tbl-navy-center thead tr th { text-align: center !important; }
.tbl-navy-center thead tr th:first-child { text-align: left !important; }
.tbl-navy-center tbody td { text-align: center !important; }
.tbl-navy-center tbody td:first-child { text-align: left !important; }
</style>
@endpush

@section('content')
<div class="container-fluid">

    <div class="page-header">
        <div>
            <h2 class="page-header-title">
                <i class="bi bi-clipboard-data me-2" style="color:var(--accent);"></i>Penilaian Kelayakan
            </h2>
            <p class="page-header-sub">Penilaian masyarakat menggunakan metode MOORA</p>
        </div>
        <form action="{{ route('admin.penilaian.hitung') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-success fw-semibold"
                    onclick="return confirm('Hitung MOORA sekarang? Hasil lama akan ditimpa.')">
                <i class="bi bi-calculator me-2"></i>Hitung MOORA
            </button>
        </form>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-3">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show rounded-3">
        <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- SECTION 1: DATA PENILAIAN --}}
    <div class="page-card mb-4">
        <div class="card-head">
            <h6 class="card-head-title">
                <i class="bi bi-people-fill"></i>1. Data Pengajuan Terverifikasi
            </h6>
        </div>
        <div class="table-responsive">
            <table class="table align-middle mb-0 tbl-navy">
                <thead>
                    <tr>
                        <th style="width:70px;">No</th>
                        <th>Nama</th>
                        <th>NIK</th>
                        <th>Jenis Bantuan</th>
                        <th style="width:180px;">Status Penilaian</th>
                        <th style="width:140px; text-align:center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pengajuans as $i => $p)
                    <tr>
                        <td style="color:#94A3B8; font-size:.78rem;">{{ $i + 1 }}</td>
                        <td style="font-weight:600; color:#1E293B; font-size:.83rem;">{{ $p->nama }}</td>
                        <td style="color:#64748B; font-family:monospace; font-size:.8rem;">{{ $p->nik }}</td>
                        <td>
                            <span style="background:#EFF6FF; color:#1E3A5F; font-size:.7rem; font-weight:700; padding:.25rem .75rem; border-radius:20px; display:inline-block;">
                                {{ $p->bantuanSosial->nama_bantuan ?? '-' }}
                            </span>
                        </td>
                        <td>
                            @if(in_array($p->id, $sudahDinilai))
                                <span style="background:#D1FAE5; color:#065F46; font-size:.7rem; font-weight:700; padding:.28rem .75rem; border-radius:20px; display:inline-block;">Sudah Dinilai</span>
                            @else
                                <span style="background:#FEF3C7; color:#92400E; font-size:.7rem; font-weight:700; padding:.28rem .75rem; border-radius:20px; display:inline-block;">Belum Dinilai</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex justify-content-center gap-1">
                                @if(in_array($p->id, $sudahDinilai))
                                    <a href="{{ route('admin.penilaian.edit', $p->id) }}"
                                       class="btn btn-sm btn-warning" style="border-radius:8px; font-size:.78rem;">
                                        <i class="bi bi-pencil me-1"></i>Edit
                                    </a>
                                @else
                                    <a href="{{ route('admin.penilaian.create', ['pengajuan_id' => $p->id]) }}"
                                       class="btn btn-sm btn-primary" style="border-radius:8px; font-size:.78rem;">
                                        <i class="bi bi-star me-1"></i>Nilai
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6">
                            <div class="empty-state" style="padding:3rem;">
                                <i class="bi bi-inbox" style="font-size:2rem;"></i>
                                <p>Belum ada pengajuan yang berstatus Diverifikasi.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- SECTION 2: HASIL MOORA --}}
    @if(session('hasil_moora'))
    @php $moora = session('hasil_moora'); @endphp

    {{-- Matriks Keputusan --}}
    <div class="page-card mb-4">
        <div class="card-head">
            <h6 class="card-head-title">
                <i class="bi bi-grid-3x3"></i>2. Matriks Keputusan
            </h6>
        </div>
        <div class="table-responsive">
            <table class="table align-middle mb-0 tbl-navy tbl-navy-center">
                <thead>
                    <tr>
                        <th>Alternatif</th>
                        @foreach($moora['kriterias'] as $k)
                            <th>
                                {{ $k['kode_kriteria'] }}<br>
                                <span style="font-size:.65rem; opacity:.85;">{{ $k['nama'] }}</span>
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($moora['pengajuans'] as $i => $peng)
                    <tr>
                        <td style="font-weight:600; color:#1E293B; font-size:.83rem;">{{ $peng['nama'] }}</td>
                        @foreach($moora['kriterias'] as $j => $k)
                            <td style="font-size:.82rem;">{{ number_format($moora['matrix'][$i][$j], 3) }}</td>
                        @endforeach
                    </tr>
                    @endforeach
                    <tr>
                        <td style="font-style:italic; color:#64748B; font-size:.8rem; background:#F8FAFC !important;">√Σ(Xij²)</td>
                        @foreach($moora['kriterias'] as $j => $k)
                            <td style="font-weight:700; color:#1E3A5F; font-size:.82rem; background:#F8FAFC !important;">{{ number_format($moora['akarKuadrat'][$j], 3) }}</td>
                        @endforeach
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Matriks Normalisasi --}}
    <div class="page-card mb-4">
        <div class="card-head">
            <h6 class="card-head-title">
                <i class="bi bi-grid"></i>3. Matriks Normalisasi (Xij*)
            </h6>
        </div>
        <div class="table-responsive">
            <table class="table align-middle mb-0 tbl-navy tbl-navy-center">
                <thead>
                    <tr>
                        <th>Alternatif</th>
                        @foreach($moora['kriterias'] as $k)
                            <th>
                                {{ $k['kode_kriteria'] }}<br>
                                @if($k['tipe'] === 'benefit')
                                    <span style="background:#D1FAE5; color:#065F46; font-size:.6rem; font-weight:700; padding:.1rem .4rem; border-radius:10px;">Benefit</span>
                                @else
                                    <span style="background:#FEE2E2; color:#991B1B; font-size:.6rem; font-weight:700; padding:.1rem .4rem; border-radius:10px;">Cost</span>
                                @endif
                            </th>
                        @endforeach
                    </tr>
                    <tr>
                        <td style="font-style:italic; color:#64748B; font-size:.8rem; font-weight:600; background:#F0F4F8 !important;">Bobot (wj)</td>
                        @foreach($moora['kriterias'] as $k)
                            <td style="font-weight:700; color:#1E3A5F; font-size:.82rem; background:#F0F4F8 !important;">{{ number_format($k['bobot'], 3) }}</td>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($moora['pengajuans'] as $i => $peng)
                    <tr>
                        <td style="font-weight:600; color:#1E293B; font-size:.83rem;">{{ $peng['nama'] }}</td>
                        @foreach($moora['kriterias'] as $j => $k)
                            <td style="font-size:.82rem;">{{ number_format($moora['normalized'][$i][$j], 3) }}</td>
                        @endforeach
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Total Skor & Ranking --}}
    <div class="page-card mb-4">
        <div class="card-head">
            <h6 class="card-head-title">
                <i class="bi bi-trophy"></i>4. Total Skor &amp; Ranking
            </h6>
        </div>
        <div class="card-body-inner">
            <div class="alert alert-info small mb-4" style="border-radius:10px;">
                <i class="bi bi-info-circle me-2"></i>
                <strong>Rumus:</strong>
                Yi = Σ(wj × Xij*) <strong style="color:#065F46;">Benefit</strong>
                − Σ(wj × Xij*) <strong style="color:#991B1B;">Cost</strong>
                &nbsp;|&nbsp;
                <strong>Kelayakan:</strong> ditentukan oleh kuota masing-masing jenis bantuan —
                ranking 1 s.d. kuota pada jenis bantuan tersebut dinyatakan <strong style="color:#065F46;">Layak</strong>,
                sisanya <strong style="color:#991B1B;">Tidak Layak</strong>.
            </div>
            <div class="table-responsive">
                <table class="table align-middle mb-0 tbl-navy tbl-navy-center">
                    <thead>
                        <tr>
                            <th style="width:100px;">Ranking</th>
                            <th style="text-align:left !important;">Nama</th>
                            <th>NIK</th>
                            <th>Jenis Bantuan</th>
                            <th>Total Skor</th>
                            <th style="width:150px;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($moora['ranked'] as $rank => $r)
                        @php $isLayak = $r['status'] === 'Layak'; @endphp
                        <tr>
                            <td>
                                <span class="rank-badge {{ ($rank + 1) <= 3 ? 'rank-'.($rank+1) : 'rank-n' }}">
                                    {{ $rank + 1 }}
                                </span>
                            </td>
                            <td style="font-weight:600; color:#1E293B; font-size:.83rem; text-align:left !important;">{{ $r['nama'] }}</td>
                            <td style="color:#64748B; font-size:.8rem; font-family:monospace;">{{ $r['nik'] }}</td>
                            <td>
                                <span style="background:#EFF6FF; color:#1E3A5F; font-size:.7rem; font-weight:700; padding:.25rem .75rem; border-radius:20px; display:inline-block;">
                                    {{ $r['jenis_bantuan'] }}
                                </span>
                            </td>
                            <td style="font-family:monospace; font-weight:700; color:#1E293B; font-size:.82rem;">
                                {{ number_format($r['yi'], 3) }}
                            </td>
                            <td>
                                @if($isLayak)
                                    <span style="background:#D1FAE5; color:#065F46; font-size:.7rem; font-weight:700; padding:.28rem .75rem; border-radius:20px; display:inline-block;">Layak</span>
                                @else
                                    <span style="background:#FEE2E2; color:#991B1B; font-size:.7rem; font-weight:700; padding:.28rem .75rem; border-radius:20px; display:inline-block;">Tidak Layak</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

</div>
@endsection