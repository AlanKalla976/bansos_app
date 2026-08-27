@extends('admin.layouts.app')

@section('title', 'Detail & Validasi Berkas')
@section('page-title', 'Pemeriksaan Berkas Pengajuan')
@section('breadcrumb', 'Pemeriksaan Berkas')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('admin.petugas.validasi.index') }}" class="btn btn-outline-secondary me-3">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
        <h4 class="fw-bold mb-0"><i class="bi bi-shield-check me-2"></i>Pemeriksaan & Validasi Berkas</h4>
    </div>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm mb-4" role="alert">
            <i class="bi bi-x-circle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        {{-- Data Pemohon & Ekonomi --}}
        <div class="col-lg-8">
            {{-- Data Diri Pemohon --}}
            <div class="card shadow-sm border-0 rounded-3 mb-4">
                <div class="card-header fw-bold text-white border-0" style="background: #1E3A5F;">
                    <i class="bi bi-person-badge-fill me-2"></i> Data Identitas Pemohon
                </div>
                <div class="card-body bg-white rounded-bottom">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover mb-0">
                            <tr>
                                <th width="200">Nama Lengkap</th>
                                <td>: {{ $pengajuan->nama }}</td>
                            </tr>
                            <tr>
                                <th>NIK</th>
                                <td>: <code class="text-dark fw-bold">{{ $pengajuan->nik }}</code></td>
                            </tr>
                            <tr>
                                <th>No. Telepon</th>
                                <td>: {{ $pengajuan->no_telepon }}</td>
                            </tr>
                            <tr>
                                <th>Jenis Kelamin</th>
                                <td>: {{ $pengajuan->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                            </tr>
                            <tr>
                                <th>Tanggal Lahir</th>
                                <td>: {{ $pengajuan->tanggal_lahir?->format('d/m/Y') }}</td>
                            </tr>
                            <tr>
                                <th>Pendidikan</th>
                                <td>: {{ $pengajuan->pendidikan }}</td>
                            </tr>
                            <tr>
                                <th>Alamat Lengkap</th>
                                <td>: {{ $pengajuan->alamat }}</td>
                            </tr>
                            <tr>
                                <th>Bantuan Sosial yang Diajukan</th>
                                <td>: <span class="badge bg-primary text-white">{{ $pengajuan->bantuanSosial->nama_bantuan ?? '-' }}</span></td>
                            </tr>
                            <tr>
                                <th>Tanggal Pengajuan</th>
                                <td>: {{ $pengajuan->created_at->format('d F Y H:i') }}</td>
                            </tr>
                            <tr>
                                <th>Status Berkas</th>
                                <td>: 
                                    @php
                                        $badge = ['Menunggu'=>'warning','Diverifikasi'=>'success','Ditolak'=>'danger'][$pengajuan->status] ?? 'secondary';
                                        $statusText = ['Menunggu'=>'Menunggu Validasi','Diverifikasi'=>'Valid (Diverifikasi)','Ditolak'=>'Tidak Valid (Ditolak)'][$pengajuan->status] ?? $pengajuan->status;
                                    @endphp
                                    <span class="badge bg-{{ $badge }} fs-6">{{ $statusText }}</span>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Data Kondisi Ekonomi --}}
            <div class="card shadow-sm border-0 rounded-3 mb-4">
                <div class="card-header fw-bold text-white border-0" style="background: #1E3A5F;">
                    <i class="bi bi-cash-stack me-2"></i> Kondisi Sosial Ekonomi
                </div>
                <div class="card-body bg-white rounded-bottom">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover mb-0">
                            <tr>
                                <th width="200">Penghasilan Bulanan</th>
                                <td>: 
                                    @if($pengajuan->penghasilan !== null)
                                        <span class="fw-semibold text-success">Rp {{ number_format($pengajuan->penghasilan, 0, ',', '.') }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Jumlah Tanggungan</th>
                                <td>: {{ $pengajuan->jumlah_tanggungan ?? '-' }} orang</td>
                            </tr>
                            <tr>
                                <th>Pekerjaan Utama</th>
                                <td>: {{ $pengajuan->pekerjaan ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Status Kepemilikan Rumah</th>
                                <td>: {{ $pengajuan->kepemilikan_rumah ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Form Proses Validasi (Hanya muncul jika status Menunggu) --}}
            @if($pengajuan->status === 'Menunggu')
            <div class="card shadow-sm border-warning rounded-3 mb-4" style="border: 2px solid;">
                <div class="card-header fw-bold bg-warning text-dark border-0">
                    <i class="bi bi-clipboard2-check-fill me-2"></i> Ambil Tindakan Validasi Berkas
                </div>
                <div class="card-body bg-white rounded-bottom">
                    <form action="{{ route('admin.petugas.validasi.proses', $pengajuan->id) }}" method="POST" id="form-validation">
                        @csrf
                        <div class="mb-4">
                            <label class="form-label fw-bold">Keputusan Validasi Berkas <span class="text-danger">*</span></label>
                            <div class="d-flex gap-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="keputusan" id="keputusan_valid" value="Diverifikasi" required>
                                    <label class="form-check-label fw-semibold text-success" for="keputusan_valid">
                                        <i class="bi bi-check-circle-fill me-1"></i> Valid (Diverifikasi)
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="keputusan" id="keputusan_tidak_valid" value="Ditolak" required>
                                    <label class="form-check-label fw-semibold text-danger" for="keputusan_tidak_valid">
                                        <i class="bi bi-x-circle-fill me-1"></i> Tidak Valid (Ditolak)
                                    </label>
                                </div>
                            </div>
                        </div>

                        {{-- Input Alasan Penolakan, wajib jika keputusan "Tidak Valid" --}}
                        <div class="mb-4" id="div-alasan" style="display: none;">
                            <label for="alasan_penolakan" class="form-label fw-bold text-danger">Alasan Penolakan / Berkas Tidak Valid <span class="text-danger">*</span></label>
                            <textarea name="alasan_penolakan" id="alasan_penolakan" class="form-control" rows="4" placeholder="Tuliskan detail berkas yang tidak lengkap, buram, atau tidak sesuai..."></textarea>
                            <div class="form-text text-muted">Contoh: "Foto KTP buram dan tidak terbaca jelas", "SKTM sudah kadaluarsa".</div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <button type="reset" class="btn btn-light" id="btn-reset">Reset</button>
                            <button type="submit" class="btn btn-primary" onclick="return confirm('Apakah Anda yakin keputusan validasi ini sudah benar?')">
                                <i class="bi bi-save me-1"></i> Simpan Keputusan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            @else
            {{-- Log Validasi --}}
            <div class="card shadow-sm border-0 rounded-3 mb-4">
                <div class="card-header fw-bold text-white border-0" style="background: #334155;">
                    <i class="bi bi-journal-text me-2"></i> Log Pemeriksaan Berkas
                </div>
                <div class="card-body bg-white rounded-bottom">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <th width="200">Pemeriksa (Petugas)</th>
                            <td>: {{ $pengajuan->validator->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Waktu Validasi</th>
                            <td>: {{ $pengajuan->validated_at ? $pengajuan->validated_at->format('d F Y H:i') : '-' }}</td>
                        </tr>
                        @if($pengajuan->status === 'Ditolak')
                        <tr>
                            <th class="text-danger">Alasan Penolakan</th>
                            <td class="text-danger">: {{ $pengajuan->alasan_penolakan }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>
            @endif
        </div>

        {{-- Uploaded Dokumen --}}
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header fw-bold text-white border-0" style="background: #1E3A5F;">
                    <i class="bi bi-files me-2"></i> Berkas Pendukung (Foto Dokumen)
                </div>
                <div class="card-body bg-light rounded-bottom">
                    @foreach(['foto_ktp' => 'Foto KTP', 'foto_kk' => 'Foto Kartu Keluarga (KK)', 'foto_sktm' => 'Foto SKTM', 'foto_rumah' => 'Foto Rumah Depan'] as $field => $label)
                    <div class="mb-4 bg-white p-3 rounded shadow-sm border border-light">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-bold text-secondary">{{ $label }}</span>
                            @if($pengajuan->$field)
                            <a href="{{ asset('storage/' . $pengajuan->$field) }}" target="_blank" class="btn btn-xs btn-outline-primary py-0 px-2" style="font-size: 0.75rem;">
                                <i class="bi bi-fullscreen"></i> Perbesar
                            </a>
                            @endif
                        </div>
                        @if($pengajuan->$field)
                            <div class="text-center">
                                <a href="{{ asset('storage/' . $pengajuan->$field) }}" target="_blank">
                                    <img src="{{ asset('storage/' . $pengajuan->$field) }}"
                                         class="img-fluid rounded border hover-shadow" style="max-height:220px; object-fit:contain; cursor:pointer;" alt="{{ $label }}">
                                </a>
                            </div>
                        @else
                            <div class="alert alert-secondary text-center py-4 mb-0">
                                <i class="bi bi-file-earmark-x fs-1 text-muted"></i>
                                <div class="mt-2 text-muted">Berkas tidak diunggah</div>
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
    document.addEventListener("DOMContentLoaded", function() {
        const radValid = document.getElementById('keputusan_valid');
        const radTidakValid = document.getElementById('keputusan_tidak_valid');
        const divAlasan = document.getElementById('div-alasan');
        const txtAlasan = document.getElementById('alasan_penolakan');
        const btnReset = document.getElementById('btn-reset');

        function toggleAlasan() {
            if (radTidakValid.checked) {
                divAlasan.style.display = 'block';
                txtAlasan.setAttribute('required', 'required');
            } else {
                divAlasan.style.display = 'none';
                txtAlasan.removeAttribute('required');
                txtAlasan.value = '';
            }
        }

        if (radValid && radTidakValid) {
            radValid.addEventListener('change', toggleAlasan);
            radTidakValid.addEventListener('change', toggleAlasan);
        }

        if (btnReset) {
            btnReset.addEventListener('click', function() {
                setTimeout(toggleAlasan, 50);
            });
        }
    });
</script>
@endpush
@endsection
