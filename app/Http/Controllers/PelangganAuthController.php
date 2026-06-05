<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Payment\TripayController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use RealRashid\SweetAlert\Facades\Alert;
use App\Models\Tagihan;
use App\Models\Pelanggan;
use App\Models\Bank;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cookie;


class PelangganAuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.pelanggan-login');
    }
    
    public function login(Request $request)
    {
        // Mendapatkan nilai email dari cookie jika tersedia
        $rememberedEmail = $request->cookie('remembered_email');
        
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        
        // Cari pengguna berdasarkan alamat email
        $pelanggan = Pelanggan::where('email', $request->email)->first();
        
        // Periksa apakah pengguna ditemukan dan password sesuai
        if ($pelanggan) {
            // Jika sesuai, verifikasi password
            if ($pelanggan->password === $request->password) {
                // Lakukan proses login
                Auth::guard('pelanggan')->login($pelanggan);
                
                // Membersihkan cookie yang menyimpan nilai email setelah berhasil login
                return redirect()->route('dashboard-pelanggan')
                ->withCookie(Cookie::forget('remembered_email'));
            }
        }
        
        // Jika tidak sesuai, kembalikan ke halaman login dengan pesan error
        return redirect()->route('login')
        ->withErrors(['email' => 'These credentials do not match our records.'])
        ->withInput($request->only('email'))
        // Mengatur cookie yang menyimpan nilai email
        ->withCookie(cookie()->forever('remembered_email', $rememberedEmail));
    }
    
    
    
    public function dashboard()
    {
        // Ambil data pelanggan yang sedang login
        $pelanggan = Auth::guard('pelanggan')->user();
        
        // Ambil semua tagihan
        $tagihanBelumLunas = $pelanggan->tagihan()->where('status', 'BL')->get();
        $jumlahTagihanBelumLunas = $tagihanBelumLunas->count();
        $totalTagihanBelumLunas = $tagihanBelumLunas->sum('tagihan');
        
        $tagihanLunas = $pelanggan->tagihan()->where('status', 'LS')->get();
        $jumlahTagihanLunas = $tagihanLunas->count();
        $totalTagihanLunas = $tagihanLunas->sum('tagihan');
        
        // Ambil bulan dan tahun sekarang
        $bulanSekarang = Carbon::now()->month; // 1-12
        $tahunSekarang = Carbon::now()->year;
        
        // Ambil tagihan bulan ini berdasarkan kolom bulan dan tahun
        $tagihanBulanIni = $pelanggan->tagihan()
            ->where('bulan', $bulanSekarang)
            ->where('tahun', $tahunSekarang)
            ->first();
        
        // Ambil tagihan terbaru yang belum lunas
        $tagihanTerbaru = $pelanggan->tagihan()
            ->where('status', 'BL')
            ->orderBy('tahun', 'desc')
            ->orderBy('bulan', 'desc')
            ->first();
        
        // Data untuk card tagihan bulan ini
        if ($tagihanBulanIni) {
            $statusTagihan = $tagihanBulanIni->status;
            $nominalTagihanBulanIni = $tagihanBulanIni->tagihan;
            $idTagihanBulanIni = $tagihanBulanIni->id;
            // Format bulan dari ID bulan (1-12) ke nama bulan
            $namaBulan = Carbon::create()->month($tagihanBulanIni->bulan)->translatedFormat('F');
            $bulanTagihan = $namaBulan . ' ' . $tagihanBulanIni->tahun;
            $tglBayar = $tagihanBulanIni->tgl_bayar ? Carbon::parse($tagihanBulanIni->tgl_bayar)->translatedFormat('d F Y') : '';
        } else {
            $statusTagihan = null;
            $nominalTagihanBulanIni = 0;
            $idTagihanBulanIni = null;
            $bulanTagihan = Carbon::now()->translatedFormat('F Y');
            $tglBayar = '';
        }
        
        // Info paket
        $paket = $pelanggan->paket;
        
        return view('dashboard-pelanggan', compact(
            'pelanggan', 
            'jumlahTagihanBelumLunas', 
            'totalTagihanBelumLunas',
            'jumlahTagihanLunas', 
            'totalTagihanLunas',
            'statusTagihan', 
            'nominalTagihanBulanIni',
            'idTagihanBulanIni',
            'bulanTagihan',
            'tglBayar',
            'tagihanTerbaru',
            'paket'
        ));
    }
    
    public function logout(Request $request)
    {
        // Mengambil email yang disimpan di dalam session
        $rememberedEmail = $request->session()->get('remembered_email');
        
        // Lakukan proses logout
        Auth::guard('pelanggan')->logout();
        
        // Invalidasi session dan hapus cookie autentikasi
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        // Redirect kembali ke halaman login atau halaman lain yang sesuai
        return redirect()->route('pelanggan.login')
        // Menyimpan email dalam cookie
        ->withCookie(cookie()->forever('remembered_email', $rememberedEmail));
    }
    
    public function belumLunas()
    {
        
        $pelanggan = Auth::guard('pelanggan')->user();
        $tagihanBelumLunas = $pelanggan->tagihan()->where('status', 'BL')->get();
        
        return view('tagihan.belum-lunas', compact('tagihanBelumLunas'));
    }
    
    public function sudahLunas()
    {
        $pelanggan = Auth::guard('pelanggan')->user();
        $tagihanSudahLunas = $pelanggan->tagihan()->where('status', 'LS')->orderBy('updated_at', 'desc')->get();
        return view('tagihan.sudah-lunas', compact('tagihanSudahLunas'));
    }
    
    public function riwayatPembayaran()
    {
        
        $pelanggan = Auth::guard('pelanggan')->user();
        
        $riwayatPembayaranLunas = $pelanggan->tagihan()->where('status', 'LS')->get();
        
        return view('tagihan.riwayat-pembayaran', compact('riwayatPembayaranLunas'));
    }
    
    public function invoicePembayaran($id)
    {
        $tagihan = Tagihan::findOrFail($id);
        $pelanggan = $tagihan->pelanggan;
        return view('tagihan.invoice-pembayaran', compact('tagihan', 'pelanggan'));
    }
    
    public function profile()
    {
        $pelanggan = Auth::guard('pelanggan')->user();
        return view ('pelanggan.profile', compact('pelanggan'));
    }
    
    public function editProfile()
    {
        $pelanggan = Auth::guard('pelanggan')->user();
        return view('pelanggan.profile', compact('pelanggan'));        
    }
    
    public function updateProfile(Request $request)
    {
        $pelanggan = Auth::guard('pelanggan')->user();
        
        // Validasi data yang dikirim
        $request->validate([
            'nama' => 'required|string|max:255',
            'alamat' => 'required|string|max:255',
            'whatsapp' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'password' => 'nullable|string|max:255',
            'profile_picture' => 'nullable|image|max:5000', // Maksimum 5 MB
        ]);
        
        // Update data profil pengguna
        $pelanggan->nama = $request->nama;
        $pelanggan->alamat = $request->alamat;
        $pelanggan->whatsapp = $request->whatsapp;
        $pelanggan->email = $request->email;
        
        // Jika ada kata sandi baru, perbarui kata sandi
        if ($request->filled('password')) {
            
            $pelanggan->password = $request->password;
        }
        
        // Jika ada gambar profil baru, unggah dan simpan
        if ($request->hasFile('profile_picture')) {
            $imagePath = $request->file('profile_picture')->store('profile_pictures', 'public');
            $pelanggan->profile_picture = $imagePath;
        }
        
        $pelanggan->save();
        Alert::success('Sukses', 'Data Profile berhasil di edit');
        return redirect()->route('profile');
        // back()->with('success', 'Profile updated successfully!');
    }
    
    
    public function uploadProfilePicture(Request $request)
    {
        // Validasi permintaan
        $request->validate([
            'profile_picture' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120', // Maksimum 5 MB
        ]);
        
        // Periksa apakah ada file yang diunggah
        if ($request->hasFile('profile_picture')) {
            // Dapatkan file yang diunggah
            $profilePicture = $request->file('profile_picture');
            
            // Simpan file ke folder penyimpanan yang diinginkan (contoh: storage/app/public/profile-pictures)
            $path = $profilePicture->store('profile-pictures', 'public');
            
            // Simpan jalur file ke basis data untuk pelanggan saat ini
            $pelanggan = Pelanggan::findOrFail(auth()->guard('pelanggan')->id()); // Sesuaikan dengan model dan penamaan kolom yang benar
            $pelanggan->profile_picture = $path;
            $pelanggan->save();
            
            Alert::success('Success', 'Profile picture uploaded successfully.');
            
            // Redirect ke halaman profil
            return redirect()->route('profile');
        } else {
            // Tampilkan Sweet Alert error
            Alert::error('Error', 'No file uploaded.');
            
            // Redirect kembali
            return back();
        }
    }
    

    public function showPaymentPage($id)
    {
        $tagihan = Tagihan::find($id);
        
        if (!$tagihan) {
            return redirect()->route('tagihan.belum_lunas')->with('error', 'Tagihan tidak ditemukan');
        }
        
        $apiKey = config('tripay.api_key');
        $tripay = new TripayController();
        $channels = $tripay->getPaymentChannels();
        
        if (!is_array($channels) && !is_object($channels)) {
            return redirect()->route('tagihan.belum_lunas')->with('error', 'Gagal mengambil data channel pembayaran');
        }

        $banks = Bank::all(); 
        
        return view('pelanggan.payment', compact('tagihan', 'channels', 'banks'));
    }
    
    public function showPembayaranPage()
    {
            // Ambil data bank
            $banks = Bank::all();

            // Kirim data bank ke view pembayaran
            return view('pelanggan.pembayaran', compact('banks'));
    }
    
    
    
    
}
