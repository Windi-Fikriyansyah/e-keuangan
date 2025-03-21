<?php

use App\Http\Controllers\KelolaAkses\PermissionController;
use App\Http\Controllers\KelolaAkses\RoleController;
use App\Http\Controllers\KelolaAkses\UserController;
use App\Http\Controllers\transaksi\Transaksi;
use App\Http\Controllers\transaksi\Terimapot;
use App\Http\Controllers\transaksi\SetorPajak;
use App\Http\Controllers\KelolaData\Subkegiatan;
use App\Http\Controllers\KelolaData\Ms_anggaran;
use App\Http\Controllers\KelolaData\PajakController;
use App\Http\Controllers\KelolaData\PenguranganStok;
use App\Http\Controllers\Laporan\LaporanStok;
use App\Http\Controllers\Laporan\Laporan;
use App\Http\Controllers\Dashboard;
use App\Http\Controllers\Laporan\SertifikatController as LaporanSertifikatController;
use App\Exports\SalesReportExport;
use App\Http\Controllers\KelolaData\Ms_program;
use App\Http\Controllers\KelolaData\Ms_sumberdana;
use App\Http\Controllers\Transaksi\Lpj;
use App\Http\Controllers\Transaksi\Lpj_tu;
use App\Http\Controllers\Transaksi\SetorKas;
use Maatwebsite\Excel\Facades\Excel;
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
Route::get('/dashboard', [Dashboard::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');
Route::get('/dashboard-owner', [Dashboard::class, 'dashboard'])->middleware(['auth', 'verified'])->name('dashboard-owner');
Route::get('/home', function () {
    return view('home');
})->middleware(['auth', 'verified'])->name('home');

Route::middleware(['auth'])->group(function () {
    Route::prefix('transaksi')->as('transaksi.')
        ->group(function () {
        Route::get('', [Transaksi::class, 'index'])->middleware('permission:14')->name('index');
        Route::post('/load', [Transaksi::class, 'load'])->middleware('permission:14')->name('load');
        Route::get('/create', [Transaksi::class, 'create'])->middleware('permission:14')->name('create');
        Route::post('store', [Transaksi::class, 'store'])->middleware('permission:14')->name('store');
        Route::get('/get-sub-kegiatan', [Transaksi::class, 'getSubKegiatan'])->middleware('permission:14')->name('get-sub-kegiatan');
        Route::get('/get-no_sp2d', [Transaksi::class, 'getsp2d'])->middleware('permission:14')->name('get-no_sp2d');
        Route::get('/get-sumberdana', [Transaksi::class, 'getsumberdana'])->middleware('permission:14')->name('get-sumberdana');
        Route::get('/get-rekening', [Transaksi::class, 'getrekening'])->middleware('permission:14')->name('get-rekening');
        Route::delete('/trmpot/{no_bukti}', [Transaksi::class, 'destroy'])->middleware('permission:14')->name('destroy');
        Route::post('/getrealisasi', [Transaksi::class, 'getrealisasi'])->middleware('permission:14')->name('getrealisasi');
        Route::post('/get-total-nilai', [Transaksi::class, 'gettotalnilai'])->middleware('permission:14')->name('get-total-nilai');
        Route::post('/getno_transaksi', [Transaksi::class, 'getno_transaksi'])->middleware('permission:14')->name('getno_transaksi');
        Route::post('/getpotongandata', [Transaksi::class, 'getpotongandata'])->middleware('permission:14')->name('getpotongandata');

        Route::get('edit/{no_bukti}', [Transaksi::class, 'edit'])->middleware('permission:14')->name('edit');
        Route::get('/cariData', [Transaksi::class, 'cariData'])->middleware('permission:14')->name('cariData');
        Route::get('/products/search', [Transaksi::class, 'search'])->middleware('permission:14')->name('search');
        Route::get('/products/{id}', [Transaksi::class, 'getProductById'])->middleware('permission:14');
        Route::post('/save', [Transaksi::class, 'saveTransaction'])->middleware('permission:14');
        Route::get('/riwayat-transaksi', [Transaksi::class, 'showTransactionHistory'])->middleware('permission:14')->name('riwayat');
    });
    Route::prefix('trmpot')->as('trmpot.')
        ->group(function () {
        Route::get('', [Terimapot::class, 'index'])->middleware('permission:35')->name('index');
        Route::post('/load', [Terimapot::class, 'load'])->middleware('permission:35')->name('load');
        Route::get('/create', [Terimapot::class, 'create'])->middleware('permission:35')->name('create');
        Route::post('store', [Terimapot::class, 'store'])->middleware('permission:35')->name('store');
        Route::post('/get-notransaksi', [Terimapot::class, 'getNotransaksi'])->middleware('permission:35')->name('getTransactions');
        Route::post('/get-no_sp2d', [Terimapot::class, 'getsp2d'])->middleware('permission:35')->name('get-no_sp2d');
        Route::post('/getsubkegiatan', [Terimapot::class, 'getsubkegiatan'])->middleware('permission:35')->name('getsubkegiatan');
        Route::get('/get-sumberdana', [Terimapot::class, 'getsumberdana'])->middleware('permission:35')->name('get-sumberdana');
        Route::post('/get-rekening', [Terimapot::class, 'getrekening'])->middleware('permission:35')->name('get-rekening');
        Route::post('/getrekan', [Terimapot::class, 'getrekan'])->middleware('permission:35')->name('getrekan');
        Route::post('/getmspot', [Terimapot::class, 'getmspot'])->middleware('permission:35')->name('getmspot');
        Route::delete('/trmpot/{no_bukti}', [Terimapot::class, 'destroy'])->middleware('permission:35')->name('destroy');
        Route::get('edit/{no_bukti}', [Terimapot::class, 'edit'])->middleware('permission:35')->name('edit');
        Route::get('ubah/{no_bukti}', [Terimapot::class, 'ubah'])->middleware('permission:35')->name('ubah');
        Route::post('update/{no_bukti}', [Terimapot::class, 'update'])->middleware('permission:35')->name('update');
        Route::post('/getTransactionDetails', [Terimapot::class, 'getTransactionDetails'])->middleware('permission:35')->name('getTransactionDetails');

        Route::get('/cariData', [Terimapot::class, 'cariData'])->middleware('permission:35')->name('cariData');
        Route::get('/products/search', [Terimapot::class, 'search'])->middleware('permission:35')->name('search');
        Route::get('/products/{id}', [Terimapot::class, 'getProductById'])->middleware('permission:35');
        Route::post('/save', [Terimapot::class, 'saveTransaction'])->middleware('permission:35');
        Route::get('/riwayat-Terimapot', [Terimapot::class, 'showTransactionHistory'])->middleware('permission:35')->name('riwayat');
    });

    Route::prefix('strpot')->as('strpot.')
        ->group(function () {
        Route::get('', [SetorPajak::class, 'index'])->middleware('permission:36')->name('index');
        Route::post('/load', [SetorPajak::class, 'load'])->middleware('permission:36')->name('load');
        Route::get('/create', [SetorPajak::class, 'create'])->middleware('permission:36')->name('create');
        Route::post('store', [SetorPajak::class, 'store'])->middleware('permission:36')->name('store');
        Route::post('/getnoterima', [SetorPajak::class, 'getnoterima'])->middleware('permission:36')->name('getnoterima');
        Route::post('/getpotongandata', [SetorPajak::class, 'getpotongandata'])->middleware('permission:36')->name('getpotongandata');
        Route::delete('/trmpot/{no_bukti}', [SetorPajak::class, 'destroy'])->middleware('permission:36')->name('destroy');
        Route::get('edit/{no_bukti}', [SetorPajak::class, 'edit'])->middleware('permission:36')->name('edit');
        Route::post('/getTransactionDetails', [SetorPajak::class, 'getTransactionDetails'])->middleware('permission:36')->name('getTransactionDetails');
        Route::get('ubah/{no_bukti}', [SetorPajak::class, 'ubah'])->middleware('permission:36')->name('ubah');
        Route::post('update/{no_bukti}', [SetorPajak::class, 'update'])->middleware('permission:36')->name('update');

        Route::get('/cariData', [SetorPajak::class, 'cariData'])->middleware('permission:36')->name('cariData');
        Route::get('/products/search', [SetorPajak::class, 'search'])->middleware('permission:36')->name('search');
        Route::get('/products/{id}', [SetorPajak::class, 'getProductById'])->middleware('permission:36');
        Route::post('/save', [SetorPajak::class, 'saveTransaction'])->middleware('permission:36');
        Route::get('/riwayat-SetorPajak', [SetorPajak::class, 'showTransactionHistory'])->middleware('permission:36')->name('riwayat');
    });

    Route::prefix('setorkas')->as('setorkas.')
        ->group(function () {
        Route::get('', [SetorKas::class, 'index'])->middleware('permission:41')->name('index');
        Route::post('/load', [SetorKas::class, 'load'])->middleware('permission:41')->name('load');
        Route::get('/create', [SetorKas::class, 'create'])->middleware('permission:41')->name('create');
        Route::post('store', [SetorKas::class, 'store'])->middleware('permission:41')->name('store');
        Route::post('/getnosp2d', [SetorKas::class, 'getnosp2d'])->middleware('permission:41')->name('getnosp2d');
        Route::post('/getsubkegiatan', [SetorKas::class, 'getsubkegiatan'])->middleware('permission:41')->name('getsubkegiatan');
        Route::post('/getrekening', [SetorKas::class, 'getrekening'])->middleware('permission:41')->name('getrekening');

        Route::post('/getpotongandata', [SetorKas::class, 'getpotongandata'])->middleware('permission:41')->name('getpotongandata');
        Route::delete('/trmpot/{no_sts}', [SetorKas::class, 'destroy'])->middleware('permission:41')->name('destroy');
        Route::get('edit/{no_bukti}', [SetorKas::class, 'edit'])->middleware('permission:41')->name('edit');
        Route::post('update/{no_bukti}', [SetorKas::class, 'update'])->middleware('permission:41')->name('update');
        Route::post('/getTransactionDetails', [SetorKas::class, 'getTransactionDetails'])->middleware('permission:41')->name('getTransactionDetails');

        Route::get('/cariData', [SetorKas::class, 'cariData'])->middleware('permission:41')->name('cariData');
        Route::get('/products/search', [SetorKas::class, 'search'])->middleware('permission:41')->name('search');
        Route::get('/products/{id}', [SetorKas::class, 'getProductById'])->middleware('permission:41');
        Route::post('/save', [SetorKas::class, 'saveTransaction'])->middleware('permission:41');
        Route::get('/riwayat-SetorKas', [SetorKas::class, 'showTransactionHistory'])->middleware('permission:41')->name('riwayat');
    });

    Route::prefix('lpj')->as('lpj.')
        ->group(function () {
        Route::get('', [Lpj::class, 'index'])->middleware('permission:44')->name('index');
        Route::post('/load', [Lpj::class, 'load'])->middleware('permission:44')->name('load');
        Route::get('/create', [Lpj::class, 'create'])->middleware('permission:44')->name('create');
        Route::get('/lpj/get-data', [Lpj::class, 'getData'])->middleware('permission:44')->name('getData');
        Route::delete('/lpj_tabel/{id}', [Lpj::class, 'hapus_tabel'])->middleware('permission:44')->name('hapus_tabel');
        Route::post('store', [Lpj::class, 'store'])->middleware('permission:44')->name('store');
        Route::delete('/lpj/{no_lpj}', [Lpj::class, 'destroy'])->middleware('permission:44')->name('destroy');
        Route::get('edit/{no_bukti}', [Lpj::class, 'edit'])->middleware('permission:44')->name('edit');
        Route::get('ubah/{no_bukti}', [Lpj::class, 'ubah'])->middleware('permission:44')->name('ubah');
        Route::post('update/{no_bukti}', [Lpj::class, 'update'])->middleware('permission:44')->name('update');
        Route::post('/lpj/get-sp2d', [Lpj::class, 'getSp2d'])->middleware('permission:44')->name('get-sp2d');
        Route::post('/lpj/print', [Lpj::class, 'print'])->middleware('permission:44')->name('print');
        Route::post('tandaTangan', [Lpj::class, 'tandaTangan'])->name('tandaTangan');
        Route::post('tandaTanganPa', [Lpj::class, 'tandaTanganPa'])->name('tandaTanganPa');

    });

    Route::prefix('lpj_tu')->as('lpj_tu.')
    ->group(function () {
    Route::get('', [Lpj_tu::class, 'index'])->middleware('permission:45')->name('index');
    Route::post('/load', [Lpj_tu::class, 'load'])->middleware('permission:45')->name('load');
    Route::get('/create', [Lpj_tu::class, 'create'])->middleware('permission:45')->name('create');
    Route::get('/lpj_tu/get-data', [Lpj_tu::class, 'getData'])->middleware('permission:45')->name('getData');
    // Route::delete('/lpj_tu/{id}', [Lpj_tu::class, 'hapus_tabel'])->middleware('permission:45')->name('hapus_tabel');
    Route::post('store', [Lpj_tu::class, 'store'])->middleware('permission:45')->name('store');
    Route::get('edit/{no_bukti}', [Lpj_tu::class, 'edit'])->middleware('permission:45')->name('edit');
    Route::get('ubah/{no_bukti}', [Lpj_tu::class, 'ubah'])->middleware('permission:45')->name('ubah');
    Route::delete('/lpj_tu/{no_lpj}', [Lpj_tu::class, 'destroy'])->middleware('permission:45')->name('destroy');
    Route::post('/get_nosp2d', [Lpj_tu::class, 'get_nosp2d'])->middleware('permission:45')->name('get_nosp2d');

    Route::get('lpj/getDataByNoBukti', [Lpj_tu::class, 'getDataByNoBukti'])->name('getDataByNoBukti');
    Route::post('/Lpj_tu/get-sp2d', [Lpj_tu::class, 'getSp2d'])->middleware('permission:45')->name('get-sp2d');
        Route::post('/Lpj_tu/print', [Lpj_tu::class, 'print'])->middleware('permission:45')->name('print');
        Route::post('tandaTangan', [Lpj_tu::class, 'tandaTangan'])->name('tandaTangan');
        Route::post('tandaTanganPa', [Lpj_tu::class, 'tandaTanganPa'])->name('tandaTanganPa');
});
});

Route::middleware(['auth'])->group(function () {
    Route::get('/laporan-penjualan/export', [Laporan::class, 'exportSalesReport'])->name('laporan.penjualan.export');

    // Permission
    Route::resource('akses', PermissionController::class)->middleware('permission:4');
    Route::post('akses/load', [PermissionController::class, 'load'])->middleware('permission:4')->name('akses.load');

    // Role
    Route::resource('peran', RoleController::class)->middleware('permission:5');
    Route::post('peran/load', [RoleController::class, 'load'])->middleware('permission:5')->name('peran.load');

    // User
    Route::resource('user', UserController::class)->middleware('permission:6');
    Route::post('user/load', [UserController::class, 'load'])->middleware('permission:6')->name('user.load');


    // Kelola Data
    Route::prefix('kelola_data')->as('kelola_data.')->group(function () {
        // SKPD

        // BPKB
        Route::prefix('pajak')->as('pajak.')
            ->group(function () {
                Route::get('', [PajakController::class, 'index'])->middleware('permission:8')->name('index');
                Route::post('load', [PajakController::class, 'load'])->middleware('permission:8')->name('load');
                Route::get('create', [PajakController::class, 'create'])->middleware('permission:8')->name('create');
                Route::post('store', [PajakController::class, 'store'])->middleware('permission:8')->name('store');
                Route::get('edit/{id}', [PajakController::class, 'edit'])->middleware('permission:8')->name('edit');
                Route::post('update/{id}', [PajakController::class, 'update'])->middleware('permission:8')->name('update');
                Route::delete('/pajak/{id}', [PajakController::class, 'destroy'])->middleware('permission:8')->name('destroy');

            });


        Route::prefix('program')->as('program.')
            ->group(function () {
                Route::get('', [Ms_program::class, 'index'])->middleware('permission:43')->name('index');
                Route::post('load', [Ms_program::class, 'load'])->middleware('permission:43')->name('load');
                Route::get('create', [Ms_program::class, 'create'])->middleware('permission:43')->name('create');
                Route::post('store', [Ms_program::class, 'store'])->middleware('permission:43')->name('store');
                Route::get('edit/{id}', [Ms_program::class, 'edit'])->middleware('permission:43')->name('edit');
                Route::post('update/{id}', [Ms_program::class, 'update'])->middleware('permission:43')->name('update');
                Route::delete('/pajak/{id}', [Ms_program::class, 'destroy'])->middleware('permission:43')->name('destroy');

            });

        Route::prefix('subkegiatan')->as('subkegiatan.')
            ->group(function () {
                Route::get('', [Subkegiatan::class, 'index'])->middleware('permission:34')->name('index');
                Route::post('load', [Subkegiatan::class, 'load'])->middleware('permission:34')->name('load');
                Route::get('create', [Subkegiatan::class, 'create'])->middleware('permission:34')->name('create');
                Route::get('edit/{id}', [Subkegiatan::class, 'edit'])->middleware('permission:34')->name('edit');
                Route::delete('/subkegiatan/{id}', [Subkegiatan::class, 'destroy'])->middleware('permission:8')->name('destroy');
                Route::post('update/{id}', [Subkegiatan::class, 'update'])->middleware('permission:34')->name('update');
                Route::get('/search-product', [Subkegiatan::class, 'searchProduct'])->middleware('permission:34')->name('search');
                Route::get('/product/barcode/{barcode}', [Subkegiatan::class, 'getProductByBarcode'])->middleware('permission:34')->name('getByBarcode');
                Route::post('store', [Subkegiatan::class, 'store'])->middleware('permission:34')->name('store');
                Route::post('/getprogram', [Subkegiatan::class, 'getprogram'])->middleware('permission:35')->name('getprogram');


            });

        Route::prefix('msanggaran')->as('msanggaran.')
            ->group(function () {
                Route::get('', [Ms_anggaran::class, 'index'])->middleware('permission:39')->name('index');
                Route::post('load', [Ms_anggaran::class, 'load'])->middleware('permission:39')->name('load');
                Route::get('create', [Ms_anggaran::class, 'create'])->middleware('permission:39')->name('create');
                Route::get('edit/{id}', [Ms_anggaran::class, 'edit'])->middleware('permission:39')->name('edit');
                Route::delete('/msanggaran/{id}', [Ms_anggaran::class, 'destroy'])->middleware('permission:39')->name('destroy');
                Route::post('update/{id}', [Ms_anggaran::class, 'update'])->middleware('permission:39')->name('update');
                Route::get('/search-product', [Ms_anggaran::class, 'searchProduct'])->middleware('permission:39')->name('search');
                Route::get('/product/barcode/{barcode}', [Ms_anggaran::class, 'getProductByBarcode'])->middleware('permission:39')->name('getByBarcode');
                Route::post('store', [Ms_anggaran::class, 'store'])->middleware('permission:39')->name('store');
                Route::post('/msanggaran/upload', [Ms_anggaran::class, 'upload'])->middleware('permission:39')->name('upload');
                Route::get('/download-format', [Ms_anggaran::class, 'downloadFormat'])->middleware('permission:39')->name('download_format');
                Route::post('/getsubkegiatan', [Ms_anggaran::class, 'getsubkegiatan'])->middleware('permission:39')->name('getsubkegiatan');
                Route::post('/getsumberdana', [Ms_anggaran::class, 'getsumberdana'])->middleware('permission:39')->name('getsumberdana');


            });

        Route::prefix('mssumberdana')->as('mssumberdana.')
            ->group(function () {
                Route::get('', [Ms_sumberdana::class, 'index'])->middleware('permission:40')->name('index');
                Route::post('load', [Ms_sumberdana::class, 'load'])->middleware('permission:40')->name('load');
                Route::get('create', [Ms_sumberdana::class, 'create'])->middleware('permission:40')->name('create');
                Route::get('edit/{id}', [Ms_sumberdana::class, 'edit'])->middleware('permission:40')->name('edit');
                Route::delete('/mssumberdana/{id}', [Ms_sumberdana::class, 'destroy'])->middleware('permission:8')->name('destroy');
                Route::post('update/{id}', [Ms_sumberdana::class, 'update'])->middleware('permission:40')->name('update');
                Route::get('/search-product', [Ms_sumberdana::class, 'searchProduct'])->middleware('permission:40')->name('search');
                Route::get('/product/barcode/{barcode}', [Ms_sumberdana::class, 'getProductByBarcode'])->middleware('permission:40')->name('getByBarcode');
                Route::post('store', [Ms_sumberdana::class, 'store'])->middleware('permission:40')->name('store');
                Route::post('/mssumberdana/upload', [Ms_sumberdana::class, 'upload'])->middleware('permission:40')->name('upload');
                Route::get('/download-format', [Ms_sumberdana::class, 'downloadFormat'])->middleware('permission:40')->name('download_format');



            });

        Route::prefix('PenguranganStok')->as('PenguranganStok.')
            ->group(function () {
                Route::get('', [PenguranganStok::class, 'index'])->middleware('permission:10')->name('index');
                Route::post('load', [PenguranganStok::class, 'load'])->middleware('permission:10')->name('load');
                Route::get('create', [PenguranganStok::class, 'create'])->middleware('permission:10')->name('create');
                Route::get('edit/{id}', [PenguranganStok::class, 'edit'])->middleware('permission:10')->name('edit');
                Route::delete('/delete/{id}', [PenguranganStok::class, 'destroy'])->middleware('permission:10')->name('delete');
                Route::get('/search-product', [PenguranganStok::class, 'searchProduct'])->middleware('permission:10')->name('search');
                Route::get('/product/barcode/{barcode}', [PenguranganStok::class, 'getProductByBarcode'])->middleware('permission:10')->name('getByBarcode');
                Route::post('store', [PenguranganStok::class, 'store'])->middleware('permission:10')->name('store');


            });




    });


    Route::prefix('laporan')->as('laporan.')->group(function () {

        Route::prefix('laporan')->as('laporan.')
            ->group(function () {
                Route::get('', [Laporan::class, 'index'])->middleware('permission:33')->name('index');
                Route::post('load', [Laporan::class, 'load'])->middleware('permission:33')->name('load');
                Route::get('cetakbku', [Laporan::class, 'cetakbku'])->name('cetakbku');
                Route::get('cetakbpp', [Laporan::class, 'cetakbpp'])->name('cetakbpp');
                Route::get('cetakbpbank', [Laporan::class, 'cetakbpbank'])->name('cetakbpbank');
                Route::post('tandaTangan', [Laporan::class, 'tandaTangan'])->name('tandaTangan');
                Route::post('tandaTanganPa', [Laporan::class, 'tandaTanganPa'])->name('tandaTanganPa');
                Route::get('cetakdth', [Laporan::class, 'cetakdth'])->name('cetakdth');
                Route::get('cetakrealisasi', [Laporan::class, 'cetakrealisasi'])->name('cetakrealisasi');
                Route::get('cetakobjek', [Laporan::class, 'cetakobjek'])->name('cetakobjek');
                Route::post('getsubkegiatan', [Laporan::class, 'getsubkegiatan'])->name('getsubkegiatan');
                Route::post('getakunbelanja', [Laporan::class, 'getakunbelanja'])->name('getakunbelanja');
                Route::get('cetakspj', [Laporan::class, 'cetakspj'])->name('cetakspj');
                Route::get('cetakrinciancp', [Laporan::class, 'cetakrinciancp'])->name('cetakrinciancp');
            });

    });


});

require __DIR__ . '/auth.php';
