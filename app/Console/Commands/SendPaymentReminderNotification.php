<?php

namespace App\Console\Commands;

use App\Models\Pelanggan;
use App\Models\Tagihan;
use App\Models\MobileNotification;
use App\Services\FirebaseNotificationService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendPaymentReminderNotification extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'notification:payment-reminder {--date= : Tanggal reminder (15 atau 20)}';

    /**
     * The console command description.
     */
    protected $description = 'Kirim notifikasi pengingat pembayaran ke pelanggan yang belum bayar';

    /**
     * Execute the console command.
     */
    public function handle(FirebaseNotificationService $firebaseService)
    {
        $tanggal = $this->option('date') ?? Carbon::now()->day;
        $bulanIni = Carbon::now()->month;
        $tahunIni = Carbon::now()->year;

        $this->info("Mengirim notifikasi pengingat pembayaran tanggal {$tanggal}...");

        // Ambil nama bulan dari array (tidak perlu query database)
        $namaBulan = $this->getNamaBulan($bulanIni);

        // Ambil pelanggan yang belum bayar bulan ini dan punya FCM token
        $pelangganBelumBayar = Pelanggan::where('status', 'aktif')
            ->whereNotNull('fcm_token')
            ->whereHas('tagihan', function ($query) use ($bulanIni, $tahunIni) {
                $query->where('bulan', $bulanIni)
                    ->where('tahun', $tahunIni)
                    ->where('status', 'BL'); // Belum Lunas
            })
            ->with(['tagihan' => function ($query) use ($bulanIni, $tahunIni) {
                $query->where('bulan', $bulanIni)
                    ->where('tahun', $tahunIni)
                    ->where('status', 'BL');
            }])
            ->get();

        $successCount = 0;
        $failedCount = 0;

        foreach ($pelangganBelumBayar as $pelanggan) {
            $tagihan = $pelanggan->tagihan->first();

            if (!$tagihan) {
                continue;
            }

            // Format nominal
            $formattedAmount = 'Rp ' . number_format($tagihan->tagihan, 0, ',', '.');
            $title = 'Pengingat Pembayaran';

            // Pesan berbeda untuk tanggal 15 dan 20
            if ($tanggal == 20) {
                $body = "Halo {$pelanggan->nama}, tagihan bulan {$namaBulan} {$tahunIni} sebesar {$formattedAmount} belum dibayar. Segera lakukan pembayaran untuk menghindari isolir layanan.";
            } else {
                $body = "Halo {$pelanggan->nama}, tagihan bulan {$namaBulan} {$tahunIni} sebesar {$formattedAmount} belum dibayar. Mohon segera lakukan pembayaran.";
            }

            // Simpan notifikasi ke database
            MobileNotification::createForPelanggan(
                $pelanggan->id_pelanggan,
                $title,
                $body,
                'reminder',
                [
                    'tagihan_id' => $tagihan->id,
                    'bulan' => $bulanIni,
                    'tahun' => $tahunIni,
                    'reminder_date' => $tanggal
                ]
            );

            // Kirim push notification
            $result = $firebaseService->sendPaymentReminder(
                $pelanggan->fcm_token,
                $pelanggan->nama,
                $namaBulan,
                $tahunIni,
                $tagihan->tagihan,
                $tanggal
            );

            if ($result) {
                $successCount++;
                $this->line("✓ Notifikasi terkirim ke: {$pelanggan->nama}");
            } else {
                $failedCount++;
                $this->error("✗ Gagal kirim ke: {$pelanggan->nama}");
            }
        }

        $this->newLine();
        $this->info("Selesai! Sukses: {$successCount}, Gagal: {$failedCount}");

        Log::info('Payment reminder notification sent', [
            'date' => $tanggal,
            'bulan' => $bulanIni,
            'tahun' => $tahunIni,
            'success' => $successCount,
            'failed' => $failedCount,
        ]);

        return Command::SUCCESS;
    }

    /**
     * Get nama bulan dari nomor bulan
     */
    private function getNamaBulan(int $bulan): string
    {
        $namaBulan = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];

        return $namaBulan[$bulan] ?? (string) $bulan;
    }
}
