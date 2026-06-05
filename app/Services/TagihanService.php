<?php

namespace App\Services;

use App\Models\Pelanggan;
use App\Models\Tagihan;
use App\Models\Bulan;
use App\Models\MobileNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class TagihanService
{
    protected $firebaseService;

    public function __construct(FirebaseNotificationService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    /**
     * Generate tagihan secara massal untuk pelanggan yang dipilih.
     *
     * @param int $bulan
     * @param int $tahun
     * @param array $pelangganIds
     * @return array Result summary
     */
    public function generateBulk($bulan, $tahun, ?array $pelangganIds = null)
    {
        $successCount = 0;
        $skippedCount = 0;
        $notificationSent = 0;
        $errors = [];

        // Ambil nama bulan
        $bulanModel = Bulan::find($bulan);
        $namaBulan = $bulanModel ? $bulanModel->bulan : (string) $bulan;

        // 1. Ambil pelanggan - jika tidak ada id yang diberikan, ambil semua pelanggan aktif
        if (empty($pelangganIds)) {
            $pelangganList = Pelanggan::with('paket')
                ->where('status', 'aktif')
                ->get();
            $pelangganIds = $pelangganList->pluck('id_pelanggan')->toArray();
        } else {
            $pelangganList = Pelanggan::with('paket')
                ->whereIn('id_pelanggan', $pelangganIds)
                ->get();
        }

        // 2. Ambil semua tagihan yang sudah ada untuk bulan/tahun ini agar tidak perlu query berulang
        $existingTagihanIds = Tagihan::where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->whereIn('id_pelanggan', $pelangganIds)
            ->pluck('id_pelanggan')
            ->toArray();

        DB::beginTransaction();
        try {
            foreach ($pelangganList as $pelanggan) {
                // Cek apakah tagihan sudah ada
                if (in_array($pelanggan->id_pelanggan, $existingTagihanIds)) {
                    Log::warning('Tagihan sudah ada untuk pelanggan ini. Melewati.', ['id_pelanggan' => $pelanggan->id_pelanggan]);
                    $skippedCount++;
                    continue;
                }

                // Cek status aktif
                if ($pelanggan->status !== 'aktif') {
                    Log::warning('Pelanggan tidak aktif, melewati.', ['id_pelanggan' => $pelanggan->id_pelanggan]);
                    $skippedCount++;
                    continue;
                }

                // Cek paket
                if (!$pelanggan->paket) {
                    Log::warning('Pelanggan tidak memiliki paket, melewati.', ['id_pelanggan' => $pelanggan->id_pelanggan]);
                    $skippedCount++;
                    continue;
                }

                // Buat Tagihan
                $tagihan = Tagihan::create([
                    'bulan' => $bulan,
                    'tahun' => $tahun,
                    'id_pelanggan' => $pelanggan->id_pelanggan,
                    'tagihan' => $pelanggan->paket->tarif,
                    'status' => 'BL', // Belum Lunas
                ]);

                Log::info('Tagihan berhasil disimpan untuk pelanggan:', ['id_pelanggan' => $pelanggan->id_pelanggan]);
                $successCount++;

                // Simpan notifikasi ke database
                $formattedAmount = 'Rp ' . number_format($pelanggan->paket->tarif, 0, ',', '.');
                MobileNotification::createForPelanggan(
                    $pelanggan->id_pelanggan,
                    'Tagihan Baru',
                    "Tagihan bulan {$namaBulan} {$tahun} sebesar {$formattedAmount} sudah tersedia. Silakan lakukan pembayaran.",
                    'tagihan',
                    ['tagihan_id' => $tagihan->id, 'bulan' => $bulan, 'tahun' => $tahun]
                );

                // Kirim push notification jika pelanggan punya FCM token
                if (!empty($pelanggan->fcm_token)) {
                    try {
                        $sent = $this->firebaseService->sendTagihanNotification(
                            $pelanggan->fcm_token,
                            $pelanggan->nama,
                            $namaBulan,
                            $tahun,
                            $pelanggan->paket->tarif
                        );

                        if ($sent) {
                            $notificationSent++;
                            Log::info('Push notification terkirim ke pelanggan:', ['id_pelanggan' => $pelanggan->id_pelanggan]);
                        }
                    } catch (\Exception $e) {
                        Log::error('Gagal kirim push notification: ' . $e->getMessage(), ['id_pelanggan' => $pelanggan->id_pelanggan]);
                    }
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saat generate tagihan massal: ' . $e->getMessage());
            throw $e;
        }

        return [
            'success' => $successCount,
            'skipped' => $skippedCount,
            'notification_sent' => $notificationSent
        ];
    }
}
