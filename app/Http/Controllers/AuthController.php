<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use RealRashid\SweetAlert\Facades\Alert;


class AuthController extends Controller
{
    public function register()
	{
		return view('auth/register');
	}

	public function registerSimpan(Request $request)
	{
		Validator::make($request->all(), [
			'nama' => 'required',
			'email' => 'required|email',
			'password' => 'required|confirmed'
		])->validate();

		User::create([
			'nama' => $request->nama,
			'email' => $request->email,
			'password' => Hash::make($request->password),
			'level' => 'User'
		]);

		return redirect()->route('login');
	}

	public function login()
	{
		return view('auth/login');
	}

	public function loginAksi(Request $request)
	{
		Validator::make($request->all(), [
			'email' => 'required|email',
			'password' => 'required'
		])->validate();
		
		// Cek apakah user adalah Admin (di tabel users)
		$user = User::where('email', $request->email)->first();
		
		if ($user && Hash::check($request->password, $user->password)) {
			// Login sebagai Admin
			Auth::guard('web')->login($user, $request->boolean('remember'));
			$request->session()->regenerate();
			Alert::success('Berhasil', 'Login sebagai Admin');
			return redirect()->route('dashboard');
		}
		
		// Cek apakah user adalah Pelanggan (di tabel pelanggan)
		$pelanggan = \App\Models\Pelanggan::where('email', $request->email)->first();
		
		if ($pelanggan && $pelanggan->password === $request->password) {
			// Login sebagai Pelanggan
			Auth::guard('pelanggan')->login($pelanggan, $request->boolean('remember'));
			$request->session()->regenerate();
			Alert::success('Berhasil', 'Login sebagai Pelanggan');
			return redirect()->route('dashboard-pelanggan');
		}
		
		// Jika tidak ditemukan di kedua tabel
		throw ValidationException::withMessages([
			'email' => 'Email atau password salah'
		]);
	}

	public function logout(Request $request)
	{
		// Logout dari guard yang aktif
		if (Auth::guard('web')->check()) {
			Auth::guard('web')->logout();
		}
		
		if (Auth::guard('pelanggan')->check()) {
			Auth::guard('pelanggan')->logout();
		}
		
		$request->session()->invalidate();
		$request->session()->regenerateToken();
		
		Alert::success('Berhasil', 'Anda telah logout');
		return redirect()->route('login');
	}


		// Metode API
	
		public function registerApi(Request $request)
		{
			$validator = Validator::make($request->all(), [
				'nama' => 'required',
				'email' => 'required|email|unique:users,email',
				'password' => 'required|confirmed'
			]);
	
			if ($validator->fails()) {
				return response()->json(['errors' => $validator->errors()], 422);
			}
	
			$user = User::create([
				'nama' => $request->nama,
				'email' => $request->email,
				'password' => Hash::make($request->password),
				'level' => 'User'
			]);
	
			return response()->json(['user' => $user], 201);
		}
	
		public function loginApi(Request $request)
		{
			$validator = Validator::make($request->all(), [
				'email' => 'required|email',
				'password' => 'required'
			]);
	
			if ($validator->fails()) {
				return response()->json(['errors' => $validator->errors()], 422);
			}
	
			if (!Auth::attempt($request->only('email', 'password'))) {
				return response()->json(['message' => 'Unauthorized'], 401);
			}
	
			$user = Auth::user();
			$token = $user->createToken('auth_token')->plainTextToken;
	
			return response()->json(['token' => $token, 'user' => $user], 200);
		}
	
		public function logoutApi(Request $request)
		{
			$request->user()->tokens()->delete();
			return response()->json(['message' => 'Logged out'], 200);
		}

}
