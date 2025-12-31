<?php

namespace App\Http\Controllers;
use App\Models\POOutModel;
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

    public function poOpen()
    {
        return view('b2b.po_open');
    }

    public function cetakSj ($noDok){
        $datas = POOutModel::where('no_dokumen',$noDok)->get();

        $pdf = PDF::loadview('/b2b/pdf_sj',['datas'=>$datas])->setPaper('A4', 'potrait');
        return $pdf->stream('Surat Jalan.pdf');
    }

    public function cetakInv ($noDok){
        $datas = POOutModel::where('no_dokumen',$noDok)->get();

        $pdf = PDF::loadview('/b2b/pdf_inv',['datas'=>$datas])->setPaper('A4', 'potrait');
        return $pdf->stream('Surat Jalan.pdf');
    }
}
