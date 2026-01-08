<?php

use App\Http\Controllers\AnggotaController;
use App\Http\Controllers\B2BController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('trx/get_barcode', [TransaksiController::class, 'getBarcode']);
    Route::post('trx/ins_transaksi', [TransaksiController::class, 'insTransaksi']);
    Route::get('trx/hasil_trx_today', [TransaksiController::class, 'hasilTrxToday']);
    Route::get('trx/list_detail_trx', [TransaksiController::class, 'listDetailTrx']);
    Route::patch('trx/edt_transaksi', [TransaksiController::class, 'edtTransaksi']);

    Route::get('b2b/list_master_item', [B2BController::class, 'listMasterItem']);
    Route::post('b2b/add_master_item', [B2BController::class, 'addMasterItem']);
    Route::patch('b2b/edt_master_item', [B2BController::class, 'edtMasterItem']);

    Route::post('b2b/add_po', [B2BController::class, 'addPo']);
    Route::get('b2b/list_add_po', [B2BController::class, 'listAddPo']);
    Route::get('b2b/get_data_master_item/{itemCd}', [B2BController::class, 'getDataMasterItem']);
    Route::get('b2b/list_po_open', [B2BController::class, 'listPoOpen']);
    Route::get('b2b/get_no_dokumen', [B2BController::class, 'getNoDokumen']);
    Route::patch('b2b/upd_kirim_po', [B2BController::class, 'updKirimPo']);
    Route::post('b2b/krm_po_telegram', [B2BController::class, 'krmPoTelegram']);

    Route::get('anggota/list_anggota', [AnggotaController::class, 'listAnggota']);
    Route::post('anggota/add_anggota', [AnggotaController::class, 'addAnggota']);
    Route::patch('anggota/edt_anggota', [AnggotaController::class, 'edtAnggota']);

    Route::get('report/list_stock_barang', [ReportController::class, 'listStockBarang']);
    Route::post('report/add_stock_barang', [ReportController::class, 'addStockBarang']);
    Route::post('report/kurang_stock_barang', [ReportController::class, 'kurangStockBarang']);
});
