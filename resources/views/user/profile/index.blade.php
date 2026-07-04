@extends('user.layouts.app')

@section('title', 'Profil Akun')
@section('page-title', 'Profil Akun')
@section('breadcrumb', 'Profil')

@push('styles')
<style>
.page-card   { background:#fff; border-radius:16px; box-shadow:0 1px 4px rgba(0,0,0,.07); border:1px solid #f1f5f9; overflow:hidden; }
.card-head   { display:flex; align-items:center; justify-content:space-between; padding:.9rem 1.25rem; border-bottom:1px solid #f1f5f9; }
.card-head h6{ font-weight:700; margin:0; font-size:.88rem; color:#1e293b; }
.card-body-inner { padding:1.4rem; }
.avatar-circle {
    width:88px; height:88px; border-radius:50%;
    background: linear-gradient(135deg, #1e3a5f 0%, #2d6a4f 100%);
    display:flex; align-items:center; justify-content:center;
    font-size:2rem; font-weight:800; color:#fff;
    border:3px solid #e2e8f0; flex-shrink:0;
}
.info-row { display:flex; align-items:flex-start; padding:.6rem 0; border-bottom:1px solid #f8fafc; font-size:.83rem; }
.info-row:last-child { border-bottom:none; }
.info-key  { color:#64748b; min-width:110px; flex-shrink:0; padding-top:.05rem; }
.info-val  { color:#1e293b; font-weight:600; word-break:break-all; }
.nav-tab-profile .nav-link { border-radius:10px !important; font-size:.83rem; font-weight:500; color:#64748b; padding:.45rem 1rem; }
.nav-tab-profile .nav-link.active { background:#1e3a5f; color:#fff; }
.form-label   { font-size:.82rem; font-weight:600; color:#374151; margin-bottom:.3rem; }
.form-control { border-radius:10px; font-size:.84rem; border-color:#e2e8f0; padding:.52rem .85rem; }
.form-control:focus { border-color:#1e3a5f; box-shadow:0 0 0 3px rgba(30,58,95,.10); }
.form-control[readonly] { background:#f8fafc; color:#64748b; cursor:not-allowed; }
.btn-save { background:#1e3a5f; color:#fff; border:none; border-radius:10px; padding:.52rem 1.5rem; font-size:.84rem; font-weight:600; transition:background .15s; }
.btn-save:hover { background:#2d6a4f; color:#fff; }
.role-badge { display:inline-block; background:#dbeafe; color:#1e40af; border-radius:20px; padding:.25rem .85rem; font-size:.72rem; font-weight:600; }
.strength-bar { height:5px; border-radius:3px; transition:width .3s,background .3s; }
</style>
@endpush

@section('content')

@if(session('success'))
<div class="alert d-flex align-items-center gap-2 rounded-3 mb-4 border-0"
     style="background:#d1fae5;color:#065f46;font-size:.84rem;">
    <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
</div>
@endif
@if($errors->any())
<div class="alert d-flex align-items-center gap-2 rounded-3 mb-4 border-0"
     style="background:#fee2e2;color:#991b1b;font-size:.84rem;">
    <i class="bi bi-exclamation-triangle-fill"></i> {{ $errors->first() }}
</div>
@endif

<div class="row g-4">

    {{-- KOLOM KIRI --}}
    <div class="col-lg-4">
        <div class="page-card">
            <div class="card-head">
                <h6><i class="bi bi-person-circle me-2 text-secondary"></i>Informasi Akun</h6>
            </div>
            <div class="card-body-inner text-center">
                <div class="avatar-circle mx-auto mb-3">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div class="fw-bold mb-1" style="font-size:1rem;color:#1e293b;">
                    {{ $user->name }}
                </div>
                <div style="font-size:.78rem;color:#64748b;margin-bottom:.75rem;">
                    {{ $user->email }}
                </div>
                <span class="role-badge">
                    <i class="bi bi-shield-check me-1"></i>
                    {{ ucfirst($user->role) }}
                </span>
                <hr class="my-3" style="border-color:#f1f5f9;">
                <div class="text-start">
                    <div class="info-row">
                        <span class="info-key"><i class="bi bi-person me-2 text-secondary"></i>Nama</span>
                        <span class="info-val">{{ $user->name }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-key"><i class="bi bi-card-text me-2 text-secondary"></i>NIK</span>
                        <span class="info-val font-monospace" style="font-size:.8rem;letter-spacing:.04em;">
                            {{ $user->nik }}
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="info-key"><i class="bi bi-envelope me-2 text-secondary"></i>Email</span>
                        <span class="info-val">{{ $user->email }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-key"><i class="bi bi-calendar me-2 text-secondary"></i>Bergabung</span>
                        <span class="info-val">{{ $user->created_at->format('d M Y') }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-key"><i class="bi bi-circle-fill me-2" style="color:#10b981;font-size:.5rem;"></i>Status</span>
                        <span class="info-val" style="color:#059669;">Aktif</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- KOLOM KANAN --}}
    <div class="col-lg-8">
        <div class="page-card">
            <div class="card-head">
                <ul class="nav nav-pills nav-tab-profile gap-1 mb-0" id="profileTabs" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active" data-bs-toggle="pill" data-bs-target="#tabEditProfil">
                            <i class="bi bi-pencil-square me-1"></i>Edit Profil
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="pill" data-bs-target="#tabPassword">
                            <i class="bi bi-lock me-1"></i>Ganti Password
                        </button>
                    </li>
                </ul>
            </div>

            <div class="tab-content card-body-inner">

                {{-- Tab Edit Profil --}}
                <div class="tab-pane fade show active" id="tabEditProfil">
                    <p style="font-size:.78rem;color:#64748b;margin-bottom:1.2rem;">
                        <i class="bi bi-info-circle me-1"></i>
                        NIK tidak dapat diubah karena merupakan identitas utama akun.
                    </p>
                    <form method="POST" action="{{ route('user.profile.update') }}">
                        @csrf
                        @method('PUT')
                        <div class="row g-3">

                            <div class="col-12">
                                <label class="form-label">
                                    NIK
                                    <span class="ms-1 badge rounded-pill"
                                          style="background:#f1f5f9;color:#64748b;font-size:.65rem;font-weight:500;">
                                        Tidak dapat diubah
                                    </span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text rounded-start-3" style="border-color:#e2e8f0;background:#f8fafc;">
                                        <i class="bi bi-card-text text-secondary"></i>
                                    </span>
                                    <input type="text" class="form-control" value="{{ $user->nik }}"
                                           readonly style="border-color:#e2e8f0;">
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label">
                                    Role / Hak Akses
                                    <span class="ms-1 badge rounded-pill"
                                          style="background:#f1f5f9;color:#64748b;font-size:.65rem;font-weight:500;">
                                        Tidak dapat diubah
                                    </span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text rounded-start-3" style="border-color:#e2e8f0;background:#f8fafc;">
                                        <i class="bi bi-shield-check text-secondary"></i>
                                    </span>
                                    <input type="text" class="form-control"
                                           value="{{ ucfirst($user->role) }}"
                                           readonly style="border-color:#e2e8f0;">
                                </div>
                                <div style="font-size:.72rem;color:#94a3b8;margin-top:.3rem;">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Role ditentukan oleh administrator dan tidak dapat diubah sendiri.
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label">
                                    Nama Lengkap <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text rounded-start-3" style="border-color:#e2e8f0;">
                                        <i class="bi bi-person text-secondary"></i>
                                    </span>
                                    <input type="text" name="name"
                                           class="form-control @error('name') is-invalid @enderror"
                                           value="{{ old('name', $user->name) }}"
                                           placeholder="Masukkan nama lengkap" maxlength="100">
                                </div>
                                @error('name')
                                <div class="text-danger mt-1" style="font-size:.75rem;">
                                    <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                                </div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label">
                                    Email <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text rounded-start-3" style="border-color:#e2e8f0;">
                                        <i class="bi bi-envelope text-secondary"></i>
                                    </span>
                                    <input type="email" name="email"
                                           class="form-control @error('email') is-invalid @enderror"
                                           value="{{ old('email', $user->email) }}"
                                           placeholder="contoh@email.com">
                                </div>
                                @error('email')
                                <div class="text-danger mt-1" style="font-size:.75rem;">
                                    <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                                </div>
                                @enderror
                            </div>

                            <div class="col-12 d-flex justify-content-end pt-1">
                                <button type="submit" class="btn btn-save">
                                    <i class="bi bi-save me-1"></i>Simpan Perubahan
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                {{-- Tab Ganti Password --}}
                <div class="tab-pane fade" id="tabPassword">
                    <p style="font-size:.78rem;color:#64748b;margin-bottom:1.2rem;">
                        <i class="bi bi-shield-lock me-1"></i>
                        Gunakan kombinasi huruf, angka, dan simbol untuk password yang lebih aman.
                    </p>
                    <form method="POST" action="{{ route('user.profile.update-password') }}">
                        @csrf
                        @method('PUT')
                        <div class="row g-3">

                            <div class="col-12">
                                <label class="form-label">
                                    Password Lama <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text rounded-start-3" style="border-color:#e2e8f0;">
                                        <i class="bi bi-lock text-secondary"></i>
                                    </span>
                                    <input type="password" name="current_password" id="oldPwd"
                                           class="form-control @error('current_password') is-invalid @enderror"
                                           placeholder="Masukkan password lama">
                                    <button class="btn btn-outline-secondary" type="button"
                                            onclick="togglePwd('oldPwd', this)"
                                            style="border-radius:0 10px 10px 0;border-color:#e2e8f0;">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                                @error('current_password')
                                <div class="text-danger mt-1" style="font-size:.75rem;">
                                    <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                                </div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">
                                    Password Baru <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text rounded-start-3" style="border-color:#e2e8f0;">
                                        <i class="bi bi-key text-secondary"></i>
                                    </span>
                                    <input type="password" name="password" id="newPwd"
                                           class="form-control @error('password') is-invalid @enderror"
                                           placeholder="Min. 8 karakter"
                                           oninput="checkStrength(this.value)">
                                    <button class="btn btn-outline-secondary" type="button"
                                            onclick="togglePwd('newPwd', this)"
                                            style="border-radius:0 10px 10px 0;border-color:#e2e8f0;">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                                <div style="margin-top:.4rem;">
                                    <div style="background:#e2e8f0;border-radius:3px;height:5px;overflow:hidden;">
                                        <div id="strengthBar" class="strength-bar" style="width:0%;background:#dc2626;"></div>
                                    </div>
                                    <div id="strengthLabel" style="font-size:.7rem;color:#94a3b8;margin-top:.2rem;"></div>
                                </div>
                                @error('password')
                                <div class="text-danger mt-1" style="font-size:.75rem;">
                                    <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                                </div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">
                                    Konfirmasi Password Baru <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text rounded-start-3" style="border-color:#e2e8f0;">
                                        <i class="bi bi-key-fill text-secondary"></i>
                                    </span>
                                    <input type="password" name="password_confirmation" id="confPwd"
                                           class="form-control"
                                           placeholder="Ulangi password baru">
                                    <button class="btn btn-outline-secondary" type="button"
                                            onclick="togglePwd('confPwd', this)"
                                            style="border-radius:0 10px 10px 0;border-color:#e2e8f0;">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="p-3 rounded-3" style="background:#f8fafc;font-size:.78rem;color:#64748b;">
                                    <strong style="color:#374151;">Tips keamanan password:</strong>
                                    <ul class="mb-0 mt-1 ps-3" style="line-height:1.8;">
                                        <li>Minimal 8 karakter</li>
                                        <li>Kombinasikan huruf besar, huruf kecil, dan angka</li>
                                        <li>Hindari menggunakan tanggal lahir atau NIK</li>
                                    </ul>
                                </div>
                            </div>

                            <div class="col-12 d-flex justify-content-end pt-1">
                                <button type="submit" class="btn btn-save">
                                    <i class="bi bi-shield-lock me-1"></i>Ubah Password
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
function togglePwd(id, btn) {
    const input = document.getElementById(id);
    const icon  = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('bi-eye', 'bi-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('bi-eye-slash', 'bi-eye');
    }
}

function checkStrength(val) {
    const bar   = document.getElementById('strengthBar');
    const label = document.getElementById('strengthLabel');
    if (!bar) return;
    let score = 0;
    if (val.length >= 8)             score++;
    if (/[A-Z]/.test(val))           score++;
    if (/[0-9]/.test(val))           score++;
    if (/[^A-Za-z0-9]/.test(val))   score++;

    const map = {
        0: { w:'0%',   c:'#dc2626', t:'' },
        1: { w:'25%',  c:'#dc2626', t:'Lemah' },
        2: { w:'50%',  c:'#d97706', t:'Cukup' },
        3: { w:'75%',  c:'#2563eb', t:'Kuat' },
        4: { w:'100%', c:'#059669', t:'Sangat Kuat' },
    };
    const m = map[score];
    bar.style.width      = m.w;
    bar.style.background = m.c;
    label.textContent    = m.t;
    label.style.color    = m.c;
}

@if($errors->hasAny(['current_password', 'password']))
document.addEventListener('DOMContentLoaded', () => {
    const el = document.querySelector('[data-bs-target="#tabPassword"]');
    if (el) bootstrap.Tab.getOrCreateInstance(el).show();
});
@endif
</script>
@endpush