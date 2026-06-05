<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GenieAcsSetting;
use App\Models\WifiChangeHistory;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Log;

class WifiSettingController extends Controller
{
    // Path umum TR-069 untuk SSID dan Password
    // Sesuaikan ini jika router Anda menggunakan path lain (misal index 2, 3, dst)
    const WLAN_PATH_SSID = 'InternetGatewayDevice.LANDevice.1.WLANConfiguration.1.SSID';
    const WLAN_PATH_PASS = 'InternetGatewayDevice.LANDevice.1.WLANConfiguration.1.PreSharedKey.1.KeyPassphrase';

    public function index()
    {
        if (!GenieAcsSetting::isEnabled()) {
            Alert::warning('Tidak Tersedia', 'Fitur pengaturan WiFi belum diaktifkan oleh admin');
            return redirect()->route('dashboard-pelanggan');
        }

        $pelanggan = Auth::guard('pelanggan')->user();
        
        $currentWifi = $this->getCurrentWifiInfo($pelanggan);
        
        // SAYA MENAMBAHKAN DUMMY METHOD DI BAWAH AGAR TIDAK ERROR
        $connectedDevices = $this->getConnectedDevices($pelanggan); 
        $history = WifiChangeHistory::where('id_pelanggan', $pelanggan->id_pelanggan)
                                    ->orderBy('created_at', 'desc')
                                    ->limit(10)
                                    ->get();

        // Debug info bisa dihapus saat production
        $debugInfo = [
            'pelanggan_ip' => $pelanggan->ip_address,
            'wifi_status' => $currentWifi['ssid'] !== 'Tidak tersedia' ? 'Online' : 'Offline/Not Found'
        ];

        return view('pelanggan.wifi-settings', compact('currentWifi', 'connectedDevices', 'history', 'debugInfo'));
    }

    /**
     * Mencari Device ID dan Info Wifi
     */
    private function getCurrentWifiInfo($pelanggan)
    {
        if (!$pelanggan->ip_address) {
            return $this->defaultWifiResponse();
        }

        // 1. Cari Device berdasarkan IP (Query yang dioptimalkan)
        $query = json_encode([
            '$or' => [
                ['InternetGatewayDevice.WANDevice.1.WANConnectionDevice.1.WANIPConnection.1.ExternalIPAddress' => $pelanggan->ip_address],
                ['InternetGatewayDevice.WANDevice.1.WANConnectionDevice.1.WANPPPConnection.1.ExternalIPAddress' => $pelanggan->ip_address],
                ['VirtualParameters.pppoeIP' => $pelanggan->ip_address], // Jika anda pakai Virtual Parameter
                ['_ip' => $pelanggan->ip_address] // GenieACS sering menyimpan IP di root param _ip
            ]
        ]);

        $devices = $this->callGenieAPI('/devices', 'GET', ['query' => $query]);

        // Fallback jika query strict gagal, coba regex (lebih lambat tapi fleksibel)
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
                'password' => '********', 
                'ip' => $pelanggan->ip_address,
                'device_id' => $device['_id'] // Penting untuk update
            ];
        }

        return $this->defaultWifiResponse($pelanggan->ip_address);
    }

    public function update(Request $request)
    {
        $request->validate([
            'new_ssid' => 'nullable|string|max:32',
            'new_password' => 'nullable|string|min:8',
            'confirm_password' => 'nullable|same:new_password'
        ]);

        if (!$request->filled('new_ssid') && !$request->filled('new_password')) {
            Alert::warning('Perhatian', 'Minimal isi salah satu: SSID atau Password');
            return redirect()->back();
        }

        $pelanggan = Auth::guard('pelanggan')->user();
        
        // Ambil device ID terbaru
        $wifiInfo = $this->getCurrentWifiInfo($pelanggan);
        
        if (!isset($wifiInfo['device_id'])) {
            Alert::error('Gagal', 'Router tidak ditemukan atau offline. Pastikan IP pelanggan sesuai.');
            return redirect()->back();
        }

        $deviceId = $wifiInfo['device_id'];
        $messages = [];
        $hasError = false;

        // --- UPDATE SSID ---
        if ($request->filled('new_ssid') && $request->new_ssid !== $wifiInfo['ssid']) {
            $log = $this->createLog($pelanggan->id_pelanggan, 'ssid', $wifiInfo['ssid'], $request->new_ssid, $request);
            
            $success = $this->pushToGenieACS($deviceId, self::WLAN_PATH_SSID, $request->new_ssid);
            
            if ($success) {
                $log->update(['status' => 'success']);
                $messages[] = 'SSID berhasil diubah';
            } else {
                $log->update(['status' => 'failed', 'description' => 'Gagal push ke GenieACS']);
                $hasError = true;
            }
        }

        // --- UPDATE PASSWORD ---
        if ($request->filled('new_password')) {
            $log = $this->createLog($pelanggan->id_pelanggan, 'password', '***', '***', $request);
            
            $success = $this->pushToGenieACS($deviceId, self::WLAN_PATH_PASS, $request->new_password);
            
            if ($success) {
                $log->update(['status' => 'success']);
                $messages[] = 'Password berhasil diubah';
            } else {
                $log->update(['status' => 'failed']);
                $hasError = true;
            }
        }

        if (!$hasError && count($messages) > 0) {
            Alert::success('Berhasil', implode(', ', $messages) . '. Tunggu 1-2 menit agar router merestart WiFi.');
        } elseif ($hasError) {
            Alert::error('Gagal', 'Sebagian perubahan gagal diterapkan. Cek koneksi router.');
        } else {
            Alert::info('Info', 'Tidak ada perubahan yang dilakukan.');
        }

        return redirect()->route('wifi-settings.index');
    }

    public function destroy($id)
    {
        $pelanggan = Auth::guard('pelanggan')->user();
        WifiChangeHistory::where('id', $id)->where('id_pelanggan', $pelanggan->id_pelanggan)->delete();
        Alert::success('Berhasil', 'Riwayat dihapus');
        return redirect()->back();
    }

    // ==========================================
    // PRIVATE HELPER METHODS (CLEAN CODE)
    // ==========================================

    /**
     * Sentralisasi panggilan API GenieACS (Menghindari Duplikasi cURL)
     */
    private function callGenieAPI($endpoint, $method = 'GET', $params = [])
    {
        $url = GenieAcsSetting::getValue('genieacs_url');
        $username = GenieAcsSetting::getValue('genieacs_username');
        $password = GenieAcsSetting::getValue('genieacs_password');

        if (!$url) return null;

        $fullUrl = $url . $endpoint;
        
        // Jika GET dan ada params (seperti query), append ke URL
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

        Log::error("GenieACS Error [$httpCode]: " . $response);
        return null;
    }

    /**
     * Helper khusus untuk Push Parameter
     */
    private function pushToGenieACS($deviceId, $parameter, $value)
    {
        // Gunakan timeout pada connection_request agar user tidak menunggu terlalu lama jika modem offline
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
            'description' => "Mengubah $type via Web",
            'old_value' => $old,
            'new_value' => $new,
            'changed_by' => 'customer',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'status' => 'processing'
        ]);
    }

    private function defaultWifiResponse($ip = 'Tidak ada')
    {
        return [
            'ssid' => 'Tidak tersedia',
            'password' => '********',
            'ip' => $ip
        ];
    }

    // --- Placeholder Methods untuk mencegah Error ---
    private function getConnectedDevices($pelanggan)
    {
        // TODO: Implementasi logika Host/Connected Devices dari GenieACS
        return []; 
    }

    private function getRawDeviceData($pelanggan)
    {
        return null;
    }
}