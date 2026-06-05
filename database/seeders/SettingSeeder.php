<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Setting::firstOrCreate(
            ['id' => 1],
            [
                'fonnte_token' => '',
                'wa_template' => "Assalamu'alaikum {nama_pelanggan},\n\nIni adalah pengingat tagihan internet Anda untuk periode *{periode}* sebesar *{nominal_tagihan}*.\n\nMohon segera melakukan pembayaran. Terima kasih.\n\n_Admin Selinggo-Net_",
            ]
        );
    }
}
