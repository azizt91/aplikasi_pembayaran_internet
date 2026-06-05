<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WifiChangeHistory extends Model
{
    use HasFactory;

    protected $table = 'wifi_change_history';
    
    protected $fillable = [
        'id_pelanggan',
        'type',
        'description',
        'old_value',
        'new_value',
        'changed_by',
        'ip_address',
        'user_agent',
        'status',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationship with Pelanggan
     */
    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class, 'id_pelanggan', 'id_pelanggan');
    }

    /**
     * Get formatted change description
     */
    public function getDescriptionAttribute()
    {
        $descriptions = [
            'ssid' => 'Mengubah SSID WiFi',
            'password' => 'Mengubah Password WiFi',
            'security' => 'Mengubah Tipe Keamanan',
        ];

        return $descriptions[$this->type] ?? 'Perubahan WiFi';
    }
}

