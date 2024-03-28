<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Paket;
use App\Models\Pelanggan;
use App\Models\Dashboard;
use RealRashid\SweetAlert\Facades\Alert;
use Carbon\Carbon;


class PelangganController extends Controller
{

	public function index()
	{

		$pelanggan = pelanggan::paginate('100');
		return view('pelanggan.index', compact('pelanggan'));
	
	}

	public function tambah()
	{
		$paket = Paket::get();
		$status = ['aktif', 'nonaktif'];
		return view('pelanggan.form', compact('paket', 'status'));
	}

	public function simpan(Request $request)
	{

		$pass_acak = mt_rand(1000, 9999);

		$data = [
			'id_pelanggan' => $request->id_pelanggan,
			'nama' => $request->nama,
			'alamat' => $request->alamat,
			'whatsapp' => $request->whatsapp,
			'email' => $request->email,
			'password' => $pass_acak,
			'level' => 'User',
			'id_paket' => $request->id_paket,
			'jatuh_tempo' => $request->jatuh_tempo,
			'status' => $request->status,

		];

		Pelanggan::create($data);
		Alert::toast('Data berhasil disimpan','success');
		return redirect()->route('pelanggan');
	}

	public function edit($id_pelanggan)
    {
        $pelanggan = Pelanggan::find($id_pelanggan);
        $paket = Paket::get();
        $status = ['aktif', 'nonaktif'];
    	return view('pelanggan.form', compact('pelanggan', 'paket', 'status'));
    }

    public function update($id_pelanggan, Request $request)
    {
        $data = [
            'id_pelanggan' => $request->id_pelanggan,
            'nama' => $request->nama,
            'alamat' => $request->alamat,
            'whatsapp' => $request->whatsapp,
            'email' => $request->email,
            'id_paket' => $request->id_paket,
            'jatuh_tempo' => $request->jatuh_tempo,
			'status' => $request->status,
        ];

        Pelanggan::where('id_pelanggan', $id_pelanggan)->update($data);
        Alert::toast('Data berhasil diedit', 'success');
        return redirect()->route('pelanggan');
    }


	public function hapus($id_pelanggan)
	{

	$pelanggan = Pelanggan::find($id_pelanggan);

    if ($pelanggan) {
        $pelanggan->delete();
        Alert::toast('Data Berhasil Dihapus','success');
    } else {
        Alert::toast('Data tidak ditemukan','error');
    }

    return redirect()->route('pelanggan');
	}
	
	public function showDashboard()
	{
		$jumlah_pelanggan = Pelanggan::count();
		return view('dashboard', compact('jumlah_pelanggan'));
	}

	public function show($id_pelanggan)
    {
        $pelanggan = Pelanggan::findOrFail($id_pelanggan);
        $tagihanBelumLunas = $pelanggan->tagihan()->where('status', 'BL')->get();
        
        return view('pelanggan.detail', compact('pelanggan', 'tagihanBelumLunas'));
    }

	public function profile($id_pelanggan)
	{
	$pelanggan = Pelanggan::findOrFail($id_pelanggan);
	return view ('pelanggan.profile', compact('pelanggan'));
	}

	

}
