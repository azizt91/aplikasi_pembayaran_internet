<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class FirebaseNotificationService
{
    private $projectId;
    private $credentialsPath;
    private $credentials;

    public function __construct()
    {
        $this->projectId = config('services.firebase.project_id');
        $credentialsConfig = config('services.firebase.credentials');
        
        // Handle relative or absolute path
        if (file_exists($credentialsConfig)) {
            $this->credentialsPath = $credentialsConfig;
        } else {
            $this->credentialsPath = base_path($credentialsConfig);
        }
        
        $this->loadCredentials();
    }

    /**
     * Load Firebase credentials from JSON file
     */
    private function loadCredentials(): void
    {
        if (file_exists($this->credentialsPath)) {
            $this->credentials = json_decode(file_get_contents($this->credentialsPath), true);
        }
    }

    /**
     * Generate JWT token for Firebase authentication
     */
    private function generateJwt(): string
    {
        $header = [
            'alg' => 'RS256',
            'typ' => 'JWT'
        ];

        $now = time();
        $payload = [
            'iss' => $this->credentials['client_email'],
            'sub' => $this->credentials['client_email'],
            'aud' => 'https://oauth2.googleapis.com/token',
            'iat' => $now,
            'exp' => $now + 3600,
            'scope' => 'https://www.googleapis.com/auth/firebase.messaging'
        ];

        $base64Header = $this->base64UrlEncode(json_encode($header));
        $base64Payload = $this->base64UrlEncode(json_encode($payload));

        $signatureInput = $base64Header . '.' . $base64Payload;
        
        $privateKey = openssl_pkey_get_private($this->credentials['private_key']);
        openssl_sign($signatureInput, $signature, $privateKey, OPENSSL_ALGO_SHA256);
        
        $base64Signature = $this->base64UrlEncode($signature);

        return $base64Header . '.' . $base64Payload . '.' . $base64Signature;
    }

    /**
     * Base64 URL encode
     */
    private function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * Get OAuth2 access token for Firebase Cloud Messaging v1 API
     */
    private function getAccessToken(): ?string
    {
        // Check cache first
        $cachedToken = Cache::get('firebase_access_token');
        if ($cachedToken) {
            return $cachedToken;
        }

        try {
            if (!$this->credentials) {
                Log::error('Firebase: Credentials not loaded');
                return null;
            }

            $jwt = $this->generateJwt();

            $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $jwt
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $accessToken = $data['access_token'];
                $expiresIn = $data['expires_in'] ?? 3600;
                
                // Cache token for slightly less than expiry time
                Cache::put('firebase_access_token', $accessToken, $expiresIn - 60);
                
                return $accessToken;
            }

            Log::error('Firebase: Failed to get access token', $response->json());
            return null;
        } catch (\Exception $e) {
            Log::error('Firebase: Error getting access token - ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Send notification to a single device
     */
    public function sendToDevice(string $fcmToken, string $title, string $body, array $data = []): bool
    {
        $accessToken = $this->getAccessToken();
        if (!$accessToken) {
            return false;
        }

        $url = "https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send";

        $message = [
            'message' => [
                'token' => $fcmToken,
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                ],
                'data' => array_map('strval', $data),
                'android' => [
                    'priority' => 'high',
                    'notification' => [
                        'channel_id' => 'high_importance_channel',
                        'sound' => 'default',
                    ],
                ],
            ],
        ];

        try {
            $response = Http::withToken($accessToken)
                ->post($url, $message);

            if ($response->successful()) {
                Log::info('Firebase: Notification sent successfully', ['token' => substr($fcmToken, 0, 20) . '...']);
                return true;
            }

            Log::error('Firebase: Failed to send notification', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return false;
        } catch (\Exception $e) {
            Log::error('Firebase: Error sending notification - ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send notification to multiple devices
     */
    public function sendToMultipleDevices(array $fcmTokens, string $title, string $body, array $data = []): array
    {
        $results = [
            'success' => 0,
            'failed' => 0,
            'errors' => [],
        ];

        foreach ($fcmTokens as $token) {
            if ($this->sendToDevice($token, $title, $body, $data)) {
                $results['success']++;
            } else {
                $results['failed']++;
                $results['errors'][] = substr($token, 0, 20) . '...';
            }
        }

        return $results;
    }

    /**
     * Send tagihan notification to pelanggan
     */
    public function sendTagihanNotification(string $fcmToken, string $namaPelanggan, string $bulan, int $tahun, int $nominal): bool
    {
        $title = 'Tagihan Baru';
        $body = "Halo {$namaPelanggan}, tagihan internet bulan {$bulan} {$tahun} sebesar Rp " . number_format($nominal, 0, ',', '.') . " sudah tersedia. Silakan lakukan pembayaran.";
        
        $data = [
            'type' => 'tagihan_baru',
            'bulan' => $bulan,
            'tahun' => (string) $tahun,
            'nominal' => (string) $nominal,
        ];

        return $this->sendToDevice($fcmToken, $title, $body, $data);
    }

    /**
     * Send payment reminder notification
     */
    public function sendPaymentReminder(string $fcmToken, string $namaPelanggan, string $bulan, int $tahun, int $nominal, int $tanggal): bool
    {
        $title = 'Pengingat Pembayaran';
        $body = "Halo {$namaPelanggan}, tagihan internet bulan {$bulan} {$tahun} sebesar Rp " . number_format($nominal, 0, ',', '.') . " belum dibayar. Mohon segera lakukan pembayaran untuk menghindari pemutusan layanan.";
        
        $data = [
            'type' => 'payment_reminder',
            'bulan' => $bulan,
            'tahun' => (string) $tahun,
            'nominal' => (string) $nominal,
            'reminder_date' => (string) $tanggal,
        ];

        return $this->sendToDevice($fcmToken, $title, $body, $data);
    }
}
