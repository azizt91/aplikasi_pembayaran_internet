<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Paket;
use App\Models\Pelanggan;
use App\Models\Dashboard;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

use Carbon\Carbon;


class PelangganController extends Controller
{

	public function index()
	{
        $pelanggan = Pelanggan::with('paket')->paginate(1000);
        return view('pelanggan.index', compact('pelanggan'));

	}

	public function aktif()
	{
		$pelanggan = Pelanggan::where('status', 'aktif')->get();
		return view('pelanggan.aktif', compact('pelanggan'));
	}

	public function nonaktif()
	{
		$pelanggan = Pelanggan::where('status', 'nonaktif')->get();
		return view('pelanggan.nonaktif', compact('pelanggan'));
	}

	public function tambah()
	{
		$paket = Paket::get();
		$status = ['aktif', 'nonaktif'];

        // Auto-generate ID Pelanggan (IDxxx)
        $lastPelanggan = Pelanggan::orderBy('id', 'desc')->first();
        if ($lastPelanggan) {
            $lastId = intval(substr($lastPelanggan->id_pelanggan, 2));
            $nextId = 'ID' . sprintf('%03d', $lastId + 1);
        } else {
            $nextId = 'ID001';
        }

        // Prediksi ID Auto Increment untuk tampilan Email
        $nextAutoId = Pelanggan::max('id') + 1;

		return view('pelanggan.form', compact('paket', 'status', 'nextId', 'nextAutoId'));
	}

	public function simpan(Request $request)
	{
        // Password default 'password'
		$password = 'password';
        
        // Email sementara (akan diupdate setelah create)
        $tempEmail = 'temp_' . uniqid() . '@gmail.com';

		$data = [
			'id_pelanggan' => $request->id_pelanggan,
			'nama' => $request->nama,
			'alamat' => $request->alamat,
			'whatsapp' => $request->whatsapp,
			'email' => $tempEmail,
			'password' => $password,
			'password_hash' => Hash::make($password),
			'level' => 'User',
			'id_paket' => $request->id_paket,
			'ip_address' => $request->ip_address,
			'status' => $request->status,

		];

		$pelanggan = Pelanggan::create($data);

        // Update email sesuai ID yang baru saja dibuat
        $pelanggan->update([
            'email' => 'user' . $pelanggan->id . '@gmail.com'
        ]);

		Alert::toast('Data berhasil disimpan','success');
		return redirect()->route('pelanggan');
	}

	public function edit($id)
	{
		$pelanggan = Pelanggan::find($id);
		$paket = Paket::get();
		$status = ['aktif', 'nonaktif'];
		return view('pelanggan.form', compact('pelanggan', 'paket', 'status'));
	}

	public function update($id, Request $request)
	{
		$data = [
			'id_pelanggan' => $request->id_pelanggan,
			'nama' => $request->nama,
			'alamat' => $request->alamat,
			'whatsapp' => $request->whatsapp,
			'id_paket' => $request->id_paket,
			'ip_address' => $request->ip_address,
			'status' => $request->status,
		];

		Pelanggan::find($id)->update($data);
		Alert::toast('Data berhasil diedit', 'success');
		return redirect()->route('pelanggan');
	}

    public function hapus($id)
    {
        // Pengecekan Akses (Hanya useradmin)

        $pelanggan = Pelanggan::find($id);
    
        if ($pelanggan) {
            // 1. Hapus data yang terkait di tabel tagihan
            foreach ($pelanggan->tagihan as $tagihan) {
                $tagihan->delete();
            }
            
            // 2. Antisipasi: Hapus riwayat perubahan WiFi jika model dan tabelnya ada
            if (class_exists(\App\Models\WiFiChangeHistory::class)) {
                \App\Models\WiFiChangeHistory::where('id_pelanggan', $pelanggan->id_pelanggan)->delete();
            }

            // 3. Hapus data di tabel wifi_settings menggunakan Model yang sudah Anda buat
            \App\Models\WiFiSettings::where('id_pelanggan', $pelanggan->id_pelanggan)->delete();
    
            // 4. Setelah semua relasi dihapus, barulah hapus data pelanggan
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

	public function show($id)
	{
		$pelanggan = Pelanggan::findOrFail($id);
		$tagihanBelumLunas = $pelanggan->tagihan()->where('status', 'BL')->get();

		return view('pelanggan.detail', compact('pelanggan', 'tagihanBelumLunas'));
	}

	public function profile($id)
	{
		$pelanggan = Pelanggan::findOrFail($id);
		return view ('pelanggan.profile', compact('pelanggan'));
	}



}




    


