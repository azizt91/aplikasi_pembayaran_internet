<?php

namespace App\Http\Controllers;

use App\Models\Bulan;
use App\Models\Pelanggan;
use App\Models\Tagihan;
use App\Models\Paket;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Dompdf\Dompdf;
use PDF;

class TagihanController extends Controller
{
    public function index()
    {
        $bulanList = Bulan::all();
        $pelangganList = Pelanggan::where('status', 'aktif')->get();

        return view('tagihan.index', compact('bulanList', 'pelangganList'));
    }

    // public function storeTagihan(Request $request)
    // {
    //     $request->validate([
    //         'bulan' => 'required',
    //         'tahun' => 'required',
    //         'id_pelanggan' => 'required|array',
    //         'tarif' => 'required',
    //     ]);

    //     // Get the necessary data from the request
    //     $bulan = $request->bulan;
    //     $tahun = $request->tahun;
    //     $tarif = (int) str_replace(['Rp ', '.'], '', $request->tarif);

    //     try {
    //         // Loop through id_pelanggan array and create a Tagihan for each
    //         foreach ($request->id_pelanggan as $id_pelanggan) {
    //             // Create a new Tagihan instance for each id_pelanggan
    //             $tagihan = new Tagihan([
    //                 'bulan' => $bulan,
    //                 'tahun' => $tahun,
    //                 'id_pelanggan' => $id_pelanggan,
    //                 'tagihan' => $tarif,
    //                 'status' => 'BL',
    //             ]);

    //             // Save the Tagihan
    //             $tagihan->save();
    //         }

    //         // Display success alert
    //         Alert::success('Success', 'Tagihan berhasil disimpan');
    //     } catch (\Exception $e) {
    //         // Display error alert
    //         Alert::error('Error', 'Tagihan gagal disimpan');
    //     }

    //     // Redirect or respond as needed
    //     return redirect()->route('buka-tagihan');
    // }

//     public function storeTagihan(Request $request)
// {
//     $request->validate([
//         'bulan' => 'required',
//         'tahun' => 'required',
//         'id_pelanggan' => 'required|array',
//         'tarif' => 'required',
//     ]);

//     // Dapatkan data yang diperlukan dari request
//     $bulan = $request->bulan;
//     $tahun = $request->tahun;
//     $tarif = (int) str_replace(['Rp ', '.'], '', $request->tarif);

//     try {
//         // Loop melalui array id_pelanggan dan buat Tagihan untuk masing-masing
//         foreach ($request->id_pelanggan as $id_pelanggan) {
//             // Periksa apakah pelanggan yang dipilih masih aktif
//             $pelanggan = Pelanggan::findOrFail($id_pelanggan);
//             if ($pelanggan->status == 'aktif') {
//                 // Buat instance Tagihan baru untuk setiap id_pelanggan
//                 $tagihan = new Tagihan([
//                     'bulan' => $bulan,
//                     'tahun' => $tahun,
//                     'id_pelanggan' => $id_pelanggan,
//                     'tagihan' => $tarif,
//                     'status' => 'BL',
//                 ]);

//                 // Simpan Tagihan
//                 $tagihan->save();
//             }
//         }

//         // Tampilkan pesan sukses
//         Alert::success('Sukses', 'Tagihan berhasil disimpan');
//     } catch (\Exception $e) {
//         // Tampilkan pesan error
//         Alert::error('Error', 'Tagihan gagal disimpan');
//     }

//     // Redirect atau berikan respons sesuai kebutuhan
//     return redirect()->route('buka-tagihan');
// }

public function storeTagihan(Request $request)
{
    $request->validate([
        'bulan' => 'required',
        'tahun' => 'required',
        'id_pelanggan' => 'required|array',
    ]);

    $bulan = $request->bulan;
    $tahun = $request->tahun;

    try {
        foreach ($request->id_pelanggan as $id_pelanggan) {
            $pelanggan = Pelanggan::with('paket')->findOrFail($id_pelanggan);
            if ($pelanggan->status == 'aktif') {
                // Ambil tarif dari relasi paket yang dimiliki pelanggan
                $tarifPelanggan = $pelanggan->paket->tarif;

                $tagihan = new Tagihan([
                    'bulan' => $bulan,
                    'tahun' => $tahun,
                    'id_pelanggan' => $id_pelanggan,
                    'tagihan' => $tarifPelanggan,
                    'status' => 'BL',
                ]);

                $tagihan->save();
            }
        }

        Alert::success('Sukses', 'Tagihan berhasil disimpan');
    } catch (\Exception $e) {
        Alert::error('Error', 'Tagihan gagal disimpan');
    }

    return redirect()->route('buka-tagihan');
}


    public function bukaTagihan()
    {
        // Fetch the list of months and years
        $bulanList = Bulan::all();
        $tahunList = range(date('Y'), date('Y') + 5);

        $pelangganList = Pelanggan::where('status', 'aktif')->get();

        return view('tagihan.buka-tagihan', compact('bulanList', 'tahunList'));
    }

    public function dataTagihan(Request $request)
    {
        $request->validate([
            'bulan' => 'required',
            'tahun' => 'required',
        ]);

        $bulan = $request->input('bulan');
        $tahun = $request->input('tahun');

        // Assuming you have a method to fetch data based on the month, year, and status
        $tagihanList = Tagihan::getDataByMonthYearAndStatus($bulan, $tahun, 'BL');

        return view('tagihan.data-tagihan', compact('tagihanList', 'bulan', 'tahun'));
    }

    public function bayarTagihan($kode)
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
        $tagihan->save();

        Alert::success('Sukses', 'Pembayaran tagihan berhasil');
        return redirect()->route('lunas-tagihan');
    }

    public function lunasTagihan()
    {
        
        return view('tagihan.lunas-tagihan');

    }

    public function cetakStruk($id)
    {
    // Temukan tagihan berdasarkan ID
    $tagihan = Tagihan::find($id);

    // Pastikan tagihan ditemukan
    if (!$tagihan) {
        return redirect()->route('buka-tagihan')->with('error', 'Tagihan tidak ditemukan');
    }

    // Generate PDF
    $pdf = PDF::loadView('tagihan.cetak-struk', compact('tagihan'));

    // Download atau tampilkan PDF
    return $pdf->download('struk_pembayaran.pdf');
    
    }

    public function generatePdf()
    {
    $dompdf = new Dompdf();

    // Set base path for DOMPDF
    $dompdf->setBasePath(public_path());

    // Load HTML content with image
    $html = '<img src="' . base_path('public/template/img/sn.png') . '" alt="Logo">';

    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');

    // Render the PDF
    $dompdf->render();

    // Output the generated PDF
    return $dompdf->stream();
    }

    public function lunas()
    {
        $bulanIni = Carbon::now()->month;
        $tahunIni = Carbon::now()->year;

        $pelangganLunas = Pelanggan::where('status', 'aktif')
            ->whereHas('tagihan', function ($query) use ($bulanIni, $tahunIni) {
                $query->where('status', 'LS')
                      ->whereMonth('created_at', $bulanIni)
                      ->whereYear('created_at', $tahunIni);
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
                  ->whereMonth('created_at', $bulanIni)
                  ->whereYear('created_at', $tahunIni);
        })
        ->orWhere(function ($query) use ($bulanIni, $tahunIni) {
            $query->whereHas('tagihan', function ($query) use ($bulanIni, $tahunIni) {
                $query->where('status', '!=', 'LS')
                      ->whereMonth('created_at', $bulanIni)
                      ->whereYear('created_at', $tahunIni);
            });
        })->get();

    return view('tagihan.belumLunas', compact('pelangganBelumLunas'));
}


}

