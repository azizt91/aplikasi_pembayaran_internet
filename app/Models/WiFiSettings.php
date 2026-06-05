<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WiFiSettings extends Model
{
    use HasFactory;

    protected $table = 'wifi_settings';

    protected $fillable = [
        'id_pelanggan',
        'ssid',
        'password',
        'security_type',
        'is_active',
    ];

    protected $hidden = [
        'password', // Hide password by default in JSON responses
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationship dengan Pelanggan
     */
    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class, 'id_pelanggan', 'id_pelanggan');
    }

    /**
     * Get change history
     */
    public function changeHistory()
    {
        return $this->hasMany(WiFiChangeHistory::class, 'id_pelanggan', 'id_pelanggan');
    }

    /**
     * Get masked password (for display)
     */
    public function getMaskedPasswordAttribute()
    {
        return str_repeat('•', min(strlen($this->password), 8));
    }
}
