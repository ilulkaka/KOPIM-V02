<?php

namespace App\Http\Controllers;

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
}
