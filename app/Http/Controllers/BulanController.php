<?php

namespace App\Http\Controllers;
use App\Models\Bulan;
use Illuminate\Http\Request;

class BulanController extends Controller
{
    public function index()
    {
        // Mengambil semua data bulan dari tabel bulan
        $bulanList = [
            ['id' => 1, 'bulan' => 'Januari'],
            ['id' => 2, 'bulan' => 'Februari'],
            ['id' => 3, 'bulan' => 'Maret'],
            ['id' => 4, 'bulan' => 'April'],
            ['id' => 5, 'bulan' => 'Mei'],
            ['id' => 6, 'bulan' => 'Juni'],
            ['id' => 7, 'bulan' => 'Juli'],
            ['id' => 8, 'bulan' => 'Agustus'],
            ['id' => 9, 'bulan' => 'September'],
            ['id' => 10, 'bulan' => 'Oktober'],
            ['id' => 11, 'bulan' => 'November'],
            ['id' => 12, 'bulan' => 'Desember'],
            // ... tambahkan bulan lainnya sesuai kebutuhan
        ];
        return view('buat-tagihan/index', compact('bulanList'));
    }
}
