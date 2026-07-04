@extends('admin.layouts.app')

@section('title', 'Edit Pengajuan')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('admin.pengajuan.index') }}" class="btn btn-outline-secondary me-3">
            <i class="bi bi-arrow-left"></i>
        </a>
        <h4 class="fw-bold mb-0"><i class="bi bi-pencil-square me-2"></i>Edit Pengajuan</h4>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.pengajuan.update', $pengajuan) }}" method="POST" enctype="multipart/form-data">
                @csrf @method('PUT')

                {{-- Data Pemohon --}}
                <h6 class="fw-bold text-primary border-bottom pb-2 mb-3">Data Pemohon</h6>
                <div class="row g-3 mb-3">

                    {{-- Dropdown pilih masyarakat --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Masyarakat <span class="text-danger">*</span></label>
                        <select name="user_id" id="user_select"
                                class="form-select @error('user_id') is-invalid @enderror"
                                required>
                            <option value="">-- Pilih Masyarakat --</option>
                            @foreach($users as $u)
                                <option value="{{ $u->users_id }}"
                                    data-nama="{{ $u->name }}"
                                    data-nik="{{ $u->nik }}"
                                    {{ old('user_id', $pengajuan->user_id) == $u->users_id ? 'selected' : '' }}>
                                    {{ $u->name }} — {{ $u->nik }}
                                </option>
                            @endforeach
                        </select>
                        @error('user_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Bantuan Sosial <span class="text-danger">*</span></label>
                        <select name="bantuan_sosial_id" class="form-select @error('bantuan_sosial_id') is-invalid @enderror" required>
                            @foreach($bantuans as $b)
                                <option value="{{ $b->id }}"
                                    {{ old('bantuan_sosial_id', $pengajuan->bantuan_sosial_id) == $b->id ? 'selected' : '' }}>
                                    {{ $b->nama_bantuan }}
                                </option>
                            @endforeach
                        </select>
                        @error('bantuan_sosial_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    {{-- ✅ Status dipindah sebelum nama agar mudah ditemukan --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                        <select name="status" id="status_select"
                                class="form-select @error('status') is-invalid @enderror" required>
                            @foreach(['Menunggu','Diverifikasi','Ditolak','Diterima'] as $s)
                                <option value="{{ $s }}"
                                    {{ old('status', $pengajuan->status) == $s ? 'selected' : '' }}>
                                    {{ $s }}
                                </option>
                            @endforeach
                        </select>
                        @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="nama" id="input_nama"
                               class="form-control @error('nama') is-invalid @enderror"
                               value="{{ old('nama', $pengajuan->nama) }}">
                        @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">NIK <span class="text-danger">*</span></label>
                        <input type="text" name="nik" id="input_nik"
                               class="form-control @error('nik') is-invalid @enderror"
                               value="{{ old('nik', $pengajuan->nik) }}" maxlength="16">
                        @error('nik')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">No. Telepon <span class="text-danger">*</span></label>
                        <input type="text" name="no_telepon"
                               class="form-control @error('no_telepon') is-invalid @enderror"
                               value="{{ old('no_telepon', $pengajuan->no_telepon) }}">
                        @error('no_telepon')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Jenis Kelamin <span class="text-danger">*</span></label>
                        <select name="jenis_kelamin" class="form-select @error('jenis_kelamin') is-invalid @enderror" required>
                            <option value="L" {{ old('jenis_kelamin', $pengajuan->jenis_kelamin) == 'L' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="P" {{ old('jenis_kelamin', $pengajuan->jenis_kelamin) == 'P' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                        @error('jenis_kelamin')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Tanggal Lahir <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_lahir"
                               class="form-control @error('tanggal_lahir') is-invalid @enderror"
                               value="{{ old('tanggal_lahir', $pengajuan->tanggal_lahir?->format('Y-m-d')) }}">
                        @error('tanggal_lahir')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Pendidikan <span class="text-danger">*</span></label>
                        <select name="pendidikan" class="form-select @error('pendidikan') is-invalid @enderror" required>
                            @foreach(['Tidak Sekolah','SD','SMP','SMA/SMK','Diploma','S1','S2','S3'] as $p)
                                <option value="{{ $p }}"
                                    {{ old('pendidikan', $pengajuan->pendidikan) == $p ? 'selected' : '' }}>
                                    {{ $p }}
                                </option>
                            @endforeach
                        </select>
                        @error('pendidikan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold">Alamat <span class="text-danger">*</span></label>
                        <textarea name="alamat" rows="3"
                                  class="form-control @error('alamat') is-invalid @enderror">{{ old('alamat', $pengajuan->alamat) }}</textarea>
                        @error('alamat')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                {{-- Data Ekonomi --}}
                <h6 class="fw-bold text-primary border-bottom pb-2 mb-3">Data Ekonomi</h6>
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Penghasilan per Bulan</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            {{-- ✅ value pakai intval agar tidak ada desimal saat ditampilkan di input --}}
                            <input type="number" name="penghasilan" id="input_penghasilan"
                                   class="form-control @error('penghasilan') is-invalid @enderror"
                                   value="{{ old('penghasilan', $pengajuan->penghasilan !== null ? (int) $pengajuan->penghasilan : '') }}"
                                   min="0" step="1" placeholder="0">
                            @error('penghasilan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        {{-- ✅ Preview format ribuan di bawah input --}}
                        <small class="text-muted" id="preview_penghasilan">
                            @if($pengajuan->penghasilan !== null)
                                Rp {{ number_format((int) $pengajuan->penghasilan, 0, ',', '.') }}
                            @endif
                        </small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Jumlah Tanggungan</label>
                        <input type="number" name="jumlah_tanggungan"
                               class="form-control @error('jumlah_tanggungan') is-invalid @enderror"
                               value="{{ old('jumlah_tanggungan', $pengajuan->jumlah_tanggungan) }}" min="0">
                        @error('jumlah_tanggungan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Pekerjaan</label>
                        <input type="text" name="pekerjaan"
                               class="form-control @error('pekerjaan') is-invalid @enderror"
                               value="{{ old('pekerjaan', $pengajuan->pekerjaan) }}" maxlength="100">
                        @error('pekerjaan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Kepemilikan Rumah</label>
                        <select name="kepemilikan_rumah"
                                class="form-select @error('kepemilikan_rumah') is-invalid @enderror">
                            <option value="">-- Pilih --</option>
                            @foreach(['Milik Sendiri','Sewa/Kontrak','Menumpang','Tidak Memiliki'] as $kr)
                                <option value="{{ $kr }}"
                                    {{ old('kepemilikan_rumah', $pengajuan->kepemilikan_rumah) == $kr ? 'selected' : '' }}>
                                    {{ $kr }}
                                </option>
                            @endforeach
                        </select>
                        @error('kepemilikan_rumah')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                {{-- ✅ Alasan Penolakan: tampil otomatis saat status = Ditolak --}}
                <div class="mb-3" id="alasan-penolakan-section"
                     style="{{ old('status', $pengajuan->status) == 'Ditolak' ? '' : 'display:none;' }}">
                    <h6 class="fw-bold text-danger border-bottom pb-2 mb-3">Alasan Penolakan</h6>
                    <textarea name="alasan_penolakan" rows="3"
                              class="form-control @error('alasan_penolakan') is-invalid @enderror"
                              placeholder="Isi alasan penolakan pengajuan...">{{ old('alasan_penolakan', $pengajuan->alasan_penolakan) }}</textarea>
                    @error('alasan_penolakan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Upload Dokumen --}}
                <h6 class="fw-bold text-primary border-bottom pb-2 mb-3">
                    Upload Dokumen
                    <small class="text-muted fw-normal">(kosongkan jika tidak diganti)</small>
                </h6>
                <div class="row g-3 mb-4">
                    @foreach(['foto_ktp' => 'Foto KTP', 'foto_kk' => 'Foto KK', 'foto_sktm' => 'Foto SKTM', 'foto_rumah' => 'Foto Rumah'] as $field => $label)
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">{{ $label }}</label>
                        @if($pengajuan->$field)
                            <div class="mb-1">
                                <img src="{{ asset('storage/' . $pengajuan->$field) }}"
                                     class="img-thumbnail" style="height:80px;">
                            </div>
                        @endif
                        <input type="file" name="{{ $field }}" accept="image/*"
                               class="form-control @error($field) is-invalid @enderror">
                        @error($field)<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    @endforeach
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i>Update
                    </button>
                    <a href="{{ route('admin.pengajuan.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // ✅ Toggle alasan penolakan
    const statusSelect  = document.getElementById('status_select');
    const alasanSection = document.getElementById('alasan-penolakan-section');

    statusSelect.addEventListener('change', function () {
        alasanSection.style.display = this.value === 'Ditolak' ? '' : 'none';
    });

    // ✅ Auto-fill nama & NIK saat ganti masyarakat
    const userSelect = document.getElementById('user_select');
    if (userSelect) {
        userSelect.addEventListener('change', function () {
            const selected = this.options[this.selectedIndex];
            const inputNama = document.getElementById('input_nama');
            const inputNik  = document.getElementById('input_nik');
            if (inputNama) inputNama.value = selected.dataset.nama ?? '';
            if (inputNik)  inputNik.value  = selected.dataset.nik  ?? '';
        });
    }

    // ✅ Preview format ribuan di bawah input penghasilan
    const inputPenghasilan  = document.getElementById('input_penghasilan');
    const previewPenghasilan = document.getElementById('preview_penghasilan');
    if (inputPenghasilan && previewPenghasilan) {
        inputPenghasilan.addEventListener('input', function () {
            const val = parseInt(this.value.replace(/\D/g, ''), 10);
            if (!isNaN(val) && val >= 0) {
                previewPenghasilan.textContent = 'Rp ' + val.toLocaleString('id-ID');
            } else {
                previewPenghasilan.textContent = '';
            }
        });
    }
});
</script>
@endsection