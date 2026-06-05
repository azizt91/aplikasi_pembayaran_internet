<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GenieAcsSetting;
use App\Models\Setting;
use RealRashid\SweetAlert\Facades\Alert;

class SettingController extends Controller
{
    /**
     * Display settings page
     */
    public function index()
    {
        $settings = GenieAcsSetting::getAllSettings();
        $appSetting = Setting::getSetting();
        
        return view('settings.index', compact('settings', 'appSetting'));
    }

    /**
     * Update GenieACS settings
     */
    public function updateGenieACS(Request $request)
    {
        $request->validate([
            'genieacs_url' => 'nullable|url',
            'genieacs_username' => 'nullable|string|max:100',
            'genieacs_password' => 'nullable|string|max:255'
        ]);

        try {
            // Update enabled status
            GenieAcsSetting::setValue('genieacs_enabled', $request->has('genieacs_enabled') ? 'true' : 'false');
            
            // Update URL
            if ($request->filled('genieacs_url')) {
                GenieAcsSetting::setValue('genieacs_url', $request->genieacs_url);
            }
            
            // Update username
            GenieAcsSetting::setValue('genieacs_username', $request->genieacs_username ?? '');
            
            // Update password (only if provided)
            if ($request->filled('genieacs_password')) {
                GenieAcsSetting::setValue('genieacs_password', encrypt($request->genieacs_password));
            }

            Alert::success('Berhasil', 'Pengaturan GenieACS berhasil disimpan');
            return redirect()->route('settings.index');
        } catch (\Exception $e) {
            Alert::error('Error', 'Gagal menyimpan pengaturan: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * Test GenieACS connection
     */
    public function testGenieACS()
    {
        try {
            $url = GenieAcsSetting::getValue('genieacs_url');
            $username = GenieAcsSetting::getValue('genieacs_username');
            $password = GenieAcsSetting::getValue('genieacs_password');

            if (!$url) {
                return response()->json([
                    'success' => false,
                    'message' => 'URL GenieACS belum diatur'
                ]);
            }

            // Try to connect to GenieACS
            $ch = curl_init($url . '/devices');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            
            if ($username && $password) {
                try {
                    $decryptedPassword = decrypt($password);
                    curl_setopt($ch, CURLOPT_USERPWD, "$username:$decryptedPassword");
                } catch (\Exception $e) {
                    // Password not encrypted or invalid
                    curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
                }
            }

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode == 200 || $httpCode == 401) {
                return response()->json([
                    'success' => true,
                    'message' => 'Koneksi ke GenieACS berhasil!'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat terhubung ke GenieACS (HTTP ' . $httpCode . ')'
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Update Fonnte WhatsApp settings
     */
    public function updateFonnte(Request $request)
    {
        $request->validate([
            'fonnte_token' => 'nullable|string',
            'wa_template' => 'nullable|string',
        ]);

        try {
            Setting::updateSetting([
                'fonnte_token' => $request->fonnte_token ?? '',
                'wa_template' => $request->wa_template ?? '',
            ]);

            Alert::success('Berhasil', 'Pengaturan Fonnte WhatsApp berhasil disimpan');
            return redirect()->route('settings.index');
        } catch (\Exception $e) {
            Alert::error('Error', 'Gagal menyimpan pengaturan: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }
}
