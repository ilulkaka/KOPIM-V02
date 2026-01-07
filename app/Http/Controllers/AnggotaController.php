<?php

namespace App\Http\Controllers;

use App\Models\AnggotaModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AnggotaController extends Controller
{
    public function listAnggota(Request $request)
    {
        $draw = $request->input('draw');
        $search = $request->input('search')['value'];
        $start = (int) $request->input('start');
        $length = (int) $request->input('length');
        $Datas = DB::table('tb_anggota')->where(function ($q) use ($search) {
            $q
                ->where('no_barcode', 'like', '%'.$search.'%')
                ->orwhere('nik', 'like', '%'.$search.'%')
                ->orwhere('nama', 'like', '%'.$search.'%')
                ->orWhere('alamat', 'like', '%'.$search.'%');
        })
            ->orderBy('no_barcode', 'asc')
            ->skip($start)
            ->take($length)
            ->get();

        $count = DB::table('tb_anggota')
            ->where(function ($q) use ($search) {
                $q
                    ->where('no_barcode', 'like', '%'.$search.'%')
                    ->orwhere('nik', 'like', '%'.$search.'%')
                    ->orwhere('nama', 'like', '%'.$search.'%')
                    ->orWhere('alamat', 'like', '%'.$search.'%');
            })
            ->count();

        return [
            'draw' => $draw,
            'recordsTotal' => $count,
            'recordsFiltered' => $count,
            'data' => $Datas,
        ];
    }

    public function edtAnggota(Request $request)
    {
        // dd($request->all());
        $findid = AnggotaModel::find($request->ea_id_anggota);

        if (! in_array($request->user()->role, ['Administrator', 'Pengurus'])) {
            return [
                'success' => false,
                'message' => 'Akses ditolak .',
            ];
        } else {
            $findid->nik = $request->ea_nik;
            $findid->nama = $request->ea_nama;
            $findid->no_ktp = $request->ea_noktp;
            $findid->no_telp = $request->ea_notelp;
            $findid->alamat = $request->ea_alamat;
            $findid->status = $request->ea_status;

            $findid->save();

            return [
                'message' => 'Edit data Anggota Berhasil .',
                'success' => true,
            ];
        }
    }

    public function addAnggota(Request $request)
    {
        // dd($request->all());

        if (! in_array($request->user()->role, ['Administrator', 'Pengurus'])) {
            return [
                'success' => false,
                'message' => 'Akses ditolak .',
            ];
        } else {
            $cb = Carbon::now();
            $tahun = $cb->format('Ym');

            $leng = DB::select(
                'select length(no_barcode)as panjang from tb_anggota WHERE no_barcode in (SELECT max(no_barcode)FROM tb_anggota) '
            );
            $cek = DB::select(
                'SELECT substring(no_barcode,-4)as terakhir FROM tb_anggota WHERE no_barcode in (SELECT max(no_barcode)FROM tb_anggota) '
            );
            // dd($cek[0]->terakhir);

            if ($leng[0]->panjang == 0) {
                $no_barcode = $tahun.'1001';
            } elseif ($cek[0]->terakhir == 9999) {
                $no_barcode = $tahun.'1001';
            } else {
                $no_barcode = $tahun.$cek[0]->terakhir + 1;
            }

            $idAnggota = Str::uuid();
            $insert_anggota = AnggotaModel::create([
                'id_anggota' => $idAnggota,
                'nama' => $request->ta_nama,
                'nik' => $request->ta_nik,
                'alamat' => $request->ta_alamat,
                'no_telp' => $request->ta_notelp,
                'no_ktp' => $request->ta_noktp,
                'status' => 'Aktif',
                'no_barcode' => $no_barcode,
            ]);

            return [
                'message' => 'Tambah Anggota Berhasil .',
                'success' => true,
            ];
        }
    }
}
