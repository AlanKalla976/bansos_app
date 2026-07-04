@extends('admin.layouts.app')

@section('title', 'Tambah Pengajuan')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('admin.pengajuan.index') }}" class="btn btn-outline-secondary me-3">
            <i class="bi bi-arrow-left"></i>
        </a>
        <h4 class="fw-bold mb-0"><i class="bi bi-plus-circle me-2"></i>Tambah Pengajuan</h4>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.pengajuan.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                {{-- Data Pemohon --}}
                <h6 class="fw-bold text-primary border-bottom pb-2 mb-3">Data Pemohon</h6>
                <div class="row g-3 mb-3">

                    {{-- ✅ PERBAIKAN: dropdown pilih masyarakat, user_id otomatis dari pilihan --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Pilih Masyarakat <span class="text-danger">*</span></label>
                        <select name="user_id" id="user_select"
                                class="form-select @error('user_id') is-invalid @enderror"
                                required>
                            <option value="">-- Pilih Masyarakat --</option>
                            @foreach($users as $u)
                                <option value="{{ $u->users_id }}"
                                        data-nama="{{ $u->name }}"
                                        data-nik="{{ $u->nik }}"
                                        {{ old('user_id') == $u->users_id ? 'selected' : '' }}>
                                    {{ $u->name }} — {{ $u->nik }}
                                </option>
                            @endforeach
                        </select>
                        @error('user_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Bantuan Sosial <span class="text-danger">*</span></label>
                        <select name="bantuan_sosial_id" class="form-select @error('bantuan_sosial_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Bantuan --</option>
                            @foreach($bantuans as $b)
                                <option value="{{ $b->id }}" {{ old('bantuan_sosial_id') == $b->id ? 'selected' : '' }}>
                                    {{ $b->nama_bantuan }}
                                </option>
                            @endforeach
                        </select>
                        @error('bantuan_sosial_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="nama" id="input_nama"
                               class="form-control @error('nama') is-invalid @enderror"
                               value="{{ old('nama') }}" placeholder="Otomatis terisi saat pilih masyarakat">
                        @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">NIK <span class="text-danger">*</span></label>
                        <input type="text" name="nik" id="input_nik"
                               class="form-control @error('nik') is-invalid @enderror"
                               value="{{ old('nik') }}" maxlength="16"
                               placeholder="Otomatis terisi saat pilih masyarakat">
                        @error('nik')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">No. Telepon <span class="text-danger">*</span></label>
                        <input type="text" name="no_telepon"
                               class="form-control @error('no_telepon') is-invalid @enderror"
                               value="{{ old('no_telepon') }}">
                        @error('no_telepon')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Jenis Kelamin <span class="text-danger">*</span></label>
                        <select name="jenis_kelamin" class="form-select @error('jenis_kelamin') is-invalid @enderror" required>
                            <option value="">-- Pilih --</option>
                            <option value="L" {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                        @error('jenis_kelamin')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Tanggal Lahir <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_lahir"
                               class="form-control @error('tanggal_lahir') is-invalid @enderror"
                               value="{{ old('tanggal_lahir') }}">
                        @error('tanggal_lahir')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Pendidikan <span class="text-danger">*</span></label>
                        <select name="pendidikan" class="form-select @error('pendidikan') is-invalid @enderror" required>
                            <option value="">-- Pilih --</option>
                            @foreach(['Tidak Sekolah','SD','SMP','SMA/SMK','Diploma/S1'] as $p)
                                <option value="{{ $p }}" {{ old('pendidikan') == $p ? 'selected' : '' }}>{{ $p }}</option>
                            @endforeach
                        </select>
                        @error('pendidikan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold">Alamat <span class="text-danger">*</span></label>
                        <textarea name="alamat" rows="3"
                                  class="form-control @error('alamat') is-invalid @enderror">{{ old('alamat') }}</textarea>
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
                            <input type="number" name="penghasilan"
                                   class="form-control @error('penghasilan') is-invalid @enderror"
                                   value="{{ old('penghasilan') }}" placeholder="0" min="0">
                            @error('penghasilan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Jumlah Tanggungan</label>
                        <input type="number" name="jumlah_tanggungan"
                               class="form-control @error('jumlah_tanggungan') is-invalid @enderror"
                               value="{{ old('jumlah_tanggungan') }}" placeholder="0" min="0">
                        @error('jumlah_tanggungan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Pekerjaan</label>
                        <input type="text" name="pekerjaan"
                               class="form-control @error('pekerjaan') is-invalid @enderror"
                               value="{{ old('pekerjaan') }}" placeholder="Pekerjaan saat ini" maxlength="100">
                        @error('pekerjaan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Kepemilikan Rumah</label>
                        <select name="kepemilikan_rumah"
                                class="form-select @error('kepemilikan_rumah') is-invalid @enderror">
                            <option value="">-- Pilih --</option>
                            @foreach(['Milik Sendiri','Sewa/Kontrak','Menumpang','Tidak Memiliki'] as $kr)
                                <option value="{{ $kr }}" {{ old('kepemilikan_rumah') == $kr ? 'selected' : '' }}>{{ $kr }}</option>
                            @endforeach
                        </select>
                        @error('kepemilikan_rumah')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                {{-- Upload Dokumen --}}
                <h6 class="fw-bold text-primary border-bottom pb-2 mb-3">Upload Dokumen</h6>
                <div class="row g-3 mb-4">
                    @foreach(['foto_ktp' => 'Foto KTP', 'foto_kk' => 'Foto KK', 'foto_sktm' => 'Foto SKTM', 'foto_rumah' => 'Foto Rumah'] as $field => $label)
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">{{ $label }} <span class="text-danger">*</span></label>
                        <input type="file" name="{{ $field }}" accept="image/*"
                               class="form-control @error($field) is-invalid @enderror">
                        @error($field)<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    @endforeach
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i>Simpan
                    </button>
                    <a href="{{ route('admin.pengajuan.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ✅ Script: otomatis isi nama & NIK saat pilih masyarakat --}}
<script>
document.getElementById('user_select').addEventListener('change', function () {
    const selected = this.options[this.selectedIndex];
    document.getElementById('input_nama').value = selected.dataset.nama ?? '';
    document.getElementById('input_nik').value  = selected.dataset.nik  ?? '';
});
</script>
@endsection