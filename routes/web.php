<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\Auth\UserAuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\User\DashboardController as UserDashboardController;
use App\Http\Controllers\Admin\AkunMasyarakatController;
use App\Http\Controllers\Admin\BantuanSosialController;
use App\Http\Controllers\Admin\PengajuanController;
use App\Http\Controllers\Admin\KriteriaController;
use App\Http\Controllers\Admin\SubKriteriaController;
use App\Http\Controllers\Admin\PenilaianController;
use App\Http\Controllers\Admin\HasilAkhirController;
use App\Http\Controllers\User\PengajuanController as UserPengajuanController;
use App\Http\Controllers\User\HasilAkhirController as UserHasilAkhirController;
use App\Http\Controllers\User\ProfileController; // ← TAMBAHAN
use App\Http\Controllers\Petugas\ValidasiController;
use App\Http\Controllers\Lurah\PersetujuanController;
use App\Http\Controllers\Petugas\PenyaluranController;
use App\Http\Controllers\User\StatusBantuanController;
use App\Http\Controllers\Petugas\MonitoringController;

// ── Home ──
Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| USER / WARGA ROUTES
|--------------------------------------------------------------------------
*/
Route::prefix('user')->name('user.')->group(function () {

    Route::middleware('guest:web')->group(function () {
        Route::get('/login',    [UserAuthController::class, 'showLogin'])->name('login');
        Route::post('/login',   [UserAuthController::class, 'login'])->name('login.post');
        Route::get('/register', [UserAuthController::class, 'showRegister'])->name('register');
        Route::post('/register',[UserAuthController::class, 'register'])->name('register.post');
    });

    Route::middleware('auth:web')->group(function () {
        Route::post('/logout',   [UserAuthController::class, 'logout'])->name('logout');
        Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('dashboard');

        /*
        |----------------------------------------------------------------------
        | PENGAJUAN USER
        |----------------------------------------------------------------------
        */
        Route::get('/pengajuan',                         [UserPengajuanController::class, 'index'])->name('pengajuan.index');
        Route::get('/pengajuan/create/{bantuan_sosial}', [UserPengajuanController::class, 'create'])->name('pengajuan.create');
        Route::post('/pengajuan/store',                  [UserPengajuanController::class, 'store'])->name('pengajuan.store');
        Route::get('/pengajuan/success/{pengajuan}',     [UserPengajuanController::class, 'success'])->name('pengajuan.success');
        Route::get('/pengajuan/{pengajuan}',             [UserPengajuanController::class, 'show'])->name('pengajuan.show');

        /*
        |----------------------------------------------------------------------
        | HASIL AKHIR USER
        |----------------------------------------------------------------------
        */
        Route::get('/hasilakhir', [UserHasilAkhirController::class, 'index'])->name('hasilakhir.index');

        /*
        |----------------------------------------------------------------------
        | STATUS BANTUAN USER
        |----------------------------------------------------------------------
        */
        Route::get('/statusbantuan', [StatusBantuanController::class, 'index'])->name('statusbantuan.index');
        Route::post('/statusbantuan/{penyaluran}/evaluasi', [StatusBantuanController::class, 'storeEvaluasi'])->name('statusbantuan.evaluasi');

        /*
        |----------------------------------------------------------------------
        | PROFIL USER
        | Catatan: route statis (update-password) di atas {id} agar tidak konflik
        |----------------------------------------------------------------------
        */
        Route::get('/profile',                  [ProfileController::class, 'index'])->name('profile.index');
        Route::put('/profile',                  [ProfileController::class, 'update'])->name('profile.update');
        Route::put('/profile/update-password',  [ProfileController::class, 'updatePassword'])->name('profile.update-password');
    });
});

