@extends('user.layouts.app')

@section('title', 'Form Pengajuan Bantuan')
@section('page-title', 'Pengajuan Bantuan')
@section('breadcrumb', 'Pengajuan Bantuan')

@section('content')
<div class="container-fluid py-2">
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('user.pengajuan.index') }}" class="btn btn-outline-secondary rounded-circle p-2 lh-1 me-3" style="width: 40px; height: 40px;">
            <i class="bi bi-arrow-left fs-5"></i>
        </a>
        <div>
            <h4 class="page-title mb-1">Form Pengajuan Bantuan</h4>
            <p class="text-muted small mb-0">Lengkapi data Anda di bawah ini untuk mengajukan program bantuan sosial.</p>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="bg-primary p-4 text-white">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-white bg-opacity-20 rounded-3 p-3 text-white">
                            <i class="bi bi-gift fs-3"></i>
                        </div>
                        <div>
                            <span class="badge bg-white bg-opacity-20 rounded-pill mb-1">Program Dipilih</span>
                            <h5 class="fw-bold mb-0">{{ $bantuan_sosial->nama_bantuan }}</h5>
                        </div>
                    </div>
                </div>

                <div class="card-body p-4 p-md-5">
                    <form action="{{ route('user.pengajuan.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="bantuan_sosial_id" value="{{ $bantuan_sosial->id }}">

                        {{-- Section 1: Identitas --}}
                        <div class="d-flex align-items-center gap-2 mb-3 border-bottom pb-2">
                            <i class="bi bi-person-bounding-box text-primary fs-5"></i>
                            <h5 class="fw-bold text-dark mb-0">Data Identitas Pengaju</h5>
                        </div>
                        
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror"
                                       value="{{ old('nama', $user->name) }}" placeholder="Masukkan nama lengkap sesuai KTP" required>
                                @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">NIK <span class="text-danger">*</span></label>
                                <input type="text" name="nik" class="form-control @error('nik') is-invalid @enderror"
                                       value="{{ old('nik', $user->nik) }}" maxlength="16" placeholder="Masukkan 16 digit NIK" required>
                                @error('nik')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">No. Telepon <span class="text-danger">*</span></label>
                                <input type="text" name="no_telepon" class="form-control @error('no_telepon') is-invalid @enderror"
                                       value="{{ old('no_telepon') }}" placeholder="Contoh: 081234567890" required>
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
                                <input type="date" name="tanggal_lahir" class="form-control @error('tanggal_lahir') is-invalid @enderror"
                                       value="{{ old('tanggal_lahir') }}" required>
                                @error('tanggal_lahir')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Pendidikan Terakhir <span class="text-danger">*</span></label>
                                <select name="pendidikan" class="form-select @error('pendidikan') is-invalid @enderror" required>
                                    <option value="">-- Pilih --</option>
                                    @foreach(['Tidak Sekolah','SD','SMP','SMA/SMK','Diploma','S1','S2','S3'] as $p)
                                        <option value="{{ $p }}" {{ old('pendidikan') == $p ? 'selected' : '' }}>{{ $p }}</option>
                                    @endforeach
                                </select>
                                @error('pendidikan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Alamat Domisili <span class="text-danger">*</span></label>
                                <textarea name="alamat" rows="3" class="form-control @error('alamat') is-invalid @enderror" placeholder="Tuliskan alamat lengkap RT/RW, Dusun, Kelurahan, Kecamatan" required>{{ old('alamat') }}</textarea>
                                @error('alamat')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        {{-- Section 2: Data Ekonomi --}}
                        <div class="d-flex align-items-center gap-2 mb-3 border-bottom pb-2">
                            <i class="bi bi-wallet2 text-primary fs-5"></i>
                            <h5 class="fw-bold text-dark mb-0">Data Kondisi Ekonomi</h5>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Penghasilan per Bulan</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" name="penghasilan" class="form-control @error('penghasilan') is-invalid @enderror"
                                           value="{{ old('penghasilan') }}" placeholder="0" min="0">
                                    @error('penghasilan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="form-text small">Masukkan jumlah total penghasilan per bulan bersih.</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Jumlah Tanggungan</label>
                                <input type="number" name="jumlah_tanggungan" class="form-control @error('jumlah_tanggungan') is-invalid @enderror"
                                       value="{{ old('jumlah_tanggungan') }}" placeholder="0" min="0">
                                @error('jumlah_tanggungan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                <div class="form-text small">Jumlah anggota keluarga dalam KK yang ditanggung.</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Pekerjaan</label>
                                <input type="text" name="pekerjaan" class="form-control @error('pekerjaan') is-invalid @enderror"
                                       value="{{ old('pekerjaan') }}" placeholder="Contoh: Buruh, Petani, Pedagang, Tidak Bekerja" maxlength="100">
                                @error('pekerjaan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Kepemilikan Rumah</label>
                                <select name="kepemilikan_rumah" class="form-select @error('kepemilikan_rumah') is-invalid @enderror">
                                    <option value="">-- Pilih --</option>
                                    @foreach(['Milik Sendiri','Sewa/Kontrak','Menumpang','Tidak Memiliki'] as $kr)
                                        <option value="{{ $kr }}" {{ old('kepemilikan_rumah') == $kr ? 'selected' : '' }}>{{ $kr }}</option>
                                    @endforeach
                                </select>
                                @error('kepemilikan_rumah')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        {{-- Section 3: Upload Dokumen --}}
                        <div class="d-flex align-items-center gap-2 mb-3 border-bottom pb-2">
                            <i class="bi bi-file-earmark-arrow-up text-primary fs-5"></i>
                            <h5 class="fw-bold text-dark mb-0">Upload Berkas Dokumen</h5>
                        </div>

                        <div class="row g-3 mb-5">
                            @foreach([
                                'foto_ktp' => 'Foto KTP / Kartu Identitas',
                                'foto_kk' => 'Foto Kartu Keluarga (KK)',
                                'foto_sktm' => 'Foto Surat Keterangan Tidak Mampu (SKTM)',
                                'foto_rumah' => 'Foto Kondisi Rumah'
                            ] as $field => $label)
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">{{ $label }} <span class="text-danger">*</span></label>
                                <input type="file" name="{{ $field }}" accept="image/*"
                                       class="form-control @error($field) is-invalid @enderror" required>
                                @error($field)<div class="invalid-feedback">{{ $message }}</div>@enderror
                                <div class="form-text small">Hanya file gambar (jpg, jpeg, png). Maksimal 2MB.</div>
                            </div>
                            @endforeach
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm">
                                <i class="bi bi-save me-1"></i> Kirim Pengajuan
                            </button>
                            <a href="{{ route('user.pengajuan.index') }}" class="btn btn-secondary rounded-pill px-4">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection