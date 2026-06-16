<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\StokController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\RakController;
use App\Http\Controllers\InputBarangController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/login');
});

Route::get('/login', [AuthController::class, 'showLogin'])
    ->name('login');
Route::post('/login', [AuthController::class, 'login'])
    ->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout')->middleware('auth');

Route::middleware(['auth', 'role:pemilik,kasir'])
    ->group(function () {
        Route::get('/transaksi', [TransactionController::class, 'index'])
            ->name('transaksi.index');
        Route::post('/transaksi', [TransactionController::class, 'store'])
            ->name('transaksi.store');
        Route::get('/transaksi/{id}/nota', [TransactionController::class, 'nota'])
            ->name('transaksi.nota');
        Route::get('/produk/cari', [ProductController::class, 'search'])
            ->name('produk.search');
    });

Route::middleware(['auth', 'role:pemilik'])
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');
        Route::resource('/produk', ProductController::class);
        Route::post('/produk/{id}/deactivate', [ProductController::class, 'deactivate'])
            ->name('produk.deactivate');
        Route::get('/laporan', [LaporanController::class, 'index'])
            ->name('laporan.index');
        Route::get('/laporan/export-pdf', [LaporanController::class, 'exportPdf'])
            ->name('laporan.export');
        Route::get('/stok', [StokController::class, 'index'])
            ->name('stok.index');
        Route::get('/stok/{id}/min-stock', [StokController::class, 'editMinStock'])
            ->name('stok.edit-min');
        Route::post('/stok/{id}/min-stock', [StokController::class, 'updateMinStock'])
            ->name('stok.update-min');
        Route::post('/stok/dismiss/{id}', [StokController::class, 'dismissAlert'])
            ->name('stok.dismiss');
    });

Route::middleware(['auth', 'role:pemilik,kasir,gudang'])
    ->group(function () {
        Route::get('/peta-rak', [RakController::class, 'index'])
            ->name('rak.index');
    });

Route::middleware(['auth', 'role:pemilik,gudang'])
    ->group(function () {
        Route::get('/input-barang', [InputBarangController::class, 'create'])
            ->name('barang.create');
        Route::post('/input-barang', [InputBarangController::class, 'store'])
            ->name('barang.store');
    });
