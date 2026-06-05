<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Paket;
use App\Models\Pelanggan;
use App\Models\Tagihan;
use App\Models\Pengeluaran;
use App\Models\RouterosAPI;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $ip = 'remote2.vpnmurahjogja.my.id:3196';
        $user = 'azizt91';
        $password = 'Pmt52371';
        $API = new RouterosAPI();
        $API->debug = false;

        if ($API->connect($ip, $user, $password)) {

            $hotspotactive = $API->comm('/ip/hotspot/active/print');
            $resource = $API->comm('/system/resource/print');
            $secret = $API->comm('/ppp/secret/print');
            $secretactive = $API->comm('/ppp/active/print');
            $interface = $API->comm('/interface/ethernet/print');
            $routerboard = $API->comm('/system/routerboard/print');
            $identity = $API->comm('/system/identity/print');

            $statusUpCount = 0;
            $statusDownCount = 0;

            // Hitung jumlah status up dan down
            foreach ($netwatch as $data) {
                if ($data['status'] == 'up') {
                    $statusUpCount++;
                } elseif ($data['status'] == 'down') {
                    $statusDownCount++;
                }
            }


            $data = [
                'totalsecret' => count($secret),
                'totalhotspot' => count($hotspotactive),
                'hotspotactive' => count($hotspotactive),
                'secretactive' => count($secretactive),
                'cpu' => $resource[0]['cpu-load'],
                'uptime' => $resource[0]['uptime'],
                'version' => $resource[0]['version'],
                'interface' => $interface,
                'boardname' => $resource[0]['board-name'],
                'freememory' => $resource[0]['free-memory'],
                'freehdd' => $resource[0]['free-hdd-space'],
                'model' => $routerboard[0]['model'],
                'identity' => $identity[0]['name'],
                'statusUpCount' => $statusUpCount,
                'statusDownCount' => $statusDownCount,

            ];

            return view('dashboard', $data);
        } else {

            return redirect('failed');
        }
    }



    public function cpu()
    {
        $ip = session()->get('ip');
        $user = session()->get('user');
        $password = session()->get('password');
        $API = new RouterosAPI();
        $API->debug = false;

        if ($API->connect($ip, $user, $password)) {

            $cpu = $API->comm('/system/resource/print');

            $data = [
                'cpu' => $cpu['0']['cpu-load'],
            ];

            return view('realtime.cpu', $data);
        } else {

            return view('failed');
        }
    }



    public function uptime()
    {
        $ip = session()->get('ip');
        $user = session()->get('user');
        $password = session()->get('password');
        $API = new RouterosAPI();
        $API->debug = false;

        if ($API->connect($ip, $user, $password)) {

            $uptime = $API->comm('/system/resource/print');

            $data = [
                'uptime' => $uptime['0']['uptime'],
            ];

            return view('realtime.uptime', $data);
        } else {

            return view('failed');
        }
    }




    public function traffic($traffic)
    {
        $ip = session()->get('ip');
        $user = session()->get('user');
        $password = session()->get('password');
        $API = new RouterosAPI();
        $API->debug = false;

        if ($API->connect($ip, $user, $password)) {
            $traffic = $API->comm('/interface/monitor-traffic', array(
                'interface' => $traffic,
                'once' => '',
            ));

            $rx = $traffic[0]['rx-bits-per-second'];
            $tx = $traffic[0]['tx-bits-per-second'];

            $data = [
                'rx' => $rx,
                'tx' => $tx,
            ];

            // dd($data);

            return view('realtime.traffic', $data);
        } else {

            return view('failed');
        }
    }



    public function load()
    {
        $data = Report::orderBy('created_at', 'desc')->limit('2')->get();

        return view('realtime.load', compact('data'));
    }

    // public function dashboard()
    // {
    // 	$jumlah_pelanggan = Pelanggan::all()->count();
    //     $jumlah_paket = Paket::all()->count();
    // 	return view('dashboard')->with('jumlah_pelanggan',$jumlah_pelanggan)->with('jumlah_paket', $jumlah_paket);
    // }


    // public function showDashboard(Request $request)
    // {
    //     // Ambil jumlah paket dari model Paket
    //     $jumlah_paket = Paket::count();

    //     $jumlah_pelanggan_aktif = Pelanggan::where('status', 'aktif')->count();

    //     $jumlah_pelanggan_nonaktif = Pelanggan::where('status', 'nonaktif')->count();

    //     $activePelangganIds = Pelanggan::where('status', 'aktif')->pluck('id_pelanggan');

    //     // Tentukan tahun dan bulan saat ini
    //     $now = Carbon::now();
    //     $year = $now->year;
    //     $month = $now->month;

    //     // Hitung jumlah pelanggan lunas dengan status aktif
    //     $jumlah_pelanggan_lunas = Pelanggan::whereIn('id_pelanggan', $activePelangganIds)
    //         ->whereHas('tagihan', function ($query) use ($year, $month) {
    //             $query->where('status', 'LS')
    //                   ->whereYear('created_at', $year)
    //                   ->whereMonth('created_at', $month);
    //         })->count();

    //     // Hitung jumlah pelanggan belum lunas dengan status aktif
    //     $jumlah_pelanggan_belum_lunas = Pelanggan::whereIn('id_pelanggan', $activePelangganIds)
    //         ->whereHas('tagihan', function ($query) use ($year, $month) {
    //             $query->where(function ($query) use ($year, $month) {
    //                 $query->where('status', '!=', 'LS')
    //                       ->orWhereNull('status');
    //             })->whereYear('created_at', $year)
    //               ->whereMonth('created_at', $month);
    //         })->count();

    //     // Logika untuk pengambilan pendapatan berdasarkan bulan dan tahun yang dipilih
    //     $selectedMonth = $request->input('bulan', $month); // Gunakan bulan saat ini sebagai default
    //     $selectedYear = $request->input('tahun', $year);   // Gunakan tahun saat ini sebagai default

    //     $totalRevenue = Tagihan::whereYear('created_at', $selectedYear)
    //         ->whereMonth('created_at', $selectedMonth)
    //         ->where('status', 'LS')
    //         ->sum('tagihan');

    //     // Query untuk menghitung pengeluaran bulan ini
    //     $pengeluaranBulanIni = Pengeluaran::whereYear('tanggal', $selectedYear)
    //                                       ->whereMonth('tanggal', $selectedMonth)
    //                                       ->sum('jumlah');

    //     // Hitung total pendapatan setelah dikurangi pengeluaran
    //     $netRevenue = $totalRevenue - $pengeluaranBulanIni;

    //     // Hitung tagihan bulan ini
    //     $tagihanBulanIni = Tagihan::where('status', 'LS')
    //         ->whereYear('created_at', Carbon::now()->year)
    //         ->whereMonth('created_at', Carbon::now()->month)
    //         ->sum('tagihan');

    //     // Mendapatkan data pendapatan dan pengeluaran setiap bulan
    //     $pendapatan = [];
    //     $pengeluaran = [];

    //     for ($month = 1; $month <= 12; $month++) {
    //         $pendapatan[] = Tagihan::whereYear('created_at', $selectedYear)
    //             ->whereMonth('created_at', $month)
    //             ->where('status', 'LS')
    //             ->sum('tagihan');

    //         $pengeluaran[] = Pengeluaran::whereYear('tanggal', $selectedYear)
    //             ->whereMonth('tanggal', $month)
    //             ->sum('jumlah');
    //     }

    //     if ($request->ajax()) {
    //         // Kembalikan data dalam format JSON jika request adalah AJAX
    //         return response()->json([
    //             'netRevenue' => rupiah($netRevenue),
    //             'totalRevenue' => rupiah($totalRevenue),
    //             'pengeluaranBulanIni' => rupiah($pengeluaranBulanIni),
    //             'pendapatan' => $pendapatan,
    //             'pengeluaran' => $pengeluaran
    //         ]);
    //     }



    //     return view('dashboard', compact(
    //         'jumlah_paket',
    //         'tagihanBulanIni',
    //         'jumlah_pelanggan_lunas',
    //         'jumlah_pelanggan_belum_lunas',
    //         'jumlah_pelanggan_aktif',
    //         'jumlah_pelanggan_nonaktif',
    //         'totalRevenue',
    //         'selectedMonth',
    //         'selectedYear',
    //         'pengeluaranBulanIni',
    //         'netRevenue',
    //         'pendapatan',
    //         'pengeluaran'
    //     ));
    // }

    // public function showDashboard(Request $request)
    // {
    //     // Ambil jumlah paket dari model Paket
    //     $jumlah_paket = Paket::count();

    //     $jumlah_pelanggan_aktif = Pelanggan::where('status', 'aktif')->count();

    //     $jumlah_pelanggan_nonaktif = Pelanggan::where('status', 'nonaktif')->count();

    //     $tagihanBulanIni = Tagihan::where('status', 'LS')
    //         ->whereYear('created_at', Carbon::now()->year)
    //         ->whereMonth('created_at', Carbon::now()->month)
    //         ->sum('tagihan');

    //     $activePelangganIds = Pelanggan::where('status', 'aktif')->pluck('id_pelanggan');

    //     // Tentukan tahun dan bulan saat ini
    //     $now = Carbon::now();
    //     $year = $now->year;
    //     $month = $now->month;

    //     // Hitung jumlah pelanggan lunas dengan status aktif
    //     $jumlah_pelanggan_lunas = Pelanggan::whereIn('id_pelanggan', $activePelangganIds)
    //         ->whereHas('tagihan', function ($query) use ($year, $month) {
    //             $query->where('status', 'LS')
    //                   ->whereYear('created_at', $year)
    //                   ->whereMonth('created_at', $month);
    //         })->count();

    //     // Hitung jumlah pelanggan belum lunas dengan status aktif
    //     $jumlah_pelanggan_belum_lunas = Pelanggan::whereIn('id_pelanggan', $activePelangganIds)
    //         ->whereHas('tagihan', function ($query) use ($year, $month) {
    //             $query->where(function ($query) use ($year, $month) {
    //                 $query->where('status', '!=', 'LS')
    //                       ->orWhereNull('status');
    //             })->whereYear('created_at', $year)
    //               ->whereMonth('created_at', $month);
    //         })->count();

    //     // Logika untuk pengambilan pendapatan berdasarkan bulan dan tahun yang dipilih
    //     $selectedMonth = $request->input('bulan', $month); // Gunakan bulan saat ini sebagai default
    //     $selectedYear = $request->input('tahun', $year);   // Gunakan tahun saat ini sebagai default

    //     $totalRevenue = Tagihan::whereYear('created_at', $selectedYear)
    //         ->whereMonth('created_at', $selectedMonth)
    //         ->where('status', 'LS')
    //         ->sum('tagihan');

    //     // Query untuk menghitung pengeluaran bulan ini
    //     $pengeluaranBulanIni = Pengeluaran::whereYear('tanggal', $selectedYear)
    //                                       ->whereMonth('tanggal', $selectedMonth)
    //                                       ->sum('jumlah');

    //     // Hitung total pendapatan setelah dikurangi pengeluaran
    //     $netRevenue = $totalRevenue - $pengeluaranBulanIni;

    //     // Mendapatkan data pendapatan dan pengeluaran setiap bulan
    //     $pendapatan = [];
    //     $pengeluaran = [];

    //     for ($month = 1; $month <= 12; $month++) {
    //         $pendapatan[] = Tagihan::whereYear('created_at', $selectedYear)
    //             ->whereMonth('created_at', $month)
    //             ->where('status', 'LS')
    //             ->sum('tagihan');

    //         $pengeluaran[] = Pengeluaran::whereYear('tanggal', $selectedYear)
    //             ->whereMonth('tanggal', $month)
    //             ->sum('jumlah');
    //     }

    //     if ($request->ajax()) {
    //         // Kembalikan data dalam format JSON jika request adalah AJAX
    //         return response()->json([
    //             'netRevenue' => rupiah($netRevenue),
    //             'totalRevenue' => rupiah($totalRevenue),
    //             'pengeluaranBulanIni' => rupiah($pengeluaranBulanIni),
    //             'pendapatan' => rupiah($pendapatan),
    //             'pengeluaran' => rupiah($pengeluaran)
    //         ]);
    //     }

    //     return view('dashboard', compact(
    //         'jumlah_paket',
    //         'tagihanBulanIni',
    //         'jumlah_pelanggan_lunas',
    //         'jumlah_pelanggan_belum_lunas',
    //         'jumlah_pelanggan_aktif',
    //         'jumlah_pelanggan_nonaktif',
    //         'totalRevenue',
    //         'selectedMonth',
    //         'selectedYear',
    //         'pengeluaranBulanIni',
    //         'netRevenue',
    //         'pendapatan',
    //         'pengeluaran'
    //     ));
    // }

    public function showDashboard(Request $request)
    {
        // Ambil jumlah paket dari model Paket
        $jumlah_paket = Paket::count();

        $jumlah_pelanggan_aktif = Pelanggan::where('status', 'aktif')->count();

        $jumlah_pelanggan_nonaktif = Pelanggan::where('status', 'nonaktif')->count();

        $tagihanBulanIni = Tagihan::where('status', 'LS')
        ->where('tahun', Carbon::now()->year)
        ->where('bulan', Carbon::now()->month)
        ->sum('tagihan');

        $activePelangganIds = Pelanggan::where('status', 'aktif')->pluck('id_pelanggan');

        // Tentukan tahun dan bulan saat ini
        $now = Carbon::now();
        $year = $now->year;
        $month = $now->month;

        // Hitung jumlah pelanggan lunas dengan status aktif
        $jumlah_pelanggan_lunas = Pelanggan::whereIn('id_pelanggan', $activePelangganIds)
        ->whereHas('tagihan', function ($query) use ($year, $month) {
            $query->where('status', 'LS')
            ->where('tahun', $year)
            ->where('bulan', $month);
        })->count();

        // Hitung jumlah pelanggan belum lunas dengan status aktif
        $jumlah_pelanggan_belum_lunas = Pelanggan::whereIn('id_pelanggan', $activePelangganIds)
        ->whereHas('tagihan', function ($query) use ($year, $month) {
            $query->where(function ($query) use ($year, $month) {
                $query->where('status', '!=', 'LS')
                ->orWhereNull('status');
            })->where('tahun', $year)
            ->where('bulan', $month);
        })->count();

        // Logika untuk pengambilan pendapatan berdasarkan bulan dan tahun yang dipilih
        $selectedMonth = $request->input('bulan', $month); // Gunakan bulan saat ini sebagai default
        $selectedYear = $request->input('tahun', $year);   // Gunakan tahun saat ini sebagai default

        $totalRevenue = Tagihan::where('tahun', $selectedYear)
        ->where('bulan', $selectedMonth)
        ->where('status', 'LS')
        ->sum('tagihan');

        // Query untuk menghitung pengeluaran bulan ini
        $pengeluaranBulanIni = Pengeluaran::where('tahun', $selectedYear)
        ->where('bulan', $selectedMonth)
        ->sum('jumlah');

        // Hitung total pendapatan setelah dikurangi pengeluaran
        $netRevenue = $totalRevenue - $pengeluaranBulanIni;

        // Mendapatkan data pendapatan dan pengeluaran setiap bulan
        $pendapatan = [];
        $pengeluaran = [];

        for ($month = 1; $month <= 12; $month++) {
            $pendapatan[] = Tagihan::where('tahun', $selectedYear)
            ->where('bulan', $month)
            ->where('status', 'LS')
            ->sum('tagihan');

            $pengeluaran[] = Pengeluaran::where('tahun', $selectedYear)
            ->where('bulan', $month)
            ->sum('jumlah');
        }

        if ($request->ajax()) {
            // Kembalikan data dalam format JSON jika request adalah AJAX
            return response()->json([
                'netRevenue' => $netRevenue,
                'totalRevenue' => $totalRevenue,
                'pengeluaranBulanIni' => $pengeluaranBulanIni,
                'pendapatan' => $pendapatan,
                'pengeluaran' => $pengeluaran
            ]);
        }

        return view('dashboard', compact(
            'jumlah_paket',
            'tagihanBulanIni',
            'jumlah_pelanggan_lunas',
            'jumlah_pelanggan_belum_lunas',
            'jumlah_pelanggan_aktif',
            'jumlah_pelanggan_nonaktif',
            'totalRevenue',
            'selectedMonth',
            'selectedYear',
            'pengeluaranBulanIni',
            'netRevenue',
            'pendapatan',
            'pengeluaran'
        ));
    }

    //metode API

    public function showDashboardApi(Request $request)
    {
        $jumlah_paket = Paket::count();
        $jumlah_pelanggan_aktif = Pelanggan::where('status', 'aktif')->count();
        $jumlah_pelanggan_nonaktif = Pelanggan::where('status', 'nonaktif')->count();

        $now = Carbon::now();
        $year = $request->input('tahun', $now->year);
        $month = $request->input('bulan', $now->month);

        $tagihanBulanIni = Tagihan::where('status', 'LS')
        ->whereYear('created_at', $year)
        ->whereMonth('created_at', $month)
        ->sum('tagihan');

        $activePelangganIds = Pelanggan::where('status', 'aktif')->pluck('id_pelanggan');

        $jumlah_pelanggan_lunas = Pelanggan::whereIn('id_pelanggan', $activePelangganIds)
        ->whereHas('tagihan', function ($query) use ($year, $month) {
            $query->where('status', 'LS')
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month);
        })->count();

        $jumlah_pelanggan_belum_lunas = Pelanggan::whereIn('id_pelanggan', $activePelangganIds)
        ->whereHas('tagihan', function ($query) use ($year, $month) {
            $query->where(function ($query) use ($year, $month) {
                $query->where('status', '!=', 'LS')
                ->orWhereNull('status');
            })->whereYear('created_at', $year)
            ->whereMonth('created_at', $month);
        })->count();

        $totalRevenue = Tagihan::where('status', 'LS')
        ->whereYear('created_at', $year)
        ->whereMonth('created_at', $month)
        ->sum('tagihan');

        $pengeluaranBulanIni = Pengeluaran::whereYear('created_at', $year)
        ->whereMonth('created_at', $month)
        ->sum('jumlah');

        $netRevenue = $totalRevenue - $pengeluaranBulanIni;

        $pendapatan = [];
        $pengeluaran = [];
        for ($m = 1; $m <= 12; $m++) {
            $pendapatan[] = Tagihan::where('status', 'LS')
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $m)
            ->sum('tagihan');

            $pengeluaran[] = Pengeluaran::whereYear('created_at', $year)
            ->whereMonth('created_at', $m)
            ->sum('jumlah');
        }

        return response()->json([
            'jumlah_paket' => $jumlah_paket,
            'jumlah_pelanggan_aktif' => $jumlah_pelanggan_aktif,
            'jumlah_pelanggan_nonaktif' => $jumlah_pelanggan_nonaktif,
            'jumlah_pelanggan_lunas' => $jumlah_pelanggan_lunas,
            'jumlah_pelanggan_belum_lunas' => $jumlah_pelanggan_belum_lunas,
            'tagihanBulanIni' => $tagihanBulanIni,
            'totalRevenue' => $totalRevenue,
            'pengeluaranBulanIni' => $pengeluaranBulanIni,
            'netRevenue' => $netRevenue,
            'pendapatan' => $pendapatan,
            'pengeluaran' => $pengeluaran
        ]);

    }
}