/*
|--------------------------------------------------------------------------
| ADMIN ROUTES
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->group(function () {

    Route::get('/login',  [AdminAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('login.post');

    Route::middleware('auth.admin')->group(function () {

        Route::post('/logout',   [AdminAuthController::class, 'logout'])->name('logout');
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        /*
        |----------------------------------------------------------------------
        | ADMIN ONLY ACCESS
        |----------------------------------------------------------------------
        */
        Route::middleware('role:admin')->group(function () {
            // Akun Warga
            Route::resource('akunmasyarakat', AkunMasyarakatController::class);
            // Bantuan Sosial
            Route::resource('bantuansosial', BantuanSosialController::class);
            // Pengajuan (Semua Data)
            Route::get('/pengajuan/export/excel/{bantuan_sosial_id?}', [PengajuanController::class, 'exportExcel'])->name('pengajuan.export.excel');
            Route::get('/pengajuan/export/pdf/{bantuan_sosial_id?}',   [PengajuanController::class, 'exportPdf'])->name('pengajuan.export.pdf');
            Route::resource('pengajuan', PengajuanController::class);
            // Sub Kriteria
            Route::get('/subkriteria',                    [SubKriteriaController::class, 'index'])->name('subkriteria.index');
            Route::get('/subkriteria/create',             [SubKriteriaController::class, 'create'])->name('subkriteria.create');
            Route::post('/subkriteria',                   [SubKriteriaController::class, 'store'])->name('subkriteria.store');
            Route::get('/subkriteria/{subkriteria}',      [SubKriteriaController::class, 'show'])->name('subkriteria.show');
            Route::get('/subkriteria/{subkriteria}/edit', [SubKriteriaController::class, 'edit'])->name('subkriteria.edit');
            Route::put('/subkriteria/{subkriteria}',      [SubKriteriaController::class, 'update'])->name('subkriteria.update');
            Route::delete('/subkriteria/{subkriteria}',   [SubKriteriaController::class, 'destroy'])->name('subkriteria.destroy');
            // Kriteria & AHP
            Route::get('/kriteria',               [KriteriaController::class, 'index'])->name('kriteria.index');
            Route::post('/kriteria',              [KriteriaController::class, 'store'])->name('kriteria.store');
            Route::post('/kriteria/perbandingan', [KriteriaController::class, 'simpanPerbandingan'])->name('kriteria.perbandingan');
            Route::post('/kriteria/hitung-ahp',   [KriteriaController::class, 'hitungAhp'])->name('kriteria.hitung');
            Route::put('/kriteria/{kriteria}',    [KriteriaController::class, 'update'])->name('kriteria.update');
            Route::delete('/kriteria/{kriteria}', [KriteriaController::class, 'destroy'])->name('kriteria.destroy');
            // Penilaian & MOORA
            Route::get('/penilaian',                  [PenilaianController::class, 'index'])->name('penilaian.index');
            Route::get('/penilaian/create',           [PenilaianController::class, 'create'])->name('penilaian.create');
            Route::post('/penilaian',                 [PenilaianController::class, 'store'])->name('penilaian.store');
            Route::post('/penilaian/hitung-moora',    [PenilaianController::class, 'hitungMoora'])->name('penilaian.hitung');
            Route::get('/penilaian/{pengajuan}/edit', [PenilaianController::class, 'edit'])->name('penilaian.edit');
            // Hasil Akhir Admin
            Route::get('/hasilakhir',             [HasilAkhirController::class, 'index'])->name('hasilakhir.index');
            Route::get('/hasilakhir/export-excel',[HasilAkhirController::class, 'exportExcel'])->name('hasilakhir.export-excel');
            Route::get('/hasilakhir/export-pdf',  [HasilAkhirController::class, 'exportPdf'])->name('hasilakhir.export-pdf');
        });

        /*
        |----------------------------------------------------------------------
        | PETUGAS ONLY ACCESS
        |----------------------------------------------------------------------
        */
        Route::middleware('role:petugas')->prefix('petugas')->name('petugas.')->group(function () {
            // Dashboard Petugas
            Route::get('/dashboard',             [DashboardController::class, 'index'])->name('dashboard');
            // Validasi berkas menggunakan controller pengajuan yang ada
            Route::get('/validasi',              [ValidasiController::class, 'index'])->name('validasi.index');
            // Detail pengajuan — lihat berkas
            Route::get('/validasi/{pengajuan}',  [ValidasiController::class, 'show'])->name('validasi.show');
            // Submit keputusan validasi
            Route::post('/validasi/{pengajuan}', [ValidasiController::class, 'validasi'])->name('validasi.proses');

            // Penjadwalan Penyaluran
            Route::get('/penyaluran',                               [PenyaluranController::class, 'index'])->name('penyaluran.index');
            Route::get('/penyaluran/{penyaluran}/edit',             [PenyaluranController::class, 'edit'])->name('penyaluran.edit');
            Route::put('/penyaluran/{penyaluran}',                  [PenyaluranController::class, 'update'])->name('penyaluran.update');
            
            // Konfirmasi Pengambilan Bantuan
            Route::get('/penyaluran/{penyaluran}/konfirmasi',       [PenyaluranController::class, 'showKonfirmasi'])->name('penyaluran.konfirmasi.show');
            Route::post('/penyaluran/{penyaluran}/konfirmasi',      [PenyaluranController::class, 'konfirmasi'])->name('penyaluran.konfirmasi');

            // Monitoring Penyaluran
            Route::get('/monitoring',                               [MonitoringController::class, 'index'])->name('monitoring.index');
            Route::get('/monitoring/{penyaluran}/buat',             [MonitoringController::class, 'create'])->name('monitoring.create');
        });

        /*
        |----------------------------------------------------------------------
        | LURAH ONLY ACCESS
        |----------------------------------------------------------------------
        */
        Route::middleware('role:lurah')->prefix('lurah')->name('lurah.')->group(function () {
            // Dashboard Lurah
            Route::get('/dashboard',                            [DashboardController::class, 'index'])->name('dashboard');
            // Daftar calon penerima & status persetujuan
            Route::get('/persetujuan',                          [PersetujuanController::class, 'index'])->name('persetujuan.index');
            // Detail calon penerima
            Route::get('/persetujuan/{hasilAkhir}',             [PersetujuanController::class, 'show'])->name('persetujuan.show');
            // Setujui calon penerima
            Route::post('/persetujuan/{hasilAkhir}/setujui',    [PersetujuanController::class, 'setujui'])->name('persetujuan.setujui');
            // Tolak calon penerima (wajib alasan)
            Route::post('/persetujuan/{hasilAkhir}/tolak',      [PersetujuanController::class, 'tolak'])->name('persetujuan.tolak');
            // Melihat ranking hasil MOORA (read-only)
            Route::get('/hasilakhir',                           [HasilAkhirController::class, 'index'])->name('hasilakhir.index');
            // Monitoring Penyaluran Lurah (Hanya Melihat)
            Route::get('/monitoring',                           [MonitoringController::class, 'index'])->name('monitoring.index');
            Route::get('/monitoring/{penyaluran}/buat',         [MonitoringController::class, 'create'])->name('monitoring.create');
        });

    });
});