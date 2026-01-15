<?php

namespace App\Http\Controllers;

use App\Models\TransaksiModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Illuminate\Support\Facades\Storage;

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
        $nominal = (int) str_replace(',', '', $request->trx_nominal);

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
            'nominal' => $nominal,
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
                $formattedNominal = 'Rp '.number_format($nominal, 0, ',', '.');  // Format nominal menjadi Rupiah
                Http::post($url, [
                    'chat_id' => $chatId,
                    'text' => 'Halo, <b>'.$datas[0]->nama."</b> ! \nTransaksi anda sebesar <b>".$formattedNominal."</b> \npada tanggal ".date('d-m-Y H:i:s')." \n\n Terima Kasih.",
                    'parse_mode' => 'HTML', // Gunakan 'HTML' atau 'Markdown'
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

    public function hasilTrxToday()
    {
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

    public function listDetailTrx(Request $request)
    {
        $draw = $request->input('draw');
        $search = $request->input('search')['value'];
        $start = (int) $request->input('start');
        $length = (int) $request->input('length');
        $tgl_awal = $request->tgl_awal;
        $tgl_akhir = $request->tgl_akhir;
        $Datas = DB::table('tb_trx_belanja as a')->leftJoin('tb_anggota as b', 'a.no_barcode', '=', 'b.no_barcode')
            ->select('a.*', 'b.id_anggota')
            ->whereBetween('a.tgl_trx', [$tgl_awal, $tgl_akhir])
            ->where(function ($q) use ($search) {
                $q
                    ->where('a.no_barcode', 'like', '%'.$search.'%')
                    ->orwhere('a.nama', 'like', '%'.$search.'%');
            })
            ->orderBy('a.updated_at', 'desc')
            ->skip($start)
            ->take($length)
            ->get();

        $count = DB::table('tb_trx_belanja as a')->leftJoin('tb_anggota as b', 'a.no_barcode', '=', 'b.no_barcode')
            ->select('a.*', 'b.id_anggota')
            ->whereBetween('a.tgl_trx', [$tgl_awal, $tgl_akhir])
            ->where(function ($q) use ($search) {
                $q
                    ->where('a.no_barcode', 'like', '%'.$search.'%')
                    ->orwhere('a.nama', 'like', '%'.$search.'%');
            })
            ->count();

        return [
            'draw' => $draw,
            'recordsTotal' => $count,
            'recordsFiltered' => $count,
            'data' => $Datas,
        ];
    }

    public function edtTransaksi(Request $request)
    {
// dd($request->all());
        $datas = DB::table('tb_anggota')
            ->select('id_anggota', 'nama', 'chat_id')
            ->where('id_anggota', $request->et_id_anggota)
            ->where('status', '=', 'Aktif')
            ->get();

        $chatId = $datas[0]->chat_id;

        if (in_array($request->user()->role, ['Administrator','Pengurus'])) {
            $findid = TransaksiModel::find($request->et_id);

            $url = "https://api.telegram.org/bot{$this->botToken}/sendMessage";
            $nominalAwal = 'Rp '.number_format($findid->nominal, 0, ',', '.');  // Format nominal menjadi Rupiah
            $nominalAkhir = 'Rp '.number_format($request->et_nominal, 0, ',', '.');  // Format nominal menjadi Rupiah
            Http::post($url, [
                'chat_id' => $chatId,
                'text' => 'Halo, <b>'.$datas[0]->nama."</b> ! \nPerubahan data Transaksi dari <b>".$nominalAwal.'</b> menjadi <b>'.$nominalAkhir."</b> \npada tanggal ".date('d-m-Y H:i:s')." \n\nMohon maaf atas ketidaknyamanan ini. \nTerima Kasih.",
                'parse_mode' => 'HTML', // Gunakan 'HTML' atau 'Markdown'
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

    public function downloadTransaksi(Request $request)
    {
        // dd($request->all());
        $tgl_awal = Carbon::createFromFormat(
            'Y-m-d',
            $request->tgl_awal
        )->format('d-m-Y');
        $tgl_akhir = Carbon::createFromFormat(
            'Y-m-d',
            $request->tgl_akhir
        )->format('d-m-Y');

        $Datas = DB::select(
            "select no_barcode, nik, nama, sum(nominal)as nominal, kategori from tb_trx_belanja where tgl_trx between '$request->tgl_awal' and '$request->tgl_akhir' group by no_barcode, nik, nama, kategori order by nik "
        );

        if (count($Datas) > 0) {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->mergeCells('A1:F1');
            $sheet->setCellValue(
                'A1',
                'Transaksi dari Tanggal ' . $tgl_awal . ' Sampai ' . $tgl_akhir
            );
            $sheet->setCellValue('A2', 'No');
            $sheet->setCellValue('B2', 'No Barcode');
            $sheet->setCellValue('C2', 'NIK');
            $sheet->setCellValue('D2', 'NAMA');
            $sheet->setCellValue('E2', 'Nominal');
            $sheet->setCellValue('F2', 'Kategori');

            $line = 3;
            $no = 1;
            foreach ($Datas as $data) {
                $sheet->setCellValue('A' . $line, $no++);
                $sheet->setCellValue('B' . $line, $data->no_barcode);
                $sheet->setCellValue('C' . $line, $data->nik);
                $sheet->setCellValue('D' . $line, $data->nama);
                $sheet->setCellValue('E' . $line, $data->nominal);
                $sheet->setCellValue('F' . $line, $data->kategori);

                $line++;
            }

            // ========= Online ========================================
            // $writer = new Xlsx($spreadsheet);
            // $filename = 'Transaksi_' . date('YmdHis') . '.xlsx';
            // //dd('/home/berkahma/public_html/storage/excel/' . $filename);

            // $writer->save(
            //     '/home/berkahma/public_html/storage/excel/' . $filename
            // );
            // return ['file' => url('/') . '/storage/excel/' . $filename];

            // ========= Offline ========================================
            // $writer = new Xlsx($spreadsheet);
            // $filename = 'Transaksi.xlsx';
            // $writer->save(public_path('storage/excel/' . $filename));
            // return ['file' => url('/') . '/storage/excel/' . $filename];

                $filename = 'Transaksi.xlsx';
    $path = storage_path('app/public/excel/' . $filename);

    if (!file_exists($path)) {
        return response()->json(['message' => 'File tidak ditemukan'], 404);
    }

    return response()->download(
        $path,
        $filename,
        ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
    );
        } else {
            return ['message' => 'No Data .', 'success' => false];
        }
    }

    public function sendMail(Request $request)
    {
        // dd($request->all());

        require base_path('vendor/autoload.php');
        $mail = new PHPMailer(true); // Passing `true` enables exceptions

        $m_tgl_awal = Carbon::createFromFormat(
            'Y-m-d',
            $request->m_tgl_awal
        )->format('d-m-Y');
        $m_tgl_akhir = Carbon::createFromFormat(
            'Y-m-d',
            $request->m_tgl_akhir
        )->format('d-m-Y');

        $user_mail = 'cs.kopim@kopbm.com';
        $user_pass = 'Cskopim12%';

        $mailSendTo = $request->sm_to;
        $mailSendToCC = $request->sm_cc;
        // data pengaju cuti
        // $datasCuti = CutiModel::where('id_cuti', $idCuti)->get();
        // $filename =
        //     $datasCuti[0]->nomer_report . '_' . $datasCuti[0]->nama . '.pdf';

        try {
            // Konfigurasi SMTP
            $mail->SMTPDebug = 2;
            $mail->Debugoutput = 'html';

            $mail->isSMTP();
            $mail->Host = 'smtp.hostinger.com';
            $mail->SMTPAuth = true;
            $mail->Username = $user_mail;
            $mail->Password = $user_pass;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
            $mail->Timeout = 30;

            // $mail->isSMTP();
            // $mail->Host = 'smtp.hostinger.com';
            // $mail->SMTPAuth = true;
            // $mail->Username = $user_mail;
            // $mail->Password = $user_pass;
            // $mail->SMTPSecure = 'tls';
            // $mail->Port = 587;

            // Set pengirim dan penerima
            $mail->setFrom($user_mail, 'CS KOPIM');
            $mail->addAddress($mailSendTo, $mailSendTo);

            $mail->addCC($mailSendToCC);
            //$mail->addBCC($request->emailBcc);
            $mail->AddAttachment(public_path('storage/excel/Transaksi.xlsx'));

            // Konten email
            $mail->isHTML(true);
            $mail->Subject = 'Rekap Transaksi Kopim';
            $mail->Body =
                'Untuk bagian pemotongan .
            <br>
            <br>
            Terlampir adalah rekap transaksi untuk periode <br><b>' .
                $m_tgl_awal .
                '</b> Sampai <b>' .
                $m_tgl_akhir .
                '</b> 
            <br>
            <br>
            Terima Kasih <br>
            KOPIM';

            // Kirim email
            $mail->send();

            return [
                'message' => 'Email berhasil dikirim.',
                'success' => true,
            ];
        } catch (Exception $e) {
            return "Email gagal dikirim: {$mail->ErrorInfo}";
            return [
                'message' => "Email gagal dikirim: {$mail->ErrorInfo}",
                'success' => false,
            ];
        }
    }
}
