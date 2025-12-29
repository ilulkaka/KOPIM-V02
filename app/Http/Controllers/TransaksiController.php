<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Models\AnggotaModel;
use App\Models\TransaksiModel;
use Carbon\Carbon;
use App\Models\User;
use PHPMailer\PHPMailer\PHPMailer;

class TransaksiController extends Controller
{
    private $botToken;

    public function __construct()
    {
        $this->botToken = env('TELEGRAM_BOT_TOKEN'); // Ganti dengan token bot Anda
    }

    public function getBarcode(Request $request)
    {
        if (! in_array($request->user()->role, ['Administrator', 'Kasir'])) {
            return [
                'success' => false,
                'message' => 'Akses ditolak .',
            ];
        } else {
            $no_barcode1 = $request->input('no_barcode');
            if (strlen($no_barcode1) < 16) {
                return [
                    'message' => 'Barcode salah .',
                    'success' => false,
                ];
            } else {
                $no_barcode = substr(strrchr($no_barcode1, '/'), 1); // mengambil data id_anggota
            }

            $cek_anggota = DB::table('tb_anggota')
                ->select('id_anggota', 'no_barcode', 'nik', 'nama')
                ->where('id_anggota', $no_barcode)
                ->where('status', '=', 'Aktif')
                ->get();

            if (count($cek_anggota) == 0) {
                return [
                    'message' => 'Record tidak ditemukan .',
                    'success' => false,
                ];
            } else {
                return [
                    'message' => 'success',
                    'success' => true,
                    'nama' => $cek_anggota[0]->nama,
                    'nik' => $cek_anggota[0]->nik,
                ];
            }
        }
    }

    public function insTransaksi(Request $request)
    {
        // dd($request->all());

        $id_anggota = substr(strrchr($request->trx_no_barcode, '/'), 1);
        $datas = DB::table('tb_anggota')
            ->select('id_anggota', 'no_barcode', 'nik', 'nama', 'no_telp', 'chat_id')
            ->where('id_anggota', $id_anggota)
            ->where('status', '=', 'Aktif')
            ->get();

        $chatId = $datas[0]->chat_id;

        // Validasi jika data kosong
        if ($datas->isEmpty()) {
            return response()->json([
                'message' => 'Data anggota tidak ditemukan atau status tidak aktif.',
                'success' => false,
            ], 404);
        }

        $kategori = $request->input('trx_kategori');
        $no_barcode = ($kategori == 'Anggota') ? $datas[0]->no_barcode : '999999';
        $date_trx = ($request->role1 == 'Administrator') ? $request->tgl_trx : date('Y-m-d');
        $idTrx = Str::uuid();

        // Insert data transaksi
        $insert_trx = TransaksiModel::create([
            'id_trx_belanja' => $idTrx,
            'tgl_trx' => $date_trx,
            'nama' => $datas[0]->nama,
            'nominal' => $request->trx_nominal,
            'no_barcode' => $no_barcode,
            'nik' => $datas[0]->nik,
            'kategori' => $kategori,
            'inputor' => $request->role,
        ]);

        if ($insert_trx) {
            // Validasi nomor telepon
            if (empty($chatId)) {
                return response()->json([
                    'message' => "Transaksi berhasil ! \nChat ID tidak tersedia untuk anggota ini.",
                    'success' => true,
                ]);
            } else {
                $url = "https://api.telegram.org/bot{$this->botToken}/sendMessage";
                $formattedNominal = 'Rp ' . number_format($request->trx_nominal, 0, ',', '.');  // Format nominal menjadi Rupiah
                Http::post($url, [
                    'chat_id' => $chatId,
                    'text' => "Halo, <b>".$datas[0]->nama."</b> ! \nTransaksi anda sebesar <b>".$formattedNominal."</b> \npada tanggal ".date('d-m-Y H:i:s'). " \n\n Terima Kasih.",
                    'parse_mode' => 'HTML' // Gunakan 'HTML' atau 'Markdown'
                ]);            
    
                return response()->json([
                    'message' => 'Transaksi berhasil!',
                    'success' => true,
                ]);
            }

        } else {
            return response()->json([
                'message' => 'Gagal menyimpan transaksi!',
                'success' => false,
            ]);
        }
    }

