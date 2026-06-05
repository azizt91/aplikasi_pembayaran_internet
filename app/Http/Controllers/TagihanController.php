<?php

namespace App\Http\Controllers;

use App\Models\Bulan;
use App\Models\Pelanggan;
use App\Models\Tagihan;
use App\Models\Paket;
use App\Models\Setting;
use App\Models\MobileNotification;
use App\Services\TagihanService;
use App\Services\FirebaseNotificationService;
use App\Http\Requests\StoreTagihanRequest;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use Dompdf\Dompdf;
use GuzzleHttp\Client;
use PDF;

class TagihanController extends Controller
{
    public function index()
    {
        $bulanList = Bulan::all();
        $jumlahPelangganAktif = Pelanggan::where('status', 'aktif')->count();

        return view('tagihan.index', compact('bulanList', 'jumlahPelangganAktif'));
    }


    public function storeTagihan(StoreTagihanRequest $request, TagihanService $tagihanService)
    {
        // Validasi sudah ditangani oleh StoreTagihanRequest

        try {
            $result = $tagihanService->generateBulk(
                $request->bulan,
                $request->tahun
            );

            $message = "Tagihan berhasil diproses. Sukses: {$result['success']}, Dilewati: {$result['skipped']}";
            Alert::success('Sukses', $message);

        } catch (\Exception $e) {
            // Error detail sudah dilog di Service
            Alert::error('Error', 'Gagal memproses tagihan. Silakan coba lagi.');
        }

        return redirect()->route('buka-tagihan');
    }


    // private function sendWhatsAppMessage($number, $message)
    // {
    //     $client = new Client();
    //     $client->post(env('WHATSAPP_ENDPOINT'), [
    //         'query' => [
    //             'api_key' => env('WHATSAPP_API_KEY'),
    //             'sender' => env('WHATSAPP_SENDER'),
    //             'number' => $number,
    //             'message' => $message,
    //         ]
    //     ]);
    // }


    public function bukaTagihan(Request $request)
    {
        // Fetch the list of months and years
        $bulanList = Bulan::all();
        $tahunList = range(date('Y'), date('Y') + 5);

        $pelangganList = Pelanggan::where('status', 'aktif')->get();

        // Default ke bulan dan tahun sekarang jika tidak ada request
        $bulan = $request->input('bulan', date('m'));
        $tahun = $request->input('tahun', date('Y'));

        // Ambil data tagihan yang BELUM LUNAS saja (status = BL)
        $tagihanList = Tagihan::where('bulan', $bulan)
                              ->where('tahun', $tahun)
                              ->where('status', 'BL')
                              ->with('pelanggan')
                              ->get();

        // Ambil template WA dari settings
        $appSetting = Setting::getSetting();
        $waTemplate = $appSetting->wa_template ?? '';

        return view('tagihan.buka-tagihan', compact('bulanList', 'tahunList', 'tagihanList', 'bulan', 'tahun', 'waTemplate'));
    }

    public function dataTagihan(Request $request)
    {
        $request->validate([
            'bulan' => 'required',
            'tahun' => 'required',
        ]);

        $bulan = $request->input('bulan');
        $tahun = $request->input('tahun');

        // Redirect ke buka-tagihan dengan parameter
        return redirect()->route('buka-tagihan', ['bulan' => $bulan, 'tahun' => $tahun]);
    }

    public function bayarTagihan(Request $request, $kode)
    {
        // Temukan tagihan berdasarkan kode atau id_tagihan
        $tagihan = Tagihan::find($kode);

        // Cek apakah tagihan ditemukan
        if (!$tagihan) {
            Alert::error('Error', 'Tagihan tidak ditemukan');
            return redirect()->route('buka-tagihan');
        }

        // Update status dan tanggal bayar tanpa memeriksa apakah sudah lunas
        $tagihan->status = 'LS';
        $tagihan->tgl_bayar = now();
        $tagihan->pembayaran_via = 'cash';
        $tagihan->save();

        // Kirim notifikasi pembayaran berhasil ke pelanggan
        $this->sendPaymentSuccessNotification($tagihan);

        Alert::success('Sukses', 'Pembayaran tagihan berhasil');

        // Redirect kembali ke buka-tagihan dengan parameter bulan dan tahun
        return redirect()->route('buka-tagihan', [
            'bulan' => $tagihan->bulan,
            'tahun' => $tagihan->tahun
        ]);
    }

