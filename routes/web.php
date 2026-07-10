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

    Route::middleware('guest:admin')->group(function () {
        Route::get('/login',  [AdminAuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [AdminAuthController::class, 'login'])->name('login.post');
    });

    Route::middleware('auth.admin')->group(function () {

        Route::post('/logout',   [AdminAuthController::class, 'logout'])->name('logout');
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        /*
        |----------------------------------------------------------------------
        | AKUN MASYARAKAT
        |----------------------------------------------------------------------
        */
        Route::resource('akunmasyarakat', AkunMasyarakatController::class);

        /*
        |----------------------------------------------------------------------
        | BANTUAN SOSIAL
        |----------------------------------------------------------------------
        */
        Route::resource('bantuansosial', BantuanSosialController::class);

        /*
        |----------------------------------------------------------------------
        | PENGAJUAN ADMIN
        | Catatan: route export harus di atas resource agar tidak ketabrak
        | oleh route pengajuan/{pengajuan} (method show)
        |----------------------------------------------------------------------
        */
        Route::get('/pengajuan/export/excel/{bantuan_sosial_id?}', [PengajuanController::class, 'exportExcel'])->name('pengajuan.export.excel');
        Route::get('/pengajuan/export/pdf/{bantuan_sosial_id?}',   [PengajuanController::class, 'exportPdf'])->name('pengajuan.export.pdf');
        Route::resource('pengajuan', PengajuanController::class);

        /*
        |----------------------------------------------------------------------
        | SUB KRITERIA
        |----------------------------------------------------------------------
        */
        Route::get('/subkriteria',                    [SubKriteriaController::class, 'index'])->name('subkriteria.index');
        Route::get('/subkriteria/create',             [SubKriteriaController::class, 'create'])->name('subkriteria.create');
        Route::post('/subkriteria',                   [SubKriteriaController::class, 'store'])->name('subkriteria.store');
        Route::get('/subkriteria/{subkriteria}',      [SubKriteriaController::class, 'show'])->name('subkriteria.show');
        Route::get('/subkriteria/{subkriteria}/edit', [SubKriteriaController::class, 'edit'])->name('subkriteria.edit');
        Route::put('/subkriteria/{subkriteria}',      [SubKriteriaController::class, 'update'])->name('subkriteria.update');
        Route::delete('/subkriteria/{subkriteria}',   [SubKriteriaController::class, 'destroy'])->name('subkriteria.destroy');

        /*
        |----------------------------------------------------------------------
        | KRITERIA & AHP
        | Catatan: route statis harus di atas {kriteria} agar tidak konflik
        |----------------------------------------------------------------------
        */
        Route::get('/kriteria',               [KriteriaController::class, 'index'])->name('kriteria.index');
        Route::post('/kriteria',              [KriteriaController::class, 'store'])->name('kriteria.store');
        Route::post('/kriteria/perbandingan', [KriteriaController::class, 'simpanPerbandingan'])->name('kriteria.perbandingan');
        Route::post('/kriteria/hitung-ahp',   [KriteriaController::class, 'hitungAhp'])->name('kriteria.hitung');
        Route::put('/kriteria/{kriteria}',    [KriteriaController::class, 'update'])->name('kriteria.update');
        Route::delete('/kriteria/{kriteria}', [KriteriaController::class, 'destroy'])->name('kriteria.destroy');

        /*
        |----------------------------------------------------------------------
        | PENILAIAN & MOORA
        | Catatan: route statis harus di atas {pengajuan} agar tidak konflik
        |----------------------------------------------------------------------
        */
        Route::get('/penilaian',                  [PenilaianController::class, 'index'])->name('penilaian.index');
        Route::get('/penilaian/create',           [PenilaianController::class, 'create'])->name('penilaian.create');
        Route::post('/penilaian',                 [PenilaianController::class, 'store'])->name('penilaian.store');
        Route::post('/penilaian/hitung-moora',    [PenilaianController::class, 'hitungMoora'])->name('penilaian.hitung');
        Route::get('/penilaian/{pengajuan}/edit', [PenilaianController::class, 'edit'])->name('penilaian.edit');

        /*
        |----------------------------------------------------------------------
        | HASIL AKHIR ADMIN
        | Catatan: route statis export harus di atas {id} agar tidak konflik
        |----------------------------------------------------------------------
        */
        Route::get('/hasilakhir',             [HasilAkhirController::class, 'index'])->name('hasilakhir.index');
        Route::get('/hasilakhir/export-excel',[HasilAkhirController::class, 'exportExcel'])->name('hasilakhir.export-excel');
        Route::get('/hasilakhir/export-pdf',  [HasilAkhirController::class, 'exportPdf'])->name('hasilakhir.export-pdf');

    });
});