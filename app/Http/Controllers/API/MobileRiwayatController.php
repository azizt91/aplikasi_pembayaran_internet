<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;

class MobileRiwayatController extends Controller
{
    /**
     * Get payment history (paid bills)
     */
    public function index(Request $request)
    {
        $pelanggan = $request->user();
        
        $limit = $request->query('limit', 20);
        
        $riwayat = $pelanggan->tagihan()
            ->where('status', 'LS')
            ->orderBy('tgl_bayar', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($item) {
                $namaBulan = Carbon::create()->month($item->bulan)->translatedFormat('F');
                
                return [
                    'id' => $item->id,
                    'bulan' => $item->bulan,
                    'tahun' => $item->tahun,
                    'bulan_tahun' => $namaBulan . ' ' . $item->tahun,
                    'tagihan' => $item->tagihan,
                    'tgl_bayar' => $item->tgl_bayar,
                    'pembayaran_via' => $item->pembayaran_via,
                    'created_at' => $item->created_at,
                ];
            });
        
        return response()->json([
            'success' => true,
            'data' => $riwayat
        ]);
    }
}
