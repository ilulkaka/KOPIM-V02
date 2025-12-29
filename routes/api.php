<?php

use App\Http\Controllers\TransaksiController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('trx/get_barcode', [TransaksiController::class, 'getBarcode']);
    Route::post('trx/ins_transaksi', [TransaksiController::class, 'insTransaksi']);
    Route::get('trx/hasil_trx_today', [TransaksiController::class, 'hasilTrxToday']);
});
