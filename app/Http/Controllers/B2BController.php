<?php

namespace App\Http\Controllers;

use App\Models\MasterPOModel;
use App\Models\POModel;
use App\Models\POOutModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class B2BController extends Controller
{
    public function listMasterItem(Request $request)
    {
        $draw = $request->input('draw');
        $search = $request->input('search')['value'];
        $start = (int) $request->input('start');
        $length = (int) $request->input('length');
        $Datas = DB::table('tb_master_po')
            ->where(function ($q) use ($search) {
                $q
                    ->where('item_cd', 'like', '%'.$search.'%')
                    ->orwhere('nama', 'like', '%'.$search.'%')
                    ->orwhere('spesifikasi', 'like', '%'.$search.'%');
            })
            ->orderBy('item_cd', 'asc')
            ->skip($start)
            ->take($length)
            ->get();

        $count = DB::table('tb_master_po')
            ->where(function ($q) use ($search) {
                $q
                    ->where('item_cd', 'like', '%'.$search.'%')
                    ->orwhere('nama', 'like', '%'.$search.'%')
                    ->orwhere('spesifikasi', 'like', '%'.$search.'%');
            })
            ->count();

        return [
            'draw' => $draw,
            'recordsTotal' => $count,
            'recordsFiltered' => $count,
            'data' => $Datas,
        ];
    }

    public function addMasterItem(Request $request)
    {
        // dd($request->all());
        $cek = DB::table('tb_master_po')
            ->select('item_cd')
            ->where('item_cd', $request->tmpo_itemCd)
            ->count();

        if ($cek <= 0) {
            $idMasterPO = Str::uuid();
            $ins_masterPO = MasterPOModel::create([
                'id_master_po' => $idMasterPO,
                'item_cd' => $request->tmpo_itemCd,
                'nama' => $request->tmpo_nama,
                'spesifikasi' => $request->tmpo_spesifikasi,
                'satuan' => $request->tmpo_uom,
                'harga' => $request->tmpo_harga,
            ]);

            return [
                'message' => 'Tambah data Master PO Berhasil .',
                'success' => true,
            ];
        } else {
            return [
                'message' => 'Item Cd sudah ada .',
                'success' => false,
            ];
        }
    }

    public function edtMasterItem(Request $request)
    {
        // dd($request->all());
        $findid = MasterPOModel::find($request->empo_id);

        if (! in_array($request->user()->role, ['Administrator'])) {
            return [
                'message' => 'Edit gagal, Level tidak diperbolehkan .',
                'success' => false,
            ];
        } else {
            $findid->nama = $request->empo_nama;
            $findid->spesifikasi = $request->empo_spesifikasi;
            $findid->harga = $request->empo_harga;
            $findid->satuan = $request->empo_satuan;

            $findid->save();

            return [
                'message' => 'Edit Master PO Berhasil .',
                'success' => true,
            ];
        }
    }

    public function listAddPo(Request $request)
    {
        $draw = $request->input('draw');
        $search = $request->input('search')['value'];
        $start = (int) $request->input('start');
        $length = (int) $request->input('length');

        $po_no = $request->no_po;

        $Datas = DB::table('tb_po')->where('nomor_po', $po_no)
            ->where(function ($q) use ($search) {
                $q
                    ->where('item_cd', 'like', '%'.$search.'%')
                    ->orwhere('nomor_po', 'like', '%'.$search.'%')
                    ->orwhere('nama', 'like', '%'.$search.'%')
                    ->orwhere('spesifikasi', 'like', '%'.$search.'%');
            })
            ->orderBy('updated_at', 'desc')
            ->skip($start)
            ->take($length)
            ->get();

        $count = DB::table('tb_po')->where('nomor_po', $po_no)
            ->where(function ($q) use ($search) {
                $q
                    ->where('item_cd', 'like', '%'.$search.'%')
                    ->orwhere('nomor_po', 'like', '%'.$search.'%')
                    ->orwhere('nama', 'like', '%'.$search.'%')
                    ->orwhere('spesifikasi', 'like', '%'.$search.'%');
            })
            ->count();

        return [
            'draw' => $draw,
            'recordsTotal' => $count,
            'recordsFiltered' => $count,
            'data' => $Datas,
        ];
    }

    public function getDataMasterItem($itemCd)
    {
        // dd($itemCd);
        $getDatas = DB::table('tb_master_po')->where('item_cd', $itemCd)->first();

        if (! $getDatas) {
            return [
                'success' => false,
                'message' => 'Item Cd tidak ada .',
            ];
        } else {
            return [
                'success' => true,
                'datas' => $getDatas,
            ];
        }
    }

    public function addPo(Request $request)
    {
        // dd($request->all());
        if (! in_array($request->user()->role, ['Administrator'])) {
            return [
                'success' => false,
                'message' => 'Insert data gagal .',
            ];
        } else {

            $harga = str_replace(',', '', $request->tdpo_harga);
            $ins = POModel::create([
                'id_po' => str::uuid(),
                'tgl_po' => date('Y-m-d'),
                'nomor_po' => $request->tdpo_nopo,
                'item_cd' => strtoupper($request->tdpo_itemCd), // Ubah ke huruf besar
                'nama' => $request->tdpo_nama,
                'spesifikasi' => $request->tdpo_spesifikasi,
                'qty' => $request->tdpo_qty,
                'satuan' => $request->tdpo_satuan,
                'harga' => $harga,
                'nouki' => $request->tdpo_nouki,
                'status_po' => 'Open',
            ]);

            return [
                'success' => true,
                'message' => 'Insert data berhasil .',
            ];
        }
    }

    public function listPoOpen (Request $request){
        $draw = $request->input('draw');
        $search = $request->input('search')['value'];
        $start = (int) $request->input('start');
        $length = (int) $request->input('length');

        $statusPO = $request->statusPO;
        $f_tgl = $request->f_tgl;

        if($statusPO == 'Open' && $f_tgl == null){
            $tgl = '';
            $asc = 'order by nouki asc';
        } else if($statusPO == 'Open' && $f_tgl != null) {
            $tgl = "where a.nouki <='$f_tgl'";
            $asc = 'order by a.nouki asc';
        } else if($statusPO == 'Closed' && $f_tgl == null){
            $tgl = '';
            $asc = 'order by a.nouki desc';
        } else if($statusPO == 'Closed' && $f_tgl != null) {
            $tgl = "where b.tgl_kirim ='$f_tgl'";
            $asc = 'order by a.nouki desc';
        }
        // dd($tgl);
        $Datas = DB::select("SELECT a.*, b.qty_out, a.qty - b.qty_out as temp_plan, a.qty * a.harga as total, b.tgl_kirim, c.stock FROM
        (select * FROM tb_po where status_po = '$statusPO' )a 
        left join
        (select id_po, SUM(qty_out)as qty_out, tgl_kirim FROM tb_po_out group by id_po, tgl_kirim)b on a.id_po = b.id_po
        left join
        (select * from v_stock)c on a.item_cd = c.item_cd
        $tgl and (a.item_cd like '%$search%' or a.nomor_po like '%$search%') $asc LIMIT $length OFFSET $start");

        $co = DB::select("SELECT a.*, b.qty_out, a.qty - b.qty_out as temp_plan, a.qty * a.harga as total, b.tgl_kirim, c.stock FROM
        (select * FROM tb_po where status_po = '$statusPO')a 
        left join
        (select id_po, SUM(qty_out)as qty_out, tgl_kirim FROM tb_po_out group by id_po, tgl_kirim)b on a.id_po = b.id_po
        left join
        (select * from v_stock)c on a.item_cd = c.item_cd $tgl");
        $count = count($co);

        return [
            'draw' => $draw,
            'recordsTotal' => $count,
            'recordsFiltered' => $count,
            'data' => $Datas,
        ];
    }

    public function getNoDokumen (Request $request){
         // Ambil nomor terakhir dari kolom po_nomor
        $lastDokNomor = POOutModel::orderBy('no_dokumen', 'desc')->value('no_dokumen');

        // Ambil bulan dan tahun saat ini
        $bulan = date('m');
        $tahun = date('Y');

        // Parse nomor terakhir (jika ada) dan increment
        if ($lastDokNomor) {
            // Ambil tahun dari nomor terakhir (asumsi format: 0002122024)
            $lastYear = substr($lastDokNomor, -4);

            if ($lastYear == $tahun) {
                // Tahun sama, ambil angka terakhir dan increment
                $lastNumber = (int)substr($lastDokNomor, 0, 3);
                $nextNumber = $lastNumber + 1;
            } else {
                // Tahun berbeda, reset nomor ke 1
                $nextNumber = 1;
            }
        } else {
            // Jika belum ada nomor terakhir, mulai dari 1
            $nextNumber = 1;
        }

        // Format nomor baru menjadi 3 angka
        $formattedNumber = str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

        // Gabungkan menjadi nomor PO baru
        $newDokNomor = $formattedNumber . $bulan . $tahun;

        // Return data ke client
        return response()->json([
            'success' => true,
            'last_dok_nomor' => $lastDokNomor,
            'new_dok_nomor' => $newDokNomor,
        ]);
    }
}