    /**
     * Kirim notifikasi pembayaran berhasil ke pelanggan
     */
    private function sendPaymentSuccessNotification(Tagihan $tagihan)
    {
        try {
            $pelanggan = Pelanggan::where('id_pelanggan', $tagihan->id_pelanggan)->first();

            if (!$pelanggan) {
                return;
            }

            // Ambil nama bulan
            $namaBulan = $this->getNamaBulan((int) $tagihan->bulan);
            $formattedAmount = 'Rp ' . number_format($tagihan->tagihan, 0, ',', '.');

            $title = 'Pembayaran Berhasil';
            $body = "Terima kasih {$pelanggan->nama}! Pembayaran tagihan bulan {$namaBulan} {$tagihan->tahun} sebesar {$formattedAmount} telah dikonfirmasi.";

            // Simpan notifikasi ke database
            MobileNotification::createForPelanggan(
                $pelanggan->id_pelanggan,
                $title,
                $body,
                'tagihan',
                [
                    'tagihan_id' => $tagihan->id,
                    'bulan' => $tagihan->bulan,
                    'tahun' => $tagihan->tahun,
                    'status' => 'paid'
                ]
            );

            // Kirim push notification jika ada FCM token
            if (!empty($pelanggan->fcm_token)) {
                $firebaseService = app(FirebaseNotificationService::class);
                $firebaseService->sendToDevice(
                    $pelanggan->fcm_token,
                    $title,
                    $body,
                    ['type' => 'payment_success', 'tagihan_id' => (string) $tagihan->id]
                );
                Log::info('Payment success notification sent', ['id_pelanggan' => $pelanggan->id_pelanggan]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to send payment success notification: ' . $e->getMessage());
        }
    }

    /**
     * Get nama bulan dari nomor bulan
     */
    private function getNamaBulan(int $bulan): string
    {
        $namaBulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];
        return $namaBulan[$bulan] ?? (string) $bulan;
    }

    public function lunasTagihan(Request $request)
    {
        $bulanList = Bulan::all();
        $tahunList = range(date('Y') - 2, date('Y') + 1);

        // Default ke bulan dan tahun sekarang
        $bulan = $request->input('bulan', date('m'));
        $tahun = $request->input('tahun', date('Y'));

        // Query dengan filter bulan dan tahun
        $tagihanLunas = Tagihan::where('status', 'LS')
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->with('pelanggan')
            ->orderByDesc('updated_at')
            ->get();

        return view('tagihan.lunas-tagihan', compact('tagihanLunas', 'bulanList', 'tahunList', 'bulan', 'tahun'));
    }

    public function rollbackTagihan($id)
    {
        // Temukan tagihan berdasarkan ID
        $tagihan = Tagihan::find($id);

        // Cek apakah tagihan ditemukan
        if (!$tagihan) {
            Alert::error('Error', 'Tagihan tidak ditemukan');
            return redirect()->route('lunas-tagihan');
        }

        // Rollback status ke Belum Lunas
        $tagihan->status = 'BL';
        $tagihan->tgl_bayar = null;
        // Reset ke default value 'cash' (kolom ENUM tidak nullable)
        $tagihan->pembayaran_via = 'cash';
        $tagihan->save();

        Alert::success('Sukses', 'Status tagihan berhasil dikembalikan ke Belum Lunas');
        return redirect()->route('lunas-tagihan');
    }

    public function cetakStruk($id)
{
    // Temukan tagihan berdasarkan ID
    $tagihan = Tagihan::find($id);

    // Pastikan tagihan ditemukan
    if (!$tagihan) {
        return redirect()->route('buka-tagihan')->with('error', 'Tagihan tidak ditemukan');
    }

    // Render view to HTML
    $html = View::make('tagihan.cetak-struk', compact('tagihan'))->render();

    // Buat objek Dompdf
    $dompdf = new Dompdf();

    // Set base path untuk DOMPDF
    $options = $dompdf->getOptions();
    $options->set('isRemoteEnabled', true);
    $dompdf->setOptions($options);

    // Load HTML content
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');

    // Render PDF
    $dompdf->render();

    // Tampilkan PDF dengan memberikan nama file pada saat streaming
    return $dompdf->stream('struk_pembayaran.pdf');
}





    public function lunas()
    {
        $bulanIni = Carbon::now()->month;
        $tahunIni = Carbon::now()->year;

        $pelangganLunas = Pelanggan::where('status', 'aktif')
        ->whereHas('tagihan', function ($query) use ($bulanIni, $tahunIni) {
            $query->where('status', 'LS')
            ->where('bulan', $bulanIni) // Periksa bulan tagihan
            ->where('tahun', $tahunIni); // Periksa tahun tagihan
        })->get();

        return view('tagihan.lunas', compact('pelangganLunas'));
    }




        public function belumLunas()
    {
        $bulanIni = Carbon::now()->month;
        $tahunIni = Carbon::now()->year;

        $pelangganBelumLunas = Pelanggan::where('status', 'aktif')
            ->whereDoesntHave('tagihan', function ($query) use ($bulanIni, $tahunIni) {
                $query->where('status', 'LS')
                    ->where('bulan', $bulanIni) // Periksa bulan tagihan
                    ->where('tahun', $tahunIni); // Periksa tahun tagihan
            })
            ->orWhere(function ($query) use ($bulanIni, $tahunIni) {
                $query->whereHas('tagihan', function ($query) use ($bulanIni, $tahunIni) {
                    $query->where('status', '!=', 'LS')
                        ->where('bulan', $bulanIni) // Periksa bulan tagihan
                        ->where('tahun', $tahunIni); // Periksa tahun tagihan
                });
            })->get();

        return view('tagihan.belumLunas', compact('pelangganBelumLunas'));
    }

    public function deleteTagihan($id)
    {
        // Temukan tagihan berdasarkan ID
        $tagihan = Tagihan::find($id);

        // Cek apakah tagihan ditemukan
        if (!$tagihan) {
            Alert::error('Error', 'Tagihan tidak ditemukan');
            return redirect()->route('buka-tagihan');
        }

        // Simpan bulan dan tahun sebelum dihapus
        $bulan = $tagihan->bulan;
        $tahun = $tagihan->tahun;

        // Hapus tagihan
        $tagihan->delete();

        Alert::success('Sukses', 'Tagihan berhasil dihapus');

        // Redirect kembali ke buka-tagihan dengan parameter bulan dan tahun
        return redirect()->route('buka-tagihan', [
            'bulan' => $bulan,
            'tahun' => $tahun
        ]);
    }

    /**
     * Broadcast WhatsApp via Fonnte API
     */
    public function broadcastWhatsapp(Request $request)
    {
        $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer',
            'pesan' => 'required|string',
        ]);