    public function hasilTrxToday (){
                $tgl_sekarang = date('Y-m-d');
        $hasil_anggota = DB::select(
            "select sum(nominal)as nominal from tb_trx_belanja where tgl_trx = '$tgl_sekarang' and kategori = 'Anggota'"
        );
        $hasil_umum = DB::select(
            "select sum(nominal)as nominal from tb_trx_belanja where tgl_trx = '$tgl_sekarang' and kategori = 'Umum'"
        );
        $hasil_total = $hasil_anggota[0]->nominal + $hasil_umum[0]->nominal;
        // dd($hasil_anggota[0]->nominal);
        return [
            'success' => true,
            'hasil_anggota' => $hasil_anggota[0]->nominal,
            'hasil_umum' => $hasil_umum[0]->nominal,
            'hasil_total' => $hasil_total,
        ];
    }

    public function listDetailTrx (Request $request){
        $draw = $request->input('draw');
        $search = $request->input('search')['value'];
        $start = (int) $request->input('start');
        $length = (int) $request->input('length');
        $tgl_awal = $request->tgl_awal;
        $tgl_akhir = $request->tgl_akhir;
        $Datas = DB::table('tb_trx_belanja as a')->leftJoin('tb_anggota as b','a.no_barcode','=','b.no_barcode')
            ->select('a.*','b.id_anggota')
            ->whereBetween('a.tgl_trx', [$tgl_awal, $tgl_akhir])
            ->where(function ($q) use ($search) {
                $q
                    ->where('a.no_barcode', 'like', '%' . $search . '%')
                    ->orwhere('a.nama', 'like', '%' . $search . '%');
            })
            ->orderBy('a.tgl_trx', 'desc')
            ->skip($start)
            ->take($length)
            ->get();

        $count = DB::table('tb_trx_belanja as a')->leftJoin('tb_anggota as b','a.no_barcode','=','b.no_barcode')
            ->select('a.*','b.id_anggota')
            ->whereBetween('a.tgl_trx', [$tgl_awal, $tgl_akhir])
            ->where(function ($q) use ($search) {
                $q
                    ->where('a.no_barcode', 'like', '%' . $search . '%')
                    ->orwhere('a.nama', 'like', '%' . $search . '%');
            })
            ->count();

        return [
            'draw' => $draw,
            'recordsTotal' => $count,
            'recordsFiltered' => $count,
            'data' => $Datas,
        ];
    }

    public function edtTransaksi (Request $request){

        $datas = DB::table('tb_anggota')
            ->select('id_anggota', 'nama', 'chat_id')
            ->where('id_anggota', $request->et_id_anggota)
            ->where('status', '=', 'Aktif')
            ->get();

        $chatId = $datas[0]->chat_id;

        if (in_array($request->user()->role, ['Administrator'])) {
        $findid = TransaksiModel::find($request->et_id);

                $url = "https://api.telegram.org/bot{$this->botToken}/sendMessage";
                $nominalAwal = 'Rp ' . number_format($findid->nominal, 0, ',', '.');  // Format nominal menjadi Rupiah
                $nominalAkhir = 'Rp ' . number_format($request->et_nominal, 0, ',', '.');  // Format nominal menjadi Rupiah
                Http::post($url, [
                    'chat_id' => $chatId,
                    'text' => "Halo, <b>".$datas[0]->nama."</b> ! \nPerubahan data Transaksi dari <b>".$nominalAwal."</b> menjadi <b>".$nominalAkhir."</b> \npada tanggal ".date('d-m-Y H:i:s'). " \n\nMohon maaf atas ketidaknyamanan ini. \nTerima Kasih.",
                    'parse_mode' => 'HTML' // Gunakan 'HTML' atau 'Markdown'
                ]); 

        $findid->nominal = $request->et_nominal;
        $findid->save(); 

            return [
                'message' => 'Edit data Berhasil .',
                'success' => true,
            ];
        } else {
            return [
                'message' => 'Edit gagal, Access Denied .',
                'success' => false,
            ];
        }
    }
}
