<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;

class MobileDashboardController extends Controller
{
    /**
     * Get dashboard data
     */
    public function index(Request $request)
    {
        // Check if user is authenticated
        $pelanggan = $request->user();
        
        if (!$pelanggan) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated. Token tidak valid atau sudah expired.',
            ], 401);
        }
        
        // Load paket relationship
        $pelanggan->load('paket');
        
        // Get current month bill
        $bulanSekarang = Carbon::now()->month;
        $tahunSekarang = Carbon::now()->year;
        
        $tagihanBulanIni = $pelanggan->tagihan()
            ->where('bulan', $bulanSekarang)
            ->where('tahun', $tahunSekarang)
            ->first();
        
        // Get unpaid bills summary
        $tagihanBelumLunas = $pelanggan->tagihan()
            ->where('status', 'BL')
            ->get();
        
        $jumlahTagihanBelumLunas = $tagihanBelumLunas->count();
        $totalTagihanBelumLunas = $tagihanBelumLunas->sum('tagihan');
        
        // Get paid bills summary
        $tagihanLunas = $pelanggan->tagihan()
            ->where('status', 'LS')
            ->get();
        
        $jumlahTagihanLunas = $tagihanLunas->count();
        $totalTagihanLunas = $tagihanLunas->sum('tagihan');
        
        // Get latest unpaid bill
        $tagihanTerbaru = $pelanggan->tagihan()
            ->where('status', 'BL')
            ->orderBy('tahun', 'desc')
            ->orderBy('bulan', 'desc')
            ->first();
        
        // Format current month bill data
        $currentMonthBill = null;
        if ($tagihanBulanIni) {
            $namaBulan = Carbon::create()->month($tagihanBulanIni->bulan)->translatedFormat('F');
            $currentMonthBill = [
                'id' => $tagihanBulanIni->id,
                'bulan' => $tagihanBulanIni->bulan,
                'tahun' => $tagihanBulanIni->tahun,
                'bulan_tahun' => $namaBulan . ' ' . $tagihanBulanIni->tahun,
                'tagihan' => $tagihanBulanIni->tagihan,
                'status' => $tagihanBulanIni->status,
                'status_text' => $tagihanBulanIni->status === 'BL' ? 'Belum Dibayar' : 'Sudah Dibayar',
                'tgl_bayar' => $tagihanBulanIni->tgl_bayar,
                'pembayaran_via' => $tagihanBulanIni->pembayaran_via,
            ];
        }
        
        return response()->json([
            'success' => true,
            'data' => [
                'pelanggan' => [
                    'id_pelanggan' => $pelanggan->id_pelanggan,
                    'nama' => $pelanggan->nama,
                    'email' => $pelanggan->email,
                    'whatsapp' => $pelanggan->whatsapp,
                    'alamat' => $pelanggan->alamat,
                    'status' => $pelanggan->status,
                    'ip_address' => $pelanggan->ip_address,
                    'profile_picture' => $pelanggan->profile_picture 
                        ? route('api.mobile.profile.photo', ['filename' => basename($pelanggan->profile_picture)])
                        : null,
                ],
                'paket' => $pelanggan->paket ? [
                    'nama_paket' => $pelanggan->paket->paket,
                    'harga' => $pelanggan->paket->tarif,
                ] : null,
                'current_month_bill' => $currentMonthBill,
                'summary' => [
                    'unpaid' => [
                        'count' => $jumlahTagihanBelumLunas,
                        'total' => $totalTagihanBelumLunas,
                    ],
                    'paid' => [
                        'count' => $jumlahTagihanLunas,
                        'total' => $totalTagihanLunas,
                    ],
                ],
                'latest_unpaid_bill' => $tagihanTerbaru ? [
                    'id' => $tagihanTerbaru->id,
                    'bulan' => $tagihanTerbaru->bulan,
                    'tahun' => $tagihanTerbaru->tahun,
                    'tagihan' => $tagihanTerbaru->tagihan,
                ] : null,
            ]
        ]);
    }
}
