<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;

class MobileTagihanController extends Controller
{
    /**
     * Get all bills
     */
    public function index(Request $request)
    {
        $pelanggan = $request->user();
        
        $status = $request->query('status'); // 'BL' or 'LS'
        $limit = $request->query('limit', 10);
        
        $query = $pelanggan->tagihan()
            ->orderBy('tahun', 'desc')
            ->orderBy('bulan', 'desc');
        
        if ($status) {
            $query->where('status', $status);
        }
        
        $tagihan = $query->limit($limit)->get()->map(function ($item) {
            $namaBulan = Carbon::create()->month($item->bulan)->translatedFormat('F');
            
            return [
                'id' => $item->id,
                'bulan' => $item->bulan,
                'tahun' => $item->tahun,
                'bulan_tahun' => $namaBulan . ' ' . $item->tahun,
                'tagihan' => $item->tagihan,
                'status' => $item->status,
                'status_text' => $item->status === 'BL' ? 'Belum Dibayar' : 'Sudah Dibayar',
                'tgl_bayar' => $item->tgl_bayar,
                'pembayaran_via' => $item->pembayaran_via,
                'created_at' => $item->created_at,
            ];
        });
        
        return response()->json([
            'success' => true,
            'data' => $tagihan
        ]);
    }
    
    /**
     * Get single bill detail
     */
    public function show(Request $request, $id)
    {
        $pelanggan = $request->user();
        
        $tagihan = $pelanggan->tagihan()->find($id);
        
        if (!$tagihan) {
            return response()->json([
                'success' => false,
                'message' => 'Tagihan tidak ditemukan',
            ], 404);
        }
        
        $namaBulan = Carbon::create()->month($tagihan->bulan)->translatedFormat('F');
        
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $tagihan->id,
                'bulan' => $tagihan->bulan,
                'tahun' => $tagihan->tahun,
                'bulan_tahun' => $namaBulan . ' ' . $tagihan->tahun,
                'tagihan' => $tagihan->tagihan,
                'status' => $tagihan->status,
                'status_text' => $tagihan->status === 'BL' ? 'Belum Dibayar' : 'Sudah Dibayar',
                'tgl_bayar' => $tagihan->tgl_bayar,
                'pembayaran_via' => $tagihan->pembayaran_via,
                'created_at' => $tagihan->created_at,
                'pelanggan' => [
                    'id_pelanggan' => $pelanggan->id_pelanggan,
                    'nama' => $pelanggan->nama,
                    'alamat' => $pelanggan->alamat,
                    'no_hp' => $pelanggan->no_hp,
                ],
            ]
        ]);
    }
    
    /**
     * Get payment banks
     */
    public function paymentMethods()
    {
        $banks = \App\Models\Bank::where('is_active', true)->get()->map(function ($bank) {
            return [
                'id' => $bank->id,
                'nama_bank' => $bank->nama_bank,
                'pemilik_rekening' => $bank->pemilik_rekening,
                'nomor_rekening' => $bank->nomor_rekening,
                'url_icon' => asset($bank->url_icon),
            ];
        });
        
        return response()->json([
            'success' => true,
            'data' => $banks
        ]);
    }
}
