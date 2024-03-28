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
		
		if (!Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
			throw ValidationException::withMessages([
				'email' => trans('auth.failed')
			]);
		}
		
		// Alert::success('Anda Berhasil Login');
		$request->session()->regenerate();
		return redirect()->route('dashboard');
	}

	public function logout(Request $request)
	{
		Auth::guard('web')->logout();
		$request->session()->invalidate();
		return redirect('/');
	}
}
