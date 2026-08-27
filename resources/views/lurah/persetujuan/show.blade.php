@extends('admin.layouts.app')

@section('title', 'Detail Calon Penerima')
@section('page-title', 'Tinjau & Putuskan Calon Penerima')
@section('breadcrumb', 'Persetujuan')

@section('content')
<div class="container-fluid">

    {{-- ── Back Button & Title ── --}}
    <div class="d-flex align-items-center mb-4 gap-3">
        <a href="{{ route('admin.lurah.persetujuan.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
        <div>
            <h4 class="fw-bold mb-0">
                <i class="bi bi-person-check-fill me-2" style="color:var(--accent);"></i>
                Tinjauan Calon Penerima
            </h4>
            <small class="text-muted">Periksa data dan keputusan persetujuan penerima bantuan sosial</small>
        </div>
    </div>

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

    {{-- ── MOORA Result Banner ── --}}
    @php
        $isLayak = $isRekomendasiLayak;
        $statusPersetujuan = $hasilAkhir->persetujuan_status ?? 'Menunggu Persetujuan';
        $psBadge = [
            'Menunggu Persetujuan' => ['bg'=>'#FEF3C7','color'=>'#92400E','icon'=>'bi-hourglass-split','label'=>'Menunggu Persetujuan Lurah'],
            'Disetujui'            => ['bg'=>'#D1FAE5','color'=>'#065F46','icon'=>'bi-patch-check-fill','label'=>'Telah Disetujui'],
            'Ditolak'              => ['bg'=>'#FEE2E2','color'=>'#991B1B','icon'=>'bi-x-circle-fill','label'=>'Ditolak oleh Lurah'],
        ][$statusPersetujuan] ?? ['bg'=>'#F1F5F9','color'=>'#64748B','icon'=>'bi-question-circle','label'=>$statusPersetujuan];
    @endphp

    <div class="row g-3 mb-4">
        {{-- Nilai MOORA Card --}}
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon navy"><i class="bi bi-graph-up-arrow"></i></div>
                <div>
                    <div class="stat-val" style="font-size:1.4rem;">{{ number_format($hasilAkhir->nilai_yi, 4) }}</div>
                    <div class="stat-lbl">Nilai Akhir MOORA (Yi)</div>
                </div>
            </div>
        </div>
        {{-- Ranking Card --}}
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon gold"><i class="bi bi-trophy-fill"></i></div>
                <div>
                    <div class="stat-val" style="font-size:1.4rem;">#{{ $rankingDalamBantuan }}</div>
                    <div class="stat-lbl">Ranking dalam Jenis Bantuan</div>
                </div>
            </div>
        </div>
        {{-- Rekomendasi MOORA Card --}}
        <div class="col-md-3">
            <div class="stat-card" style="{{ $isLayak ? 'border:2px solid #059669;' : 'border:2px solid #DC2626;' }}">
                @if($isLayak)
                    <div class="stat-icon green"><i class="bi bi-check-circle-fill"></i></div>
                    <div>
                        <div class="stat-val" style="color:#059669; font-size:1.2rem;">Layak</div>
                        <div class="stat-lbl">Rekomendasi MOORA</div>
                    </div>
                @else
                    <div class="stat-icon red"><i class="bi bi-x-circle-fill"></i></div>
                    <div>
                        <div class="stat-val" style="color:#DC2626; font-size:1.2rem;">Tidak Layak</div>
                        <div class="stat-lbl">Rekomendasi MOORA</div>
                    </div>
                @endif
            </div>
        </div>
        {{-- Status Persetujuan Card --}}
        <div class="col-md-3">
            <div class="stat-card" style="border:2px solid {{ $psBadge['color'] }}20;">
                <div class="stat-icon" style="background:{{ $psBadge['bg'] }}; color:{{ $psBadge['color'] }};">
                    <i class="bi {{ $psBadge['icon'] }}"></i>
                </div>
                <div>
                    <div class="stat-val" style="color:{{ $psBadge['color'] }}; font-size:1rem; font-weight:800;">
                        {{ $psBadge['label'] }}
                    </div>
                    <div class="stat-lbl">Status Persetujuan Lurah</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- Left Column: Data Diri & Ekonomi --}}
        <div class="col-lg-8">

            {{-- Data Identitas --}}
            <div class="card shadow-sm border-0 rounded-3 mb-4">
                <div class="card-header fw-bold text-white border-0"
                     style="background: linear-gradient(135deg, #1E3A5F, #2D6A4F);">
                    <i class="bi bi-person-badge-fill me-2"></i> Data Identitas Pemohon
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped table-hover mb-0">
                        <tr>
                            <th width="220" class="ps-4">Nama Lengkap</th>
                            <td>: <strong>{{ $hasilAkhir->pengajuan->nama ?? '-' }}</strong></td>
                        </tr>
                        <tr>
                            <th class="ps-4">NIK</th>
                            <td>: <code class="text-dark fw-bold fs-6">{{ $hasilAkhir->pengajuan->nik ?? '-' }}</code></td>
                        </tr>
                        <tr>
                            <th class="ps-4">No. Telepon</th>
                            <td>: {{ $hasilAkhir->pengajuan->no_telepon ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th class="ps-4">Jenis Kelamin</th>
                            <td>: {{ ($hasilAkhir->pengajuan->jenis_kelamin ?? '') === 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                        </tr>
                        <tr>
                            <th class="ps-4">Tanggal Lahir</th>
                            <td>: {{ $hasilAkhir->pengajuan->tanggal_lahir?->format('d F Y') ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th class="ps-4">Pendidikan</th>
                            <td>: {{ $hasilAkhir->pengajuan->pendidikan ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th class="ps-4">Alamat Lengkap</th>
                            <td>: {{ $hasilAkhir->pengajuan->alamat ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th class="ps-4">Bantuan yang Diajukan</th>
                            <td>:
                                <span class="badge bg-primary">
                                    {{ $hasilAkhir->pengajuan->bantuanSosial->nama_bantuan ?? '-' }}
                                </span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            {{-- Kondisi Sosial Ekonomi --}}
            <div class="card shadow-sm border-0 rounded-3 mb-4">
                <div class="card-header fw-bold text-white border-0"
                     style="background: linear-gradient(135deg, #1E3A5F, #2D6A4F);">
                    <i class="bi bi-cash-stack me-2"></i> Kondisi Sosial Ekonomi
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped table-hover mb-0">
                        <tr>
                            <th width="220" class="ps-4">Penghasilan Bulanan</th>
                            <td>:
                                @if($hasilAkhir->pengajuan->penghasilan !== null)
                                    <span class="fw-semibold text-success">
                                        Rp {{ number_format($hasilAkhir->pengajuan->penghasilan, 0, ',', '.') }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th class="ps-4">Jumlah Tanggungan</th>
                            <td>: {{ $hasilAkhir->pengajuan->jumlah_tanggungan ?? '-' }} orang</td>
                        </tr>
                        <tr>
                            <th class="ps-4">Pekerjaan</th>
                            <td>: {{ $hasilAkhir->pengajuan->pekerjaan ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th class="ps-4">Kepemilikan Rumah</th>
                            <td>: {{ $hasilAkhir->pengajuan->kepemilikan_rumah ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            {{-- Form Keputusan (hanya jika belum diproses) --}}
            @if(!$hasilAkhir->sudahDiproses())
            <div class="card shadow-sm rounded-3 mb-4" style="border: 2px solid #F59E0B;">
                <div class="card-header fw-bold bg-warning text-dark border-0">
                    <i class="bi bi-gavel me-2"></i> Keputusan Lurah
                </div>
                <div class="card-body bg-white rounded-bottom">
                    <div class="alert alert-info py-2 mb-4" style="font-size:.82rem;">
                        <i class="bi bi-info-circle-fill me-1"></i>
                        Rekomendasi MOORA hanya bersifat teknis. Keputusan akhir penerimaan bantuan ada di tangan Lurah.
                    </div>

                    <div class="row g-3">
                        {{-- Tombol Setujui --}}
                        <div class="col-md-6">
                            <form action="{{ route('admin.lurah.persetujuan.setujui', $hasilAkhir->hasil_id) }}"
                                  method="POST"
                                  onsubmit="return confirm('Yakin menyetujui {{ addslashes($hasilAkhir->pengajuan->nama ?? '') }} sebagai penerima bantuan?')">
                                @csrf
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-success btn-lg py-3">
                                        <i class="bi bi-patch-check-fill me-2 fs-5"></i>
                                        Setujui Sebagai Penerima
                                    </button>
                                </div>
                            </form>
                        </div>

                        {{-- Panel Tolak --}}
                        <div class="col-md-6">
                            <button type="button" class="btn btn-danger btn-lg py-3 w-100" id="btn-show-tolak">
                                <i class="bi bi-x-circle-fill me-2 fs-5"></i>
                                Tolak Calon Penerima
                            </button>
                        </div>
                    </div>

                    {{-- Form Tolak (tersembunyi) --}}
                    <div id="div-tolak" class="mt-4" style="display:none;">
                        <hr>
                        <h6 class="text-danger fw-bold mb-3">
                            <i class="bi bi-exclamation-triangle-fill me-1"></i>
                            Formulir Penolakan
                        </h6>
                        <form action="{{ route('admin.lurah.persetujuan.tolak', $hasilAkhir->hasil_id) }}"
                              method="POST"
                              onsubmit="return confirm('Yakin menolak {{ addslashes($hasilAkhir->pengajuan->nama ?? '') }}? Tindakan ini tidak dapat dibatalkan.')">
                            @csrf
                            <div class="mb-3">
                                <label for="alasan_penolakan_lurah" class="form-label fw-semibold text-danger">
                                    Alasan Penolakan <span class="text-danger">*</span>
                                </label>
                                <textarea name="alasan_penolakan_lurah"
                                          id="alasan_penolakan_lurah"
                                          class="form-control @error('alasan_penolakan_lurah') is-invalid @enderror"
                                          rows="4"
                                          minlength="10"
                                          placeholder="Jelaskan alasan penolakan secara rinci (minimal 10 karakter)...">{{ old('alasan_penolakan_lurah') }}</textarea>
                                @error('alasan_penolakan_lurah')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text text-muted">
                                    Contoh: "Sudah terdaftar sebagai penerima bantuan jenis lain", "Data tidak sesuai hasil survei lapangan".
                                </div>
                            </div>
                            <div class="d-flex gap-2 justify-content-end">
                                <button type="button" class="btn btn-light" id="btn-cancel-tolak">Batal</button>
                                <button type="submit" class="btn btn-danger">
                                    <i class="bi bi-x-circle me-1"></i> Konfirmasi Penolakan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @else
            {{-- Log Keputusan (jika sudah diproses) --}}
            <div class="card shadow-sm border-0 rounded-3 mb-4">
                <div class="card-header fw-bold text-white border-0" style="background:#334155;">
                    <i class="bi bi-journal-check me-2"></i> Log Keputusan Lurah
                </div>
                <div class="card-body p-0">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <th width="220" class="ps-4">Keputusan</th>
                            <td>:
                                @if($statusPersetujuan === 'Disetujui')
                                    <span class="badge-layak"><i class="bi bi-patch-check-fill me-1"></i> Disetujui</span>
                                @else
                                    <span class="badge-tidaklayak"><i class="bi bi-x-circle-fill me-1"></i> Ditolak</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th class="ps-4">Diproses Oleh</th>
                            <td>: {{ $hasilAkhir->approvedBy->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th class="ps-4">Waktu Keputusan</th>
                            <td>: {{ $hasilAkhir->persetujuan_at ? $hasilAkhir->persetujuan_at->format('d F Y, H:i') : '-' }}</td>
                        </tr>
                        @if($statusPersetujuan === 'Ditolak' && $hasilAkhir->alasan_penolakan_lurah)
                        <tr>
                            <th class="ps-4 text-danger">Alasan Penolakan</th>
                            <td class="text-danger">: {{ $hasilAkhir->alasan_penolakan_lurah }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>
            @endif

        </div>

        {{-- Right Column: Dokumen Berkas --}}
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header fw-bold text-white border-0"
                     style="background: linear-gradient(135deg, #1E3A5F, #2D6A4F);">
                    <i class="bi bi-files me-2"></i> Berkas Pendukung
                </div>
                <div class="card-body bg-light rounded-bottom">
                    @foreach(['foto_ktp' => 'Foto KTP', 'foto_kk' => 'Foto Kartu Keluarga', 'foto_sktm' => 'Foto SKTM', 'foto_rumah' => 'Foto Rumah Depan'] as $field => $label)
                    <div class="mb-4 bg-white p-3 rounded shadow-sm border border-light">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-bold text-secondary small">{{ $label }}</span>
                            @if($hasilAkhir->pengajuan->$field ?? null)
                            <a href="{{ asset('storage/' . $hasilAkhir->pengajuan->$field) }}"
                               target="_blank"
                               class="btn btn-outline-primary py-0 px-2"
                               style="font-size:.7rem;">
                                <i class="bi bi-fullscreen"></i>
                            </a>
                            @endif
                        </div>
                        @if($hasilAkhir->pengajuan->$field ?? null)
                            <a href="{{ asset('storage/' . $hasilAkhir->pengajuan->$field) }}" target="_blank">
                                <img src="{{ asset('storage/' . $hasilAkhir->pengajuan->$field) }}"
                                     class="img-fluid rounded border"
                                     style="max-height:180px; width:100%; object-fit:contain; cursor:pointer;"
                                     alt="{{ $label }}">
                            </a>
                        @else
                            <div class="text-center py-4 text-muted">
                                <i class="bi bi-file-earmark-x fs-2"></i>
                                <div class="small mt-1">Tidak diunggah</div>
                            </div>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const btnShow   = document.getElementById('btn-show-tolak');
    const btnCancel = document.getElementById('btn-cancel-tolak');
    const divTolak  = document.getElementById('div-tolak');

    if (btnShow) {
        btnShow.addEventListener('click', function() {
            divTolak.style.display = 'block';
            divTolak.scrollIntoView({ behavior: 'smooth', block: 'start' });
            btnShow.style.display = 'none';
        });
    }

    if (btnCancel) {
        btnCancel.addEventListener('click', function() {
            divTolak.style.display = 'none';
            if (btnShow) btnShow.style.display = '';
        });
    }

    @if($errors->has('alasan_penolakan_lurah'))
        if (divTolak) {
            divTolak.style.display = 'block';
            if (btnShow) btnShow.style.display = 'none';
        }
    @endif
});
</script>
@endpush
@endsection
