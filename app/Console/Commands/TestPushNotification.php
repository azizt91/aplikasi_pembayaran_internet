<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Pelanggan;
use App\Models\MobileNotification;
use App\Services\FirebaseNotificationService;

class TestPushNotification extends Command
{
    protected $signature = 'notification:test {id_pelanggan} {--message=Test notifikasi dari server}';
    protected $description = 'Test kirim push notification ke pelanggan';

    public function handle()
    {
        $idPelanggan = $this->argument('id_pelanggan');
        $message = $this->option('message');

        $pelanggan = Pelanggan::where('id_pelanggan', $idPelanggan)->first();

        if (!$pelanggan) {
            $this->error("Pelanggan dengan ID {$idPelanggan} tidak ditemukan!");
            return 1;
        }

        $this->info("Pelanggan: {$pelanggan->nama}");
        $this->info("FCM Token: " . ($pelanggan->fcm_token ? substr($pelanggan->fcm_token, 0, 50) . '...' : 'TIDAK ADA'));

        if (empty($pelanggan->fcm_token)) {
            $this->error("Pelanggan tidak memiliki FCM token! Pastikan sudah login di aplikasi.");
            return 1;
        }

        // Simpan notifikasi ke database
        $notification = MobileNotification::createForPelanggan(
            $pelanggan->id_pelanggan,
            'Test Notifikasi',
            $message,
            'info',
            ['test' => true]
        );
        $this->info("Notifikasi disimpan ke database dengan ID: {$notification->id}");

        // Kirim push notification
        $this->info("Mengirim push notification...");

        try {
            $fcmService = app(FirebaseNotificationService::class);
            $result = $fcmService->sendToDevice(
                $pelanggan->fcm_token,
                'Test Notifikasi',
                $message,
                ['type' => 'test', 'notification_id' => (string) $notification->id]
            );

            if ($result) {
                $this->info("✅ Push notification berhasil dikirim!");
            } else {
                $this->warn("⚠️ Push notification gagal dikirim, tapi notifikasi tersimpan di database.");
            }
        } catch (\Exception $e) {
            $this->error("❌ Error: " . $e->getMessage());
        }

        return 0;
    }
}
