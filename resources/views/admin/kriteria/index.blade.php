@extends('admin.layouts.app')

@section('title', 'Kriteria & Perhitungan AHP')
@section('page-title', 'Kriteria')
@section('breadcrumb', 'Kriteria')

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
.tbl-navy tbody tr {
    border-bottom: 1px solid #F1F5F9 !important;
    transition: background .15s;
}
.tbl-navy tbody tr:last-child { border-bottom: none !important; }
.tbl-navy tbody tr:hover td { background: #F8FAFC !important; }
.tbl-navy tbody td {
    padding: .85rem 1rem !important;
    border: none !important;
    border-top: none !important;
    vertical-align: middle !important;
}
.btn-action {
    width: 30px; height: 30px; padding: 0;
    display: inline-flex; align-items: center; justify-content: center;
    border-radius: 8px; font-size: .78rem;
}
</style>
@endpush

@section('content')
<div class="container-fluid">

    {{-- Header --}}
    <div class="page-header">
        <div>
            <h2 class="page-header-title">
                <i class="bi bi-diagram-3-fill me-2" style="color:var(--accent);"></i>Kriteria &amp; Perhitungan AHP
            </h2>
            <p class="page-header-sub">Kelola kriteria dan hitung bobot menggunakan metode Analytical Hierarchy Process</p>
        </div>
    </div>

    {{-- SECTION 1 : DATA KRITERIA --}}
    <div class="page-card mb-4">
        <div class="card-head">
            <h6 class="card-head-title">
                <i class="bi bi-list-check"></i>1. Data Kriteria
            </h6>
            <button class="btn btn-primary btn-sm rounded-3"
                    data-bs-toggle="modal" data-bs-target="#modalTambahKriteria">
                <i class="bi bi-plus-circle me-1"></i>Tambah Kriteria
            </button>
        </div>
        <div class="table-responsive">
            <table class="table align-middle mb-0 tbl-navy">
                <thead>
                    <tr>
                        <th style="width:80px;">Kode</th>
                        <th>Nama Kriteria</th>
                        <th style="width:120px;">Tipe</th>
                        <th style="width:140px;">Bobot (AHP)</th>
                        <th style="width:140px; text-align:center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($kriterias as $k)
                    <tr>
                        <td>
                            <span style="background:#EFF6FF; color:#1E3A5F; font-size:.7rem; font-weight:700; padding:.25rem .75rem; border-radius:20px; display:inline-block;">
                                {{ $k->kode_kriteria }}
                            </span>
                        </td>
                        <td style="font-weight:600; color:#1E293B; font-size:.83rem;">{{ $k->nama }}</td>
                        <td>
                            @if($k->tipe === 'benefit')
                                <span style="background:#D1FAE5; color:#065F46; font-size:.7rem; font-weight:700; padding:.25rem .75rem; border-radius:20px; display:inline-block;">Benefit</span>
                            @else
                                <span style="background:#FEE2E2; color:#991B1B; font-size:.7rem; font-weight:700; padding:.25rem .75rem; border-radius:20px; display:inline-block;">Cost</span>
                            @endif
                        </td>
                        <td>
                            @if($k->bobot > 0)
                                <span style="font-family:monospace; font-weight:700; color:#2D6A4F; font-size:.83rem;">{{ number_format($k->bobot, 3) }}</span>
                            @else
                                <span style="color:#94A3B8; font-style:italic; font-size:.8rem;">Belum dihitung</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex justify-content-center gap-1">
                                <a href="{{ route('admin.subkriteria.index', ['kriteria_id' => $k->kriteria_id]) }}"
                                   class="btn btn-sm btn-info btn-action" title="Sub Kriteria">
                                    <i class="bi bi-list-nested"></i>
                                </a>
                                <button class="btn btn-sm btn-warning btn-action"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalEdit{{ $k->kriteria_id }}"
                                        title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <form action="{{ route('admin.kriteria.destroy', $k->kriteria_id) }}"
                                      method="POST"
                                      onsubmit="return confirm('Hapus kriteria ini?\nSemua data perbandingan terkait akan ikut terhapus.')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-danger btn-action" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>

                    {{-- Modal Edit --}}
                    <div class="modal fade" id="modalEdit{{ $k->kriteria_id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content rounded-4">
                                <div class="modal-header border-0 pb-0">
                                    <h6 class="modal-title fw-bold">
                                        <i class="bi bi-pencil-square me-2 text-warning"></i>Edit Kriteria {{ $k->kode_kriteria }}
                                    </h6>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <form action="{{ route('admin.kriteria.update', $k->kriteria_id) }}" method="POST">
                                    @csrf @method('PUT')
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Kode Kriteria</label>
                                            <input type="text" class="form-control rounded-3 bg-light"
                                                   value="{{ $k->kode_kriteria }}" disabled readonly>
                                            <div class="form-text">Kode otomatis, tidak dapat diubah.</div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Nama Kriteria <span class="text-danger">*</span></label>
                                            <input type="text" name="nama" class="form-control rounded-3"
                                                   value="{{ $k->nama }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Tipe <span class="text-danger">*</span></label>
                                            <select name="tipe" class="form-select rounded-3" required>
                                                <option value="benefit" {{ $k->tipe === 'benefit' ? 'selected' : '' }}>Benefit (semakin besar semakin baik)</option>
                                                <option value="cost"    {{ $k->tipe === 'cost'    ? 'selected' : '' }}>Cost (semakin kecil semakin baik)</option>
                                            </select>
                                        </div>
                                        <div class="mb-1">
                                            <label class="form-label fw-semibold">Bobot</label>
                                            <input type="text" class="form-control rounded-3 bg-light"
                                                   value="{{ $k->bobot > 0 ? number_format($k->bobot, 3) : '-' }}"
                                                   disabled readonly>
                                            <div class="form-text">Bobot otomatis dari hasil perhitungan AHP.</div>
                                        </div>
                                    </div>
                                    <div class="modal-footer border-0 pt-0">
                                        <button type="button" class="btn btn-secondary rounded-3" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-primary rounded-3">
                                            <i class="bi bi-save me-1"></i>Simpan
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    @empty
                    <tr>
                        <td colspan="5">
                            <div class="empty-state" style="padding:3rem;">
                                <i class="bi bi-inbox" style="font-size:2rem;"></i>
                                <p>Belum ada kriteria. Klik <strong>Tambah Kriteria</strong> untuk memulai.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- SECTION 2 : PERBANDINGAN KRITERIA --}}
    @if($kriterias->count() >= 2)
    <div class="page-card mb-4">
        <div class="card-head">
            <h6 class="card-head-title">
                <i class="bi bi-arrow-left-right"></i>2. Perbandingan Berpasangan (Skala Saaty 1–9)
            </h6>
        </div>
        <div class="card-body-inner">

            <div class="alert alert-info small mb-4" style="border-radius:10px;">
                <i class="bi bi-info-circle-fill me-2"></i>
                <strong>Panduan Skala Saaty:</strong>
                <span class="ms-1">
                    1 = Sama penting &nbsp;|&nbsp;
                    3 = Sedikit lebih penting &nbsp;|&nbsp;
                    5 = Lebih penting &nbsp;|&nbsp;
                    7 = Sangat lebih penting &nbsp;|&nbsp;
                    9 = Mutlak lebih penting &nbsp;|&nbsp;
                    2, 4, 6, 8 = Nilai tengah
                </span>
            </div>

            <form action="{{ route('admin.kriteria.perbandingan') }}" method="POST">
                @csrf
                <div class="table-responsive mb-3">
                    <table class="table align-middle mb-0 tbl-navy">
                        <thead>
                            <tr>
                                <th style="width:35%;">Kriteria A</th>
                                <th style="width:40px; text-align:center;">vs</th>
                                <th style="width:35%;">Kriteria B</th>
                                <th>Nilai Perbandingan (A terhadap B)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pasangan as $idx => $p)
                            @php
                                $key   = $p['pertama']->kriteria_id . '_' . $p['kedua']->kriteria_id;
                                $nilai = $perbandingan[$key]->nilai_perbandingan ?? 1;
                            @endphp
                            <tr>
                                <input type="hidden" name="perbandingan[{{ $idx }}][id_a]" value="{{ $p['pertama']->kriteria_id }}">
                                <input type="hidden" name="perbandingan[{{ $idx }}][id_b]" value="{{ $p['kedua']->kriteria_id }}">
                                <td style="font-weight:600; color:#1E293B; font-size:.83rem;">
                                    <span style="background:#EFF6FF; color:#1E3A5F; font-size:.7rem; font-weight:700; padding:.2rem .6rem; border-radius:20px; display:inline-block; margin-right:.4rem;">{{ $p['pertama']->kode_kriteria }}</span>
                                    {{ $p['pertama']->nama }}
                                </td>
                                <td style="text-align:center; color:#94A3B8; font-weight:700;">vs</td>
                                <td style="font-weight:600; color:#1E293B; font-size:.83rem;">
                                    <span style="background:#F1F5F9; color:#64748B; font-size:.7rem; font-weight:700; padding:.2rem .6rem; border-radius:20px; display:inline-block; margin-right:.4rem;">{{ $p['kedua']->kode_kriteria }}</span>
                                    {{ $p['kedua']->nama }}
                                </td>
                                <td>
                                    <select name="perbandingan[{{ $idx }}][nilai]" class="form-select form-select-sm rounded-3" style="font-size:.8rem;">
                                        @foreach([
                                            '9'        => '9 – A mutlak lebih penting dari B',
                                            '8'        => '8',
                                            '7'        => '7 – A sangat lebih penting dari B',
                                            '6'        => '6',
                                            '5'        => '5 – A lebih penting dari B',
                                            '4'        => '4',
                                            '3'        => '3 – A sedikit lebih penting dari B',
                                            '2'        => '2',
                                            '1'        => '1 – Sama penting',
                                            '0.5'      => '1/2',
                                            '0.333333' => '1/3 – B sedikit lebih penting dari A',
                                            '0.25'     => '1/4',
                                            '0.2'      => '1/5 – B lebih penting dari A',
                                            '0.166667' => '1/6',
                                            '0.142857' => '1/7 – B sangat lebih penting dari A',
                                            '0.125'    => '1/8',
                                            '0.111111' => '1/9 – B mutlak lebih penting dari A',
                                        ] as $val => $label)
                                        <option value="{{ $val }}" {{ abs((float)$nilai - (float)$val) < 0.0001 ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <button type="submit" class="btn btn-success">
                    <i class="bi bi-save me-1"></i>Simpan Perbandingan
                </button>
            </form>

            <hr class="my-4">

            <form action="{{ route('admin.kriteria.hitung') }}" method="POST">
                @csrf
                <div class="d-flex align-items-center gap-3 flex-wrap">
                    <button type="submit" class="btn btn-primary fw-semibold px-4">
                        <i class="bi bi-calculator me-1"></i>Hitung AHP
                    </button>
                    <span style="color:#64748B; font-size:.82rem;">
                        <i class="bi bi-info-circle me-1"></i>
                        Simpan perbandingan terlebih dahulu, lalu klik <strong>Hitung AHP</strong>.
                        Bobot otomatis tersimpan jika CR ≤ 0,1.
                    </span>
                </div>
            </form>
        </div>
    </div>

    @elseif($kriterias->count() < 2)
    <div class="alert alert-warning rounded-3 d-flex align-items-center gap-2">
        <i class="bi bi-exclamation-triangle-fill fs-5"></i>
        <div>Tambahkan minimal <strong>2 kriteria</strong> untuk mengisi perbandingan dan menghitung AHP.</div>
    </div>
    @endif

    {{-- SECTION 3 : HASIL PERHITUNGAN AHP --}}
    @if(session('hasil_ahp'))
    @php $ahp = session('hasil_ahp'); @endphp

    <div class="page-card mb-4" id="hasilAhp">
        <div class="card-head">
            <h6 class="card-head-title">
                <i class="bi bi-bar-chart-steps"></i>3. Hasil Perhitungan AHP
            </h6>
            @if($ahp['konsisten'])
                <span style="background:#D1FAE5; color:#065F46; font-size:.72rem; font-weight:700; padding:.3rem .85rem; border-radius:20px; display:inline-flex; align-items:center; gap:.3rem;">
                    <i class="bi bi-check-circle-fill"></i>Konsisten
                </span>
            @else
                <span style="background:#FEE2E2; color:#991B1B; font-size:.72rem; font-weight:700; padding:.3rem .85rem; border-radius:20px; display:inline-flex; align-items:center; gap:.3rem;">
                    <i class="bi bi-x-circle-fill"></i>Tidak Konsisten
                </span>
            @endif
        </div>
        <div class="card-body-inner">

            {{-- 3a. Matriks Perbandingan --}}
            <h6 style="font-weight:700; color:#1E293B; margin-bottom:1rem;">
                <span style="background:#64748B; color:#fff; font-size:.7rem; font-weight:700; padding:.2rem .6rem; border-radius:6px; margin-right:.5rem;">a</span>
                Matriks Perbandingan Berpasangan
            </h6>
            <div class="table-responsive mb-4">
                <table class="table align-middle mb-0 tbl-navy" style="text-align:center;">
                    <thead>
                        <tr>
                            <th style="text-align:left;">Kriteria</th>
                            @foreach($ahp['kriterias'] as $k)
                                <th>{{ $k['kode_kriteria'] }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($ahp['kriterias'] as $i => $ki)
                        <tr>
                            <td style="font-weight:600; color:#1E293B; font-size:.83rem; text-align:left; background:#F8FAFC !important;">
                                <span style="background:#EFF6FF; color:#1E3A5F; font-size:.7rem; font-weight:700; padding:.2rem .6rem; border-radius:20px; display:inline-block; margin-right:.4rem;">{{ $ki['kode_kriteria'] }}</span>
                                {{ $ki['nama'] }}
                            </td>
                            @foreach($ahp['kriterias'] as $j => $kj)
                                <td style="{{ $i === $j ? 'background:#F1F5F9 !important; font-weight:700;' : '' }} font-size:.82rem;">
                                    {{ number_format($ahp['matrix'][$i][$j], 3) }}
                                </td>
                            @endforeach
                        </tr>
                        @endforeach
                        <tr>
                            <td style="font-style:italic; color:#64748B; font-size:.8rem; text-align:left; background:#F8FAFC !important;">Jumlah Kolom</td>
                            @foreach($ahp['jumlahKolom'] as $jk)
                                <td style="font-weight:700; color:#1E3A5F; font-size:.82rem;">{{ number_format($jk, 3) }}</td>
                            @endforeach
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- 3b. Matriks Normalisasi --}}
            <h6 style="font-weight:700; color:#1E293B; margin-bottom:1rem;">
                <span style="background:#64748B; color:#fff; font-size:.7rem; font-weight:700; padding:.2rem .6rem; border-radius:6px; margin-right:.5rem;">b</span>
                Matriks Normalisasi &amp; Eigen Vector (Priority Vector)
            </h6>
            <div class="table-responsive mb-4">
                <table class="table align-middle mb-0 tbl-navy" style="text-align:center;">
                    <thead>
                        <tr>
                            <th style="text-align:left;">Kriteria</th>
                            @foreach($ahp['kriterias'] as $k)
                                <th>{{ $k['kode_kriteria'] }}</th>
                            @endforeach
                            <th>Eigen Vector</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($ahp['kriterias'] as $i => $ki)
                        <tr>
                            <td style="font-weight:600; color:#1E293B; font-size:.83rem; text-align:left; background:#F8FAFC !important;">
                                <span style="background:#EFF6FF; color:#1E3A5F; font-size:.7rem; font-weight:700; padding:.2rem .6rem; border-radius:20px; display:inline-block; margin-right:.4rem;">{{ $ki['kode_kriteria'] }}</span>
                                {{ $ki['nama'] }}
                            </td>
                            @foreach($ahp['kriterias'] as $j => $kj)
                                <td style="font-size:.82rem;">{{ number_format($ahp['normalized'][$i][$j], 3) }}</td>
                            @endforeach
                            <td style="font-weight:700; color:#2D6A4F; font-size:.82rem;">
                                {{ number_format($ahp['eigenVector'][$i], 3) }}
                            </td>
                        </tr>
                        @endforeach
                        <tr>
                            <td style="font-style:italic; color:#64748B; font-size:.8rem; text-align:left; background:#F8FAFC !important;">Jumlah Kolom</td>
                            @foreach($ahp['kriterias'] as $j => $kj)
                                @php $sumNorm = 0; foreach($ahp['normalized'] as $row) { $sumNorm += $row[$j]; } @endphp
                                <td style="font-weight:700; color:#1E3A5F; font-size:.82rem;">{{ number_format($sumNorm, 3) }}</td>
                            @endforeach
                            <td style="font-weight:700; color:#2D6A4F; font-size:.82rem;">{{ number_format(array_sum($ahp['eigenVector']), 3) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- 3c. Weighted Sum Vector --}}
            <h6 style="font-weight:700; color:#1E293B; margin-bottom:1rem;">
                <span style="background:#64748B; color:#fff; font-size:.7rem; font-weight:700; padding:.2rem .6rem; border-radius:6px; margin-right:.5rem;">c</span>
                Weighted Sum Vector (A × w)
            </h6>
            <div class="table-responsive mb-4">
                <table class="table align-middle mb-0 tbl-navy" style="text-align:center;">
                    <thead>
                        <tr>
                            <th style="text-align:left;">Kriteria</th>
                            <th>Weighted Sum Vector</th>
                            <th>Eigen Vector (w)</th>
                            <th>Weighted Sum / Eigen (λ<sub>i</sub>)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($ahp['kriterias'] as $i => $ki)
                        @php
                            $ws = $ahp['weightedSum'][$i];
                            $ev = $ahp['eigenVector'][$i];
                            $li = ($ev > 0) ? $ws / $ev : 0;
                        @endphp
                        <tr>
                            <td style="font-weight:600; color:#1E293B; font-size:.83rem; text-align:left; background:#F8FAFC !important;">
                                <span style="background:#EFF6FF; color:#1E3A5F; font-size:.7rem; font-weight:700; padding:.2rem .6rem; border-radius:20px; display:inline-block; margin-right:.4rem;">{{ $ki['kode_kriteria'] }}</span>
                                {{ $ki['nama'] }}
                            </td>
                            <td style="font-size:.82rem;">{{ number_format($ws, 3) }}</td>
                            <td style="font-weight:700; color:#2D6A4F; font-size:.82rem;">{{ number_format($ev, 3) }}</td>
                            <td style="font-weight:700; color:#1E293B; font-size:.82rem;">{{ number_format($li, 3) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- 3d. Uji Konsistensi --}}
            <h6 style="font-weight:700; color:#1E293B; margin-bottom:1rem;">
                <span style="background:#64748B; color:#fff; font-size:.7rem; font-weight:700; padding:.2rem .6rem; border-radius:6px; margin-right:.5rem;">d</span>
                Uji Konsistensi
            </h6>
            <div class="row g-3 mb-4">
                @foreach([
                    ['label' => 'n (Jumlah Kriteria)',   'value' => $ahp['n'],                           'icon' => 'bi-hash',       'bg' => '#F1F5F9', 'color' => '#475569'],
                    ['label' => 'λ Max',                  'value' => number_format($ahp['lambdaMax'], 3), 'icon' => 'bi-graph-up',   'bg' => '#EFF6FF', 'color' => '#1E3A5F'],
                    ['label' => 'CI (Consistency Index)', 'value' => number_format($ahp['ci'], 3),        'icon' => 'bi-calculator', 'bg' => '#E0F2FE', 'color' => '#0369A1'],
                    ['label' => 'RI (Random Index)',      'value' => number_format($ahp['ri'], 3),        'icon' => 'bi-table',      'bg' => '#F1F5F9', 'color' => '#475569'],
                    ['label' => 'CR (Consistency Ratio)', 'value' => number_format($ahp['cr'], 3),        'icon' => 'bi-check2-all', 'bg' => $ahp['konsisten'] ? '#D1FAE5' : '#FEE2E2', 'color' => $ahp['konsisten'] ? '#065F46' : '#991B1B'],
                ] as $item)
                <div class="col-6 col-md-4 col-lg">
                    <div style="background:{{ $item['bg'] }}; border-radius:12px; padding:1rem; text-align:center;">
                        <i class="bi {{ $item['icon'] }}" style="font-size:1.3rem; color:{{ $item['color'] }};"></i>
                        <p style="font-size:.75rem; color:#64748B; margin:.5rem 0 .25rem;">{{ $item['label'] }}</p>
                        <p style="font-weight:700; color:{{ $item['color'] }}; margin:0; font-size:.9rem;">{{ $item['value'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="alert alert-light border rounded-3 small mb-3">
                <strong>Rumus:</strong>
                CI = (λMax − n) / (n − 1) = ({{ number_format($ahp['lambdaMax'], 3) }} − {{ $ahp['n'] }}) / ({{ $ahp['n'] }} − 1) = <strong>{{ number_format($ahp['ci'], 3) }}</strong>
                &nbsp;|&nbsp;
                CR = CI / RI = {{ number_format($ahp['ci'], 3) }} / {{ number_format($ahp['ri'], 3) }} = <strong>{{ number_format($ahp['cr'], 3) }}</strong>
            </div>

            @if($ahp['konsisten'])
            <div class="alert alert-success rounded-3 fw-semibold d-flex align-items-center gap-2">
                <i class="bi bi-check-circle-fill fs-5"></i>
                <div>
                    CR = {{ number_format($ahp['cr'], 3) }} ≤ 0,1 →
                    <strong>Perbandingan Konsisten.</strong>
                    Bobot kriteria (Eigen Vector) telah disimpan ke database.
                </div>
            </div>
            @else
            <div class="alert alert-danger rounded-3 fw-semibold d-flex align-items-center gap-2">
                <i class="bi bi-x-circle-fill fs-5"></i>
                <div>
                    CR = {{ number_format($ahp['cr'], 3) }} &gt; 0,1 →
                    <strong>Perbandingan Tidak Konsisten.</strong>
                    Bobot tidak disimpan. Silakan perbaiki nilai perbandingan dan hitung ulang.
                </div>
            </div>
            @endif

        </div>
    </div>
    @endif

</div>

{{-- Modal Tambah Kriteria --}}
<div class="modal fade" id="modalTambahKriteria" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title fw-bold">
                    <i class="bi bi-plus-circle me-2 text-primary"></i>Tambah Kriteria Baru
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.kriteria.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Kode Kriteria</label>
                        <input type="text" class="form-control rounded-3 bg-light"
                               value="C{{ $kriterias->count() + 1 }}" disabled readonly>
                        <div class="form-text">Kode dibuat otomatis (C1, C2, C3, ...).</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Kriteria <span class="text-danger">*</span></label>
                        <input type="text" name="nama" class="form-control rounded-3"
                               placeholder="Contoh: Penghasilan Bulanan" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tipe <span class="text-danger">*</span></label>
                        <select name="tipe" class="form-select rounded-3" required>
                            <option value="">-- Pilih Tipe --</option>
                            <option value="benefit">Benefit (semakin besar semakin baik)</option>
                            <option value="cost">Cost (semakin kecil semakin baik)</option>
                        </select>
                        <div class="form-text">Contoh Benefit: Jumlah Tanggungan. Contoh Cost: Penghasilan.</div>
                    </div>
                    <div class="mb-1">
                        <label class="form-label fw-semibold">Bobot</label>
                        <input type="text" class="form-control rounded-3 bg-light"
                               value="Otomatis dari perhitungan AHP" disabled readonly>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-secondary rounded-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-3">
                        <i class="bi bi-plus-circle me-1"></i>Tambah
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
@if(session('hasil_ahp'))
    document.addEventListener('DOMContentLoaded', function () {
        const el = document.getElementById('hasilAhp');
        if (el) setTimeout(() => el.scrollIntoView({ behavior: 'smooth', block: 'start' }), 300);
    });
@endif
</script>
@endpush

@endsection