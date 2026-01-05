<?php

namespace App\Http\Controllers;

use App\Models\AnggotaModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class AnggotaController extends Controller
{
    public function listAnggota (Request $request){
        $draw = $request->input('draw');
        $search = $request->input('search')['value'];
        $start = (int) $request->input('start');
        $length = (int) $request->input('length');
        $Datas = DB::table('tb_anggota')->where(function ($q) use ($search) {
                $q
                    ->where('no_barcode', 'like', '%' . $search . '%')
                    ->orwhere('nik', 'like', '%' . $search . '%')
                    ->orwhere('nama', 'like', '%' . $search . '%')
                    ->orWhere('alamat', 'like', '%' . $search . '%');
            })
            ->orderBy('no_barcode', 'asc')
            ->skip($start)
            ->take($length)
            ->get();

        $count = DB::table('tb_anggota')
            ->where(function ($q) use ($search) {
                $q
                    ->where('no_barcode', 'like', '%' . $search . '%')
                    ->orwhere('nik', 'like', '%' . $search . '%')
                    ->orwhere('nama', 'like', '%' . $search . '%')
                    ->orWhere('alamat', 'like', '%' . $search . '%');
            })
            ->count();

        return [
            'draw' => $draw,
            'recordsTotal' => $count,
            'recordsFiltered' => $count,
            'data' => $Datas,
        ];
    }
}
