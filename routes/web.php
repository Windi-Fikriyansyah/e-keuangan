<?php

use App\Http\Controllers\KelolaAkses\PermissionController;
use App\Http\Controllers\KelolaAkses\RoleController;
use App\Http\Controllers\KelolaAkses\UserController;
use App\Http\Controllers\KelolaData\AsalUsulTanahController;
use App\Http\Controllers\KelolaData\BPKBController;
use App\Http\Controllers\KelolaData\SertifikatController;
use App\Http\Controllers\Laporan\BPKBController as LaporanBPKBController;
use App\Http\Controllers\Laporan\SertifikatController as LaporanSertifikatController;
use App\Http\Controllers\Peminjaman\BPKBController as PeminjamanBPKBController;
use App\Http\Controllers\Peminjaman\SertifikatController as PeminjamanSertifikatController;
use App\Http\Controllers\Penyerahan\BPKBController as PenyerahanBPKBController;
use App\Http\Controllers\Penyerahan\SertifikatController as PenyerahanSertifikatController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\KelolaData\SKPDController;
use App\Http\Controllers\KelolaData\MasterSertifikatController;
use App\Http\Controllers\KelolaData\MasterTtdController;
use App\Http\Controllers\VerifikasiAdmin\BPKBController as VerifikasiAdminBPKBController;
use App\Http\Controllers\VerifikasiAdmin\SertifikatController as VerifikasiAdminSertifikatController;
use App\Http\Controllers\VerifikasiOperator\BPKBController as VerifikasiOperatorBPKBController;
use App\Http\Controllers\VerifikasiOperator\SertifikatController as VerifikasiOperatorSertifikatController;
use App\Http\Controllers\VerifikasiPenyelia\BPKBController as VerifikasiPenyeliaBPKBController;
use App\Http\Controllers\VerifikasiPenyelia\SertifikatController as VerifikasiPenyeliaSertifikatController;
use App\Http\Controllers\VerifikasiBast\SertifikatController as VerifikasiBastSertifikatController;

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
        // SKPD
        Route::group(['prefix' => 'skpd'], function () {
            Route::get('', [SKPDController::class, 'index'])->name('skpd.index');
            Route::post('load', [SKPDController::class, 'load'])->name('skpd.load');
            Route::get('create', [SKPDController::class, 'create'])->name('skpd.create');
            Route::post('store', [SKPDController::class, 'store'])->name('skpd.store');
            Route::get('edit/{id}', [SKPDController::class, 'edit'])->name('skpd.edit');
            Route::put('update/{id}', [SKPDController::class, 'update'])->name('skpd.update');
            Route::delete('/{id}', [SKPDController::class, 'destroy'])->name('skpd.destroy');
        });
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
        // Route::prefix('sertifikat')->as('sertifikat.')
        //     ->group(function () {
        //         Route::get('', [SertifikatController::class, 'index'])->name('index');
        //         Route::post('load', [SertifikatController::class, 'load'])->name('load');
        //         Route::get('create', [SertifikatController::class, 'create'])->name('create');
        //         Route::post('store', [SertifikatController::class, 'store'])->name('store');
        //         Route::get('edit/{no_register}/{kd_skpd}', [SertifikatController::class, 'edit'])->name('edit');
        //         Route::post('update', [SertifikatController::class, 'update'])->name('update');
        //         Route::post('delete', [SertifikatController::class, 'delete'])->name('delete');
        //     });

        Route::group(['prefix' => 'sertifikat'], function () {
            Route::get('', [SertifikatController::class, 'index'])->name('sertifikat.index');
            Route::post('load', [SertifikatController::class, 'load'])->name('sertifikat.load');
            Route::get('create', [SertifikatController::class, 'create'])->name('sertifikat.create');
            Route::post('store', [SertifikatController::class, 'store'])->name('sertifikat.store');
            Route::get('edit/{no_register}/{kd_skpd}', [SertifikatController::class, 'edit'])->name('sertifikat.edit');
            Route::post('update/{id}', [SertifikatController::class, 'update'])->name('sertifikat.update');
            Route::delete('/{id}', [SertifikatController::class, 'destroy'])->name('sertifikat.destroy');
        });

        Route::group(['prefix' => 'master_ttd'], function () {
            Route::get('', [MasterTtdController::class, 'index'])->name('master_ttd.index');
            Route::post('load', [MasterTtdController::class, 'load'])->name('master_ttd.load');
            Route::get('create', [MasterTtdController::class, 'create'])->name('master_ttd.create');
            Route::post('', [MasterTtdController::class, 'store'])->name('master_ttd.store');
            Route::get('edit/{id}', [MasterTtdController::class, 'edit'])->name('master_ttd.edit');
            Route::put('update/{id}', [MasterTtdController::class, 'update'])->name('master_ttd.update');
            Route::delete('/{id}', [MasterTtdController::class, 'destroy'])->name('master_ttd.destroy');
        });

        Route::group(['prefix' => 'asalUsul'], function () {
            Route::get('', [AsalUsulTanahController::class, 'index'])->name('asalUsul.index');
            Route::post('load', [AsalUsulTanahController::class, 'load'])->name('asalUsul.load');
            Route::get('create', [AsalUsulTanahController::class, 'create'])->name('asalUsul.create');
            Route::post('', [AsalUsulTanahController::class, 'store'])->name('asalUsul.store');
            Route::get('edit/{id}', [AsalUsulTanahController::class, 'edit'])->name('asalUsul.edit');
            Route::put('update/{id}', [AsalUsulTanahController::class, 'update'])->name('asalUsul.update');
            Route::delete('/{id}', [AsalUsulTanahController::class, 'destroy'])->name('asalUsul.destroy');
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
                Route::get('edit/{no_surat}/{kd_skpd}', [PeminjamanSertifikatController::class, 'edit'])->name('edit');
                Route::post('update/{id}', [PeminjamanSertifikatController::class, 'update'])->name('update');
                Route::post('delete', [PeminjamanSertifikatController::class, 'delete'])->name('delete');
                Route::get('cetak', [PeminjamanSertifikatController::class, 'cetakPeminjaman'])->name('cetak');
                Route::post('load_sertifikat', [PeminjamanSertifikatController::class, 'loadSertifikat'])->name('load_sertifikat');
                Route::post('pengajuan', [PeminjamanSertifikatController::class, 'pengajuanPeminjaman'])->name('pengajuan');
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
                Route::post('tandaTangan', [LaporanBPKBController::class, 'tandaTangan'])->name('tandaTangan');
                Route::get('cetakRekapBpkb', [LaporanBPKBController::class, 'cetakRekapBpkb'])->name('cetakRekapBpkb');
                Route::get('cetakRekapPeminjaman', [LaporanBPKBController::class, 'cetakRekapPeminjaman'])->name('cetakRekapPeminjaman');
            });

        // SERTIFIKAT
        Route::prefix('sertifikat')->as('sertifikat.')
            ->group(function () {
                Route::get('', [LaporanSertifikatController::class, 'index'])->name('index');
                Route::post('balikNama', [LaporanSertifikatController::class, 'balikNama'])->name('balikNama');
                Route::post('hak', [LaporanSertifikatController::class, 'hak'])->name('hak');
                Route::post('asalUsul', [LaporanSertifikatController::class, 'asalUsul'])->name('asalUsul');
                Route::post('tandaTangan', [LaporanSertifikatController::class, 'tandaTangan'])->name('tandaTangan');
                Route::get('cetakRekapSertifikat', [LaporanSertifikatController::class, 'cetakRekapSertifikat'])->name('cetakRekapSertifikat');
                Route::get('cetakRekapPeminjaman', [LaporanSertifikatController::class, 'cetakRekapPeminjaman'])->name('cetakRekapPeminjaman');
            });
    });

    // VERIFIKASI OPERATOR
    Route::prefix('verifikasi_operator')->as('verifikasi_operator.')->group(function () {
        // BPKB
        Route::prefix('bpkb')->as('bpkb.')
            ->group(function () {
                Route::get('', [VerifikasiOperatorBPKBController::class, 'index'])->name('index');
                Route::post('load', [VerifikasiOperatorBPKBController::class, 'load'])->name('load');
                Route::post('verifikasi', [VerifikasiOperatorBPKBController::class, 'verifikasi'])->name('verifikasi');
            });

        // SERTIFIKAT
        Route::prefix('sertifikat')->as('sertifikat.')
            ->group(function () {
                Route::get('', [VerifikasiOperatorSertifikatController::class, 'index'])->name('index');
                Route::post('load', [VerifikasiOperatorSertifikatController::class, 'load'])->name('load');
                Route::post('verif', [VerifikasiOperatorSertifikatController::class, 'verif'])->name('verif');
                Route::post('verifikasi_operator', [VerifikasiOperatorSertifikatController::class, 'verifikasi_operator'])->name('verifikasi_operator');
                Route::post('batalkan', [VerifikasiOperatorSertifikatController::class, 'batalkan'])->name('batalkan');
            });
    });

    Route::prefix('verifikasi_admin')->as('verifikasi_admin.')->group(function () {
        // BPKB
        Route::prefix('bpkb')->as('bpkb.')
            ->group(function () {
                Route::get('', [VerifikasiAdminBPKBController::class, 'index'])->name('index');
                Route::post('load', [VerifikasiAdminBPKBController::class, 'load'])->name('load');
                Route::post('verifikasi', [VerifikasiAdminBPKBController::class, 'verifikasi'])->name('verifikasi');
            });

        // SERTIFIKAT
        Route::prefix('sertifikat')->as('sertifikat.')
            ->group(function () {
                Route::get('', [VerifikasiAdminSertifikatController::class, 'index'])->name('index');
                Route::post('load', [VerifikasiAdminSertifikatController::class, 'load'])->name('load');
                Route::post('verif', [VerifikasiAdminSertifikatController::class, 'verif'])->name('verif');
                Route::post('verifikasi_admin', [VerifikasiAdminSertifikatController::class, 'verifikasi_admin'])->name('verifikasi_admin');
                Route::post('batalkan', [VerifikasiAdminSertifikatController::class, 'batalkan'])->name('batalkan');
            });
    });

    Route::prefix('verifikasi_penyelia')->as('verifikasi_penyelia.')->group(function () {
        // BPKB
        Route::prefix('bpkb')->as('bpkb.')
            ->group(function () {
                Route::get('', [VerifikasiPenyeliaBPKBController::class, 'index'])->name('index');
                Route::post('load', [VerifikasiPenyeliaBPKBController::class, 'load'])->name('load');
                Route::post('verifikasi', [VerifikasiPenyeliaBPKBController::class, 'verifikasi'])->name('verifikasi');
            });

        // SERTIFIKAT
        Route::prefix('sertifikat')->as('sertifikat.')
            ->group(function () {
                Route::get('', [VerifikasiPenyeliaSertifikatController::class, 'index'])->name('index');
                Route::post('load', [VerifikasiPenyeliaSertifikatController::class, 'load'])->name('load');
                Route::post('verif', [VerifikasiPenyeliaSertifikatController::class, 'verif'])->name('verif');
                Route::post('verifikasi_penyelia', [VerifikasiPenyeliaSertifikatController::class, 'verifikasi_penyelia'])->name('verifikasi_penyelia');
                Route::post('batalkan', [VerifikasiPenyeliaSertifikatController::class, 'batalkan'])->name('batalkan');
            });
    });

    Route::prefix('verifikasi_bast')->as('verifikasi_bast.')->group(function () {
        // BPKB
        Route::prefix('bpkb')->as('bpkb.')
            ->group(function () {
                Route::get('', [VerifikasiPenyeliaBPKBController::class, 'index'])->name('index');
                Route::post('load', [VerifikasiPenyeliaBPKBController::class, 'load'])->name('load');
                Route::post('verifikasi', [VerifikasiPenyeliaBPKBController::class, 'verifikasi'])->name('verifikasi');
            });

        // SERTIFIKAT
        Route::prefix('sertifikat')->as('sertifikat.')
            ->group(function () {
                Route::get('', [VerifikasiBastSertifikatController::class, 'index'])->name('index');
                Route::post('load', [VerifikasiBastSertifikatController::class, 'load'])->name('load');
                Route::post('verif', [VerifikasiBastSertifikatController::class, 'verif'])->name('verif');
                Route::post('verifikasi_bast', [VerifikasiBastSertifikatController::class, 'verifikasi_bast'])->name('verifikasi_bast');
                Route::post('batalkan', [VerifikasiBastSertifikatController::class, 'batalkan'])->name('batalkan');
            });
    });
});

require __DIR__ . '/auth.php';
