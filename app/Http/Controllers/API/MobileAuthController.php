<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Pelanggan;

class MobileAuthController extends Controller
{
    /**
     * Login customer
     * Note: Password pelanggan disimpan plain text (tidak di-hash)
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        // Find pelanggan by email
        $pelanggan = Pelanggan::where('email', $request->email)->first();

        // Check if pelanggan exists and password matches (plain text comparison)
        if (!$pelanggan || $pelanggan->password !== $request->password) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau password salah',
            ], 401);
        }

        // Check if account is active
        if ($pelanggan->status !== 'aktif') {
            return response()->json([
                'success' => false,
                'message' => 'Akun Anda tidak aktif. Silakan hubungi admin.',
            ], 403);
        }

        // Create token
        $token = $pelanggan->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil',
            'data' => [
                'token' => $token,
                'pelanggan' => [
                    'id' => $pelanggan->id,
                    'id_pelanggan' => $pelanggan->id_pelanggan,
                    'nama' => $pelanggan->nama,
                    'email' => $pelanggan->email,
                    'alamat' => $pelanggan->alamat,
                    'whatsapp' => $pelanggan->whatsapp,
                    'status' => $pelanggan->status,
                    'ip_address' => $pelanggan->ip_address,
                    'paket' => $pelanggan->paket ? [
                        'id_paket' => $pelanggan->paket->id_paket,
                        'paket' => $pelanggan->paket->paket,
                        'tarif' => $pelanggan->paket->tarif,
                    ] : null,
                ]
            ]
        ]);
    }

    /**
     * Logout customer
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil',
        ]);
    }

    /**
     * Get authenticated user
     */
    public function me(Request $request)
    {
        $pelanggan = $request->user();

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $pelanggan->id,
                'id_pelanggan' => $pelanggan->id_pelanggan,
                'nama' => $pelanggan->nama,
                'email' => $pelanggan->email,
                'alamat' => $pelanggan->alamat,
                'whatsapp' => $pelanggan->whatsapp,
                'status' => $pelanggan->status,
                'ip_address' => $pelanggan->ip_address,
                'level' => $pelanggan->level,
                'profile_picture' => $pelanggan->profile_picture ? asset('storage/' . $pelanggan->profile_picture) : null,
                'paket' => $pelanggan->paket ? [
                    'id_paket' => $pelanggan->paket->id_paket,
                    'paket' => $pelanggan->paket->paket,
                    'tarif' => $pelanggan->paket->tarif,
                ] : null,
            ]
        ]);
    }

    /**
     * Update email
     */
    public function updateEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:pelanggan,email,' . $request->user()->id,
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $pelanggan = $request->user();
        $pelanggan->email = $request->email;
        $pelanggan->save();

        return response()->json([
            'success' => true,
            'message' => 'Email berhasil diubah',
            'data' => [
                'email' => $pelanggan->email,
            ]
        ]);
    }

    /**
     * Update password
     */
    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $pelanggan = $request->user();

        // Check current password (plain text comparison)
        if ($pelanggan->password !== $request->current_password) {
            return response()->json([
                'success' => false,
                'message' => 'Password lama tidak sesuai',
            ], 401);
        }

        // Update password (store as plain text)
        $pelanggan->password = $request->new_password;
        $pelanggan->save();

        return response()->json([
            'success' => true,
            'message' => 'Password berhasil diubah',
        ]);
    }
}
