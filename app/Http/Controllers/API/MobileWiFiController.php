<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WiFiSettings;
use App\Models\WifiChangeHistory;
use App\Models\GenieAcsSetting;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class MobileWiFiController extends Controller
{
    // Path umum TR-069 untuk SSID dan Password
    const WLAN_PATH_SSID = 'InternetGatewayDevice.LANDevice.1.WLANConfiguration.1.SSID';
    const WLAN_PATH_PASS = 'InternetGatewayDevice.LANDevice.1.WLANConfiguration.1.PreSharedKey.1.KeyPassphrase';

    /**
     * Get current WiFi settings from GenieACS
     */
    public function index(Request $request)
    {
        $pelanggan = $request->user();
        
        // Try to get WiFi info from GenieACS first
        $wifiInfo = $this->getWiFiFromGenieACS($pelanggan);
        
        if ($wifiInfo) {
            return response()->json([
                'success' => true,
                'data' => [
                    'ssid' => $wifiInfo['ssid'],
                    'password' => $wifiInfo['password'], // Masked or actual depending on logic
                    'security_type' => 'WPA2-PSK',
                    'is_active' => true,
                    'last_changed' => null,
                    'pelanggan_nama' => $pelanggan->nama,
                    'pelanggan_id' => $pelanggan->id_pelanggan,
                    'ip_address' => $pelanggan->ip_address,
                ]
            ]);
        }
        
        // Fallback response if GenieACS fails or device not found
        return response()->json([
            'success' => true,
            'data' => [
                'ssid' => 'Tidak tersedia',
                'password' => '********',
                'security_type' => 'Unknown',
                'is_active' => false,
                'last_changed' => null,
                'pelanggan_nama' => $pelanggan->nama,
                'pelanggan_id' => $pelanggan->id_pelanggan,
                'ip_address' => $pelanggan->ip_address,
            ]
        ]);
    }
    
    /**
     * Get WiFi info from GenieACS (Robust Implementation)
     */
    private function getWiFiFromGenieACS($pelanggan)
    {
        if (!GenieAcsSetting::isEnabled() || !$pelanggan->ip_address) {
            return null;
        }

        // 1. Cari Device berdasarkan IP (Query yang dioptimalkan)
        $query = json_encode([
            '$or' => [
                ['InternetGatewayDevice.WANDevice.1.WANConnectionDevice.1.WANIPConnection.1.ExternalIPAddress' => $pelanggan->ip_address],
                ['InternetGatewayDevice.WANDevice.1.WANConnectionDevice.1.WANPPPConnection.1.ExternalIPAddress' => $pelanggan->ip_address],
                ['VirtualParameters.pppoeIP' => $pelanggan->ip_address],
                ['_ip' => $pelanggan->ip_address]
            ]
        ]);

        $devices = $this->callGenieAPI('/devices', 'GET', ['query' => $query]);

        // Fallback jika query strict gagal, coba regex
        if (empty($devices)) {
            $regexQuery = json_encode([
                'InternetGatewayDevice.ManagementServer.ConnectionRequestURL' => ['$regex' => $pelanggan->ip_address]
            ]);
            $devices = $this->callGenieAPI('/devices', 'GET', ['query' => $regexQuery]);
        }

        if (!empty($devices) && isset($devices[0])) {
            $device = $devices[0];
            
            // Helper untuk mengambil value dari nested array dengan aman
            $ssid = data_get($device, self::WLAN_PATH_SSID . '._value', 'Tidak tersedia');
            
            return [
                'ssid' => $ssid,
                'password' => '********', // Security: Don't expose real password unless necessary
                'device_id' => $device['_id']
            ];
        }

        return null;
    }
    
    /**
     * Change SSID
     */
    public function changeSSID(Request $request)
    {
        $request->validate([
            'ssid' => 'required|string|min:3|max:32',
        ]);
        
        $pelanggan = $request->user();
        $newSSID = $request->ssid;
        
        // Get device info first
        $wifiInfo = $this->getWiFiFromGenieACS($pelanggan);
        
        if (!$wifiInfo || !isset($wifiInfo['device_id'])) {
            return response()->json([
                'success' => false,
                'message' => 'Router tidak ditemukan atau offline.'
            ], 404);
        }

        // Log the attempt
        $log = $this->createLog($pelanggan->id_pelanggan, 'ssid', $wifiInfo['ssid'], $newSSID, $request);

        // Push to GenieACS
        $success = $this->pushToGenieACS($wifiInfo['device_id'], self::WLAN_PATH_SSID, $newSSID);
        
        if ($success) {
            // Update local database as backup/cache
            WiFiSettings::updateOrCreate(
                ['id_pelanggan' => $pelanggan->id_pelanggan],
                ['ssid' => $newSSID]
            );
            
            $log->update(['status' => 'success']);
            
            return response()->json([
                'success' => true,
                'message' => 'SSID berhasil diubah. Perubahan akan diterapkan dalam 1-2 menit.'
            ]);
        }
        
        $log->update(['status' => 'failed', 'description' => 'Failed to push to GenieACS']);
        
        return response()->json([
            'success' => false,
            'message' => 'Gagal mengubah SSID. Pastikan router terhubung ke internet.'
        ], 500);
    }

    /**
     * Change Password
     */
    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required|string|min:8|max:63',
        ], [
            'password.required' => 'Password wajib diisi',
            'password.min' => 'Password minimal 8 karakter',
            'password.max' => 'Password maksimal 63 karakter',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }
        
        $pelanggan = $request->user();
        $newPassword = $request->password;
        
        // Get device info first
        $wifiInfo = $this->getWiFiFromGenieACS($pelanggan);
        
        if (!$wifiInfo || !isset($wifiInfo['device_id'])) {
            return response()->json([
                'success' => false,
                'message' => 'Router tidak ditemukan atau offline.'
            ], 404);
        }
        
        // Log history
        $log = $this->createLog($pelanggan->id_pelanggan, 'password', '***', '***', $request);
        
        // Push to GenieACS
        $success = $this->pushToGenieACS($wifiInfo['device_id'], self::WLAN_PATH_PASS, $newPassword);
        
        if ($success) {
            // Update local database
            WiFiSettings::updateOrCreate(
                ['id_pelanggan' => $pelanggan->id_pelanggan],
                ['password' => $newPassword]
            );

            $log->update(['status' => 'success']);
            
            return response()->json([
                'success' => true,
                'message' => 'Password WiFi berhasil diubah. Perubahan akan diterapkan dalam 1-2 menit.'
            ]);
        }
        
        $log->update(['status' => 'failed']);

        return response()->json([
            'success' => false,
            'message' => 'Gagal mengubah password. Pastikan router terhubung ke internet.'
        ], 500);
    }
    
    // ==========================================
    // PRIVATE HELPER METHODS
    // ==========================================

    /**
     * Sentralisasi panggilan API GenieACS
     */
    private function callGenieAPI($endpoint, $method = 'GET', $params = [])
    {
        $url = GenieAcsSetting::getValue('genieacs_url');
        $username = GenieAcsSetting::getValue('genieacs_username');
        $password = GenieAcsSetting::getValue('genieacs_password');

        if (!$url) return null;

        $fullUrl = $url . $endpoint;
        
        // Jika GET dan ada params, append ke URL
        if ($method == 'GET' && !empty($params)) {
            $fullUrl .= '?' . http_build_query($params);
        }

        $ch = curl_init($fullUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        
        // Auth Handling
        if ($username && $password) {
            try {
                $decryptedPassword = decrypt($password);
                curl_setopt($ch, CURLOPT_USERPWD, "$username:$decryptedPassword");
            } catch (\Exception $e) {
                curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
            }
        }

        if ($method == 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode >= 200 && $httpCode < 300) {
            return json_decode($response, true);
        }

        Log::error("GenieACS Mobile API Error [$httpCode]: " . $response);
        return null;
    }

    /**
     * Helper khusus untuk Push Parameter
     */
    private function pushToGenieACS($deviceId, $parameter, $value)
    {
        $endpoint = "/devices/" . urlencode($deviceId) . "/tasks?timeout=3000&connection_request";
        
        $payload = [
            'name' => 'setParameterValues',
            'parameterValues' => [
                [$parameter, $value, 'xsd:string']
            ]
        ];

        $result = $this->callGenieAPI($endpoint, 'POST', $payload);
        return $result !== null;
    }

    private function createLog($pelangganId, $type, $old, $new, $request)
    {
        return WifiChangeHistory::create([
            'id_pelanggan' => $pelangganId,
            'type' => $type,
            'description' => "Mengubah $type via Mobile App",
            'old_value' => $old,
            'new_value' => $new,
            'changed_by' => 'customer',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'status' => 'processing'
        ]);
    }
}

