<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;

/* LOGIN */
Route::get('/login', [AuthController::class, 'loginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/* DASHBOARD (WAJIB LOGIN) */
Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/', function () {
        return view('dashboard');
    })->name('dashboard');
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

Route::middleware(['web', 'auth', 'can:menu-trx'])->group(function () {
    route::get('transaksi/trx_frm', [PageController::class, 'trxFrm']);
});

Route::middleware(['web', 'auth', 'can:menu-b2b'])->group(function () {
    route::get('b2b/master_item', [PageController::class, 'masterItem']);
    route::get('b2b/purchase_order', [PageController::class, 'purchaseOrder']);
    route::get('b2b/po_open', [PageController::class, 'poOpen']);
    route::get('b2b/cetak_sj/{id}', [PageController::class, 'cetakSj']);
    route::get('b2b/cetak_inv/{id}', [PageController::class, 'cetakInv']);
});

Route::middleware(['web', 'auth', 'can:menu-anggota'])->group(function () {
    route::get('anggota/list', [PageController::class, 'listAnggota']);
});

Route::middleware(['web', 'auth', 'can:menu-report'])->group(function () {
    route::get('report/list_stock_barang', [PageController::class, 'listStockBarang']);
});
