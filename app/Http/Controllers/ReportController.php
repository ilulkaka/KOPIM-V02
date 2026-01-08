<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use App\Models\InModel;
use App\Models\OutModel;

class ReportController extends Controller
{
    public function listStockBarang(Request $request)
    {
        $draw = $request->input('draw');
        $search = $request->input('search')['value'];
        $start = (int) $request->input('start');
        $length = (int) $request->input('length');

        $endDate = $request->endDate;

        $Datas = DB::select(
            "SELECT a.item_cd, a.nama, a.spesifikasi, COALESCE(b.qty_in,0)as qty_in, COALESCE(c.qty_out,0)as qty_out, coalesce((b.qty_in) - ifnull(c.qty_out,0),0) as stock FROM
             (SELECT * FROM tb_master_po)a
             LEFT JOIN
             (SELECT item_cd, sum(qty_in)as qty_in FROM tb_in where tgl_in <= '$endDate' GROUP BY item_cd)b on b.item_cd=a.item_cd
             LEFT JOIN
             (SELECT item_cd, sum(qty_out)as qty_out FROM tb_out where tgl_out <= '$endDate' GROUP BY item_cd)c on c.item_cd=a.item_cd
             where (a.item_cd like '%$search%' or a.nama like '%$search%')
             LIMIT  $length OFFSET $start  "
        );

        $co = DB::select(
            "SELECT a.item_cd, a.nama, a.spesifikasi, COALESCE(b.qty_in,0)as qty_in, COALESCE(c.qty_out,0)as qty_out, coalesce((b.qty_in) - ifnull(c.qty_out,0),0) as stock FROM
             (SELECT * FROM tb_master_po)a
             LEFT JOIN
             (SELECT item_cd, sum(qty_in)as qty_in FROM tb_in where tgl_in <= '$endDate' GROUP BY item_cd)b on b.item_cd=a.item_cd
             LEFT JOIN
             (SELECT item_cd, sum(qty_out)as qty_out FROM tb_out where tgl_out <= '$endDate' GROUP BY item_cd)c on c.item_cd=a.item_cd
             where (a.item_cd like '%$search%' or a.nama like '%$search%')"
        );
        $count = count($co);

        return [
            'draw' => $draw,
            'recordsTotal' => $count,
            'recordsFiltered' => $count,
            'data' => $Datas,
        ];
    }

    public function addStockBarang(Request $request)
    {
                if (! in_array($request->user()->role, ['Administrator', 'Kasir'])) {
            return [
                'success' => false,
                'message' => 'Akses ditolak .',
            ];
        } else {
                        $idin = Str::uuid();
            $tambah_stock = InModel::create([
                'id_in' => $idin,
                'item_cd' => $request->ts_kode,
                'tgl_in' => $request->ts_tglmsk,
                'qty_in' => $request->ts_qty,
            ]);
            return [
                'message' => 'Tambah Stock Berhasil .',
                'success' => true,
            ];
        }
    }

    public function kurangStockBarang(Request $request)
    {
        if (! in_array($request->user()->role, ['Administrator', 'Kasir'])) {
            return [
                'success' => false,
                'message' => 'Akses ditolak .',
            ];
        } else {
            if ($request->input('ks_qty') > $request->input('ks_stock')) {
                return [
                    'message' => 'Qty out lebih besar dari Stock .',
                    'success' => false,
                ];
            } else {
                $idout = Str::uuid();
                $kurang_stock = OutModel::create([
                    'id_out' => $idout,
                    'item_cd' => $request->ks_kode,
                    'tgl_out' => $request->ks_tglklr,
                    'qty_out' => $request->ks_qty,
                ]);
                return [
                    'message' => 'Kurang Stock Berhasil .',
                    'success' => true,
                ];
            }
        }
    }
}
