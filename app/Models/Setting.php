<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $table = 'settings';

    protected $fillable = [
        'fonnte_token',
        'wa_template',
    ];

    /**
     * Ambil setting row (ID = 1).
     * Jika belum ada, buat row default.
     */
    public static function getSetting()
    {
        return static::firstOrCreate(
            ['id' => 1],
            [
                'fonnte_token' => '',
                'wa_template' => "Assalamu'alaikum {nama_pelanggan},\n\nIni adalah pengingat tagihan internet Anda untuk periode *{periode}* sebesar *{nominal_tagihan}*.\n\nMohon segera melakukan pembayaran. Terima kasih.\n\n_Admin Selinggo-Net_",
            ]
        );
    }

    /**
     * Update setting row (ID = 1).
     */
    public static function updateSetting(array $data)
    {
        $setting = static::getSetting();
        $setting->update($data);
        return $setting;
    }
}
