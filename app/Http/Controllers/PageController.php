<?php

namespace App\Http\Controllers;

use App\Models\POOutModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDF;

class PageController extends Controller
{
    public function index()
    {
        return view('dashboard');
    }

    public function trxFrm()
    {
        return view('transaksi.trx_frm');
    }

    public function masterItem()
    {
        return view('b2b.master_item');
    }

    public function purchaseOrder()
    {
        return view('b2b.purchase_order');
    }

    public function poOpen(Request $request)
    {
        $chat_id = DB::table('tb_anggota')->select('chat_id', 'nama')->whereNotNull('chat_id')->get();

        return view('b2b.po_open', ['chat_id' => $chat_id]);
    }

    public function cetakSj($noDok)
    {
        $datas = POOutModel::where('no_dokumen', $noDok)->get();
        if($datas->isEmpty()){
            return abort(404, 'Data tidak ditemukan');
        } else {
            $pdf = PDF::loadview('/b2b/pdf_sj', ['datas' => $datas])->setPaper('A4', 'potrait');
    
            return $pdf->stream('Surat Jalan.pdf');
        }
    }

    public function cetakInv($noDok)
    {
        $datas = POOutModel::where('no_dokumen', $noDok)->get();
        if($datas->isEmpty()){
            return abort(404, 'Data tidak ditemukan');
        } else {
            $pdf = PDF::loadview('/b2b/pdf_inv', ['datas' => $datas])->setPaper('A4', 'potrait');
    
            return $pdf->stream('Surat Jalan.pdf');
        }
    }

    public function listAnggota()
    {
        return view('anggota.list_anggota');
    }

    public function listStockBarang()
    {
        return view('report.list_stock_barang');
    }
}