        // Ambil token Fonnte
        $appSetting = Setting::getSetting();
        $token = $appSetting->fonnte_token ?? '';

        if (empty($token)) {
            return response()->json([
                'success' => false,
                'message' => 'Token Fonnte belum diatur. Silakan atur di menu Pengaturan.',
            ], 422);
        }

        $ids = $request->input('ids');
        $pesanTemplate = $request->input('pesan');

        $sent = 0;
        $skipped = 0;
        $errors = [];
        $broadcastCounts = [];

        // Ambil semua tagihan yang dipilih beserta relasinya
        $tagihanList = Tagihan::whereIn('id', $ids)->with('pelanggan')->get();

        foreach ($tagihanList as $tagihan) {
            $pelanggan = $tagihan->pelanggan;

            // Skip jika pelanggan tidak ada atau nomor WA kosong
            if (!$pelanggan || empty($pelanggan->whatsapp)) {
                $skipped++;
                continue;
            }

            // Format nomor HP: hapus spasi, strip, beri prefix 62
            $nomorWa = preg_replace('/[\s\-\(\)\+]/', '', $pelanggan->whatsapp);
            if (substr($nomorWa, 0, 1) === '0') {
                $nomorWa = '62' . substr($nomorWa, 1);
            } elseif (substr($nomorWa, 0, 2) !== '62') {
                $nomorWa = '62' . $nomorWa;
            }

            // Replace variabel dinamis
            $namaBulan = $this->getNamaBulan((int) $tagihan->bulan);
            $periode = $namaBulan . ' ' . $tagihan->tahun;
            $nominalTagihan = 'Rp ' . number_format($tagihan->tagihan, 0, ',', '.');

            $pesan = str_replace(
                ['{nama_pelanggan}', '{periode}', '{nominal_tagihan}'],
                [$pelanggan->nama, $periode, $nominalTagihan],
                $pesanTemplate
            );

            try {
                $response = Http::withHeaders([
                    'Authorization' => $token,
                ])->post('https://api.fonnte.com/send', [
                    'target' => $nomorWa,
                    'message' => $pesan,
                ]);

                if ($response->successful()) {
                    $body = $response->json();
                    if (isset($body['status']) && $body['status'] === true) {
                        // Increment broadcast_count
                        $tagihan->increment('broadcast_count');
                        $sent++;
                        $broadcastCounts[$tagihan->id] = $tagihan->broadcast_count;
                    } else {
                        $errors[] = $pelanggan->nama . ': ' . ($body['reason'] ?? 'Gagal kirim');
                        $skipped++;
                    }
                } else {
                    $errors[] = $pelanggan->nama . ': HTTP ' . $response->status();
                    $skipped++;
                }
            } catch (\Exception $e) {
                Log::error('Fonnte broadcast error: ' . $e->getMessage(), [
                    'pelanggan' => $pelanggan->nama,
                    'nomor' => $nomorWa,
                ]);
                $errors[] = $pelanggan->nama . ': ' . $e->getMessage();
                $skipped++;
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Broadcast selesai. Terkirim: {$sent}, Dilewati: {$skipped}",
            'sent' => $sent,
            'skipped' => $skipped,
            'errors' => $errors,
            'broadcast_counts' => $broadcastCounts, // { tagihan_id: count }
        ]);
    }

}

