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
        // dd($request->all());
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
}
