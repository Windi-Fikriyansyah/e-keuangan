<?php

use App\Http\Controllers\KelolaAkses\PermissionController;
use App\Http\Controllers\KelolaAkses\RoleController;
use App\Http\Controllers\KelolaAkses\UserController;
use App\Http\Controllers\KelolaData\BPKBController;
use App\Http\Controllers\KelolaData\SertifikatController;
use App\Http\Controllers\Laporan\BPKBController as LaporanBPKBController;
use App\Http\Controllers\Laporan\SertifikatController as LaporanSertifikatController;
use App\Http\Controllers\Peminjaman\BPKBController as PeminjamanBPKBController;
use App\Http\Controllers\Peminjaman\SertifikatController as PeminjamanSertifikatController;
use App\Http\Controllers\Penyerahan\BPKBController as PenyerahanBPKBController;
use App\Http\Controllers\Penyerahan\SertifikatController as PenyerahanSertifikatController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // Permission
    Route::resource('akses', PermissionController::class);
    Route::post('akses/load', [PermissionController::class, 'load'])->name('akses.load');

    // Role
    Route::resource('peran', RoleController::class);
    Route::post('peran/load', [RoleController::class, 'load'])->name('peran.load');

    // User
    Route::resource('user', UserController::class);
    Route::post('user/load', [UserController::class, 'load'])->name('user.load');

    // Kelola Data
    Route::prefix('kelola_data')->as('kelola_data.')->group(function () {
        // BPKB
        Route::prefix('bpkb')->as('bpkb.')
            ->group(function () {
                Route::get('', [BPKBController::class, 'index'])->name('index');
                Route::post('load', [BPKBController::class, 'load'])->name('load');
                Route::get('create', [BPKBController::class, 'create'])->name('create');
                Route::post('store', [BPKBController::class, 'store'])->name('store');
                Route::get('edit/{no_register}/{kd_skpd}', [BPKBController::class, 'edit'])->name('edit');
                Route::post('update/{id}', [BPKBController::class, 'update'])->name('update');
                Route::post('delete', [BPKBController::class, 'delete'])->name('delete');
            });

        // SERTIFIKAT
        Route::prefix('sertifikat')->as('sertifikat.')
            ->group(function () {
                Route::get('', [SertifikatController::class, 'index'])->name('index');
                Route::post('load', [SertifikatController::class, 'load'])->name('load');
                Route::get('create', [SertifikatController::class, 'create'])->name('create');
                Route::post('store', [SertifikatController::class, 'store'])->name('store');
                Route::get('edit/{no_register}/{kd_skpd}', [SertifikatController::class, 'edit'])->name('edit');
                Route::post('update', [SertifikatController::class, 'update'])->name('update');
                Route::post('delete', [SertifikatController::class, 'delete'])->name('delete');
            });
    });

    // PEMINJAMAN
    Route::prefix('peminjaman')->as('peminjaman.')->group(function () {
        // BPKB
        Route::prefix('bpkb')->as('bpkb.')
            ->group(function () {
                Route::get('', [PeminjamanBPKBController::class, 'index'])->name('index');
                Route::post('load', [PeminjamanBPKBController::class, 'load'])->name('load');
                Route::get('create', [PeminjamanBPKBController::class, 'create'])->name('create');
                Route::post('store', [PeminjamanBPKBController::class, 'store'])->name('store');
                Route::get('edit/{no_surat}/{kd_skpd}', [PeminjamanBPKBController::class, 'edit'])->name('edit');
                Route::post('update/{id}', [PeminjamanBPKBController::class, 'update'])->name('update');
                Route::post('delete', [PeminjamanBPKBController::class, 'delete'])->name('delete');

                Route::post('load_bpkb', [PeminjamanBPKBController::class, 'loadBpkb'])->name('load_bpkb');
                Route::get('cetak', [PeminjamanBPKBController::class, 'cetakPeminjaman'])->name('cetak');
                Route::post('pengajuan', [PeminjamanBPKBController::class, 'pengajuanPeminjaman'])->name('pengajuan');
            });

        // SERTIFIKAT
        Route::prefix('sertifikat')->as('sertifikat.')
            ->group(function () {
                Route::get('', [PeminjamanSertifikatController::class, 'index'])->name('index');
                Route::post('load', [PeminjamanSertifikatController::class, 'load'])->name('load');
                Route::get('create', [PeminjamanSertifikatController::class, 'create'])->name('create');
                Route::post('store', [PeminjamanSertifikatController::class, 'store'])->name('store');
                Route::get('edit/{no_register}/{kd_skpd}', [PeminjamanSertifikatController::class, 'edit'])->name('edit');
                Route::post('update', [PeminjamanSertifikatController::class, 'update'])->name('update');
                Route::post('delete', [PeminjamanSertifikatController::class, 'delete'])->name('delete');
            });
    });

    // PENYERAHAN
    Route::prefix('penyerahan')->as('penyerahan.')->group(function () {
        // BPKB
        Route::prefix('bpkb')->as('bpkb.')
            ->group(function () {
                Route::get('', [PenyerahanBPKBController::class, 'index'])->name('index');
                Route::post('load', [PenyerahanBPKBController::class, 'load'])->name('load');
                Route::get('create', [PenyerahanBPKBController::class, 'create'])->name('create');
                Route::post('store', [PenyerahanBPKBController::class, 'store'])->name('store');
                Route::get('edit/{no_register}/{kd_skpd}', [PenyerahanBPKBController::class, 'edit'])->name('edit');
                Route::post('update', [PenyerahanBPKBController::class, 'update'])->name('update');
                Route::post('delete', [PenyerahanBPKBController::class, 'delete'])->name('delete');
            });

        // SERTIFIKAT
        Route::prefix('sertifikat')->as('sertifikat.')
            ->group(function () {
                Route::get('', [PenyerahanSertifikatController::class, 'index'])->name('index');
                Route::post('load', [PenyerahanSertifikatController::class, 'load'])->name('load');
                Route::get('create', [PenyerahanSertifikatController::class, 'create'])->name('create');
                Route::post('store', [PenyerahanSertifikatController::class, 'store'])->name('store');
                Route::get('edit/{no_register}/{kd_skpd}', [PenyerahanSertifikatController::class, 'edit'])->name('edit');
                Route::post('update', [PenyerahanSertifikatController::class, 'update'])->name('update');
                Route::post('delete', [PenyerahanSertifikatController::class, 'delete'])->name('delete');
            });
    });

    // LAPORAN
    Route::prefix('laporan')->as('laporan.')->group(function () {
        // BPKB
        Route::prefix('bpkb')->as('bpkb.')
            ->group(function () {
                Route::get('', [LaporanBPKBController::class, 'index'])->name('index');
                Route::post('tahun', [LaporanBPKBController::class, 'tahun'])->name('tahun');
                Route::post('jenis', [LaporanBPKBController::class, 'jenis'])->name('jenis');
                Route::post('merk', [LaporanBPKBController::class, 'merk'])->name('merk');
                Route::get('cetakRekapBpkb', [LaporanBPKBController::class, 'cetakRekapBpkb'])->name('cetakRekapBpkb');
                Route::get('cetakRekapPeminjaman', [LaporanBPKBController::class, 'cetakRekapPeminjaman'])->name('cetakRekapPeminjaman');
            });

        // SERTIFIKAT
        Route::prefix('sertifikat')->as('sertifikat.')
            ->group(function () {
                Route::get('', [LaporanSertifikatController::class, 'index'])->name('index');
            });
    });
});

require __DIR__ . '/auth.php';
