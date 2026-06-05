<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class MobileFcmController extends Controller
{
    /**
     * Register FCM token for the authenticated pelanggan
     */
    public function register(Request $request)
    {
        $request->validate([
            'fcm_token' => 'required|string|max:500',
        ]);

        try {
            $pelanggan = Auth::user();
            $pelanggan->fcm_token = $request->fcm_token;
            $pelanggan->save();

            Log::info('FCM token registered', [
                'pelanggan_id' => $pelanggan->id_pelanggan,
                'token' => substr($request->fcm_token, 0, 20) . '...',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'FCM token berhasil didaftarkan',
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to register FCM token: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mendaftarkan FCM token',
            ], 500);
        }
    }

    /**
     * Unregister FCM token (on logout)
     */
    public function unregister(Request $request)
    {
        $request->validate([
            'fcm_token' => 'required|string',
        ]);

        try {
            $pelanggan = Auth::user();
            
            // Only clear if the token matches
            if ($pelanggan->fcm_token === $request->fcm_token) {
                $pelanggan->fcm_token = null;
                $pelanggan->save();
            }

            Log::info('FCM token unregistered', [
                'pelanggan_id' => $pelanggan->id_pelanggan,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'FCM token berhasil dihapus',
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to unregister FCM token: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus FCM token',
            ], 500);
        }
    }
}
