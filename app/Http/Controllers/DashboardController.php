<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Paket;
use App\Models\Pelanggan;
use App\Models\Tagihan;
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

    // public function showDashboard()
    // {
        
    //     // Ambil jumlah paket dari model Paket
    //     $jumlah_paket = Paket::count();

    //     $jumlah_pelanggan_aktif = Pelanggan::where('status', 'aktif')->count();

    //     $jumlah_pelanggan_nonaktif = Pelanggan::where('status', 'nonaktif')->count();


    //     $dataForChart = Tagihan::selectRaw('YEAR(tgl_bayar) as tahun, MONTH(tgl_bayar) as bulan, sum(tagihan) as total_tagihan')
    //                        ->groupBy('tahun', 'bulan')
    //                        ->orderBy('tahun', 'asc')
    //                        ->orderBy('bulan', 'asc')
    //                        ->get()
    //                        ->mapToGroups(function ($item) {
    //                            return [$item->tahun => $item->total_tagihan];
    //                        });


    
    //     $tagihanBulanIni = Tagihan::where('status', 'LS')
    //     ->whereYear('tgl_bayar', Carbon::now()->year)
    //     ->whereMonth('tgl_bayar', Carbon::now()->month)
    //     ->sum('tagihan');

    //     $jumlah_pelanggan_lunas = Pelanggan::whereHas('tagihan', function ($query) {
    //         $query->where('status', 'LS')
    //             ->whereYear('tgl_bayar', Carbon::now()->year)
    //             ->whereMonth('tgl_bayar', Carbon::now()->month);
    //     })->count();
    
    //     // Ambil jumlah pelanggan Belum Lunas (BL) berdasarkan bulan dan tahun ini
    //     $jumlah_pelanggan_belum_lunas = Pelanggan::whereDoesntHave('tagihan', function ($query) {
    //         $query->where('status', 'LS')
    //             ->whereYear('tgl_bayar', Carbon::now()->year)
    //             ->whereMonth('tgl_bayar', Carbon::now()->month);
    //     })->count();

    //     return view('dashboard', compact('jumlah_paket', 'tagihanBulanIni', 'jumlah_pelanggan_lunas', 'jumlah_pelanggan_belum_lunas', 'jumlah_pelanggan_aktif', 'jumlah_pelanggan_nonaktif', 'dataForChart'));
    // }

    public function showDashboard()
    {
        // Ambil jumlah paket dari model Paket
        $jumlah_paket = Paket::count();
    
        $jumlah_pelanggan_aktif = Pelanggan::where('status', 'aktif')->count();
    
        $jumlah_pelanggan_nonaktif = Pelanggan::where('status', 'nonaktif')->count();

    
        $tagihanBulanIni = Tagihan::where('status', 'LS')
            ->whereYear('created_at', Carbon::now()->year)
            ->whereMonth('created_at', Carbon::now()->month)
            ->sum('tagihan');

        // Ambil semua ID pelanggan dengan status aktif
        // $activePelangganIds = Pelanggan::where('status', 'aktif')->pluck('id_pelanggan');

        // // Hitung jumlah pelanggan lunas dengan status aktif
        // $jumlah_pelanggan_lunas = 0;
        // if ($activePelangganIds->isNotEmpty()) {
        //     $jumlah_pelanggan_lunas = Pelanggan::whereIn('id_pelanggan', $activePelangganIds)
        //         ->whereHas('tagihan', function ($query) {
        //             $query->where('status', 'LS')
        //                 ->whereYear('tgl_bayar', Carbon::now()->year)
        //                 ->whereMonth('tgl_bayar', Carbon::now()->month);
        //         })->count();
        // }

        // // Hitung jumlah pelanggan belum lunas dengan status aktif
        // $jumlah_pelanggan_belum_lunas = 0;
        // if ($activePelangganIds->isNotEmpty()) {
        //     $jumlah_pelanggan_belum_lunas = Pelanggan::whereIn('id_pelanggan', $activePelangganIds)
        //         ->whereDoesntHave('tagihan', function ($query) {
        //             $query->where('status', 'LS')
        //                 ->whereYear('tgl_bayar', Carbon::now()->year)
        //                 ->whereMonth('tgl_bayar', Carbon::now()->month);
        //         })->count();
        // }

        $activePelangganIds = Pelanggan::where('status', 'aktif')->pluck('id_pelanggan');

        // Tentukan tahun dan bulan saat ini
        $now = Carbon::now();
        $year = $now->year;
        $month = $now->month;
        
        // Hitung jumlah pelanggan lunas dengan status aktif
        $jumlah_pelanggan_lunas = Pelanggan::whereIn('id_pelanggan', $activePelangganIds)
            ->whereHas('tagihan', function ($query) use ($year, $month) {
                $query->where('status', 'LS')
                      ->whereYear('created_at', $year)
                      ->whereMonth('created_at', $month);
            })->count();
        
        // Hitung jumlah pelanggan belum lunas dengan status aktif
        $jumlah_pelanggan_belum_lunas = Pelanggan::whereIn('id_pelanggan', $activePelangganIds)
            ->whereHas('tagihan', function ($query) use ($year, $month) {
                // Gunakan `created_at` untuk menentukan apakah tagihan dibuat di bulan dan tahun saat ini
                $query->where(function ($query) use ($year, $month) {
                    $query->where('status', '!=', 'LS')
                          ->orWhereNull('status');
                })->whereYear('created_at', $year)
                  ->whereMonth('created_at', $month);
            })->count();


        // $activePelangganIds = Pelanggan::where('status', 'aktif')->pluck('id_pelanggan');

        // // Tentukan tahun dan bulan saat ini
        // $now = Carbon::now();
        // $year = $now->year;
        // $month = $now->month;

        // // Hitung jumlah pelanggan lunas dengan status aktif
        // $jumlah_pelanggan_lunas = Pelanggan::whereIn('id_pelanggan', $activePelangganIds)
        //     ->whereHas('tagihan', function ($query) use ($year, $month) {
        //         $query->where('status', 'LS')
        //             ->whereYear('tgl_bayar', $year)
        //             ->whereMonth('tgl_bayar', $month);
        //     })->count();

        // // Hitung jumlah pelanggan belum lunas dengan status aktif
        // $jumlah_pelanggan_belum_lunas = Pelanggan::whereIn('id_pelanggan', $activePelangganIds)
        //     ->whereDoesntHave('tagihan', function ($query) use ($year, $month) {
        //         $query->where('status', 'LS')
        //             ->whereYear('tgl_bayar', $year)
        //             ->whereMonth('tgl_bayar', $month);
        //     })
        //     ->orWhereHas('tagihan', function ($query) use ($year, $month) {
        //         $query->where('status', '!=', 'LS')
        //             ->orWhereNull('status');
        //     })->count();

    
        // Mengirimkan variabel $dataForChart ke view
        return view('dashboard', compact('jumlah_paket', 'tagihanBulanIni', 'jumlah_pelanggan_lunas', 'jumlah_pelanggan_belum_lunas', 'jumlah_pelanggan_aktif', 'jumlah_pelanggan_nonaktif'));
    }
    
}
