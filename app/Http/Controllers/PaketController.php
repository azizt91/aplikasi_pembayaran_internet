<?php

namespace App\Http\Controllers;

use App\Models\Paket;
use App\Models\Dashboard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Validator;

class PaketController extends Controller
{
    public function index()
	{
		$paket = Paket::get();
		return view('paket.index', ['data' => $paket]);
	}

	public function viewPaket()
    {
        $data = Paket::all();
        return view('paket.view', compact('data'));
    }
	
	public function tambah()
	{
		return view('paket.form');
	}
	public function simpan(Request $request)
	{
		// Validasi input
		$request->validate([
			'id_paket' => 'required|string|max:6',
			'paket' => 'required|string|max:20',
			'tarif' => 'required|integer|min:0',
		], [
			// Pesan error
			'id_paket.required' => 'ID Paket harus diisi',
			'id_paket.integer' => 'ID Paket harus berupa angka',
			'id_paket.min' => 'ID Paket minimal 1',
			'id_paket.max' => 'ID Paket maksimal 999999',
			'paket.required' => 'Paket harus diisi',
			'paket.string' => 'Paket harus berupa string',
			'paket.max' => 'Paket maksimal 20 karakter',
			'tarif.required' => 'Tarif harus diisi',
			'tarif.integer' => 'Tarif harus berupa angka',
			'tarif.min' => 'Tarif minimal 0',
		]);

		$data = [
			'id_paket' => $request->id_paket,
			'paket' => $request->paket,
			'tarif' => $request->tarif,
		];

		Paket::create($data);
		Alert::toast('Data berhasil disimpan','success');
		return redirect()->route('paket');
	}

	public function edit($id_paket)
	{
    $paket = Paket::find($id_paket);
    return view('paket.form', ['paket' => $paket]);
	}

public function update($id_paket, Request $request)
    {
        // Validate input
        $validator = Validator::make($request->all(), [
            'paket' => 'required|string|max:20',
            'tarif' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Retrieve the existing record
        $paket = Paket::find($id_paket);

        // Check if the record exists
        if (!$paket) {
            return redirect()->back()->with('error', 'Paket not found');
        }

        // Update the data
        $paket->update([
            'paket' => $request->paket,
            'tarif' => $request->tarif,
        ]);

        Alert::toast('Paket berhasil di edit','success');
        return redirect()->route('paket');
    }


	public function hapus($id_paket)
	{

	$paket = Paket::find($id_paket);

    if ($paket) {
        $paket->delete();
        Alert::toast('Data Berhasil Dihapus','success');
    } else {
        Alert::toast('Data tidak ditemukan','error');
    }

    return redirect()->route('paket');
	}

	public function showDashboard()
	{
    // Contoh: Ambil jumlah paket dari model atau database
    $jumlah_paket = Paket::count();

    return view('dashboard', compact('jumlah_paket'));
	}

}
