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
}
