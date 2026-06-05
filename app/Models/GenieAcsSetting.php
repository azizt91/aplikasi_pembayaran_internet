<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GenieAcsSetting extends Model
{
    use HasFactory;

    protected $table = 'genieacs_settings';
    
    protected $fillable = [
        'setting_key',
        'setting_value',
        'is_enabled'
    ];

    protected $casts = [
        'is_enabled' => 'boolean'
    ];

    /**
     * Get setting value by key
     */
    public static function getValue($key, $default = null)
    {
        $setting = self::where('setting_key', $key)->first();
        return $setting ? $setting->setting_value : $default;
    }

    /**
     * Set setting value by key
     */
    public static function setValue($key, $value)
    {
        return self::updateOrCreate(
            ['setting_key' => $key],
            ['setting_value' => $value, 'updated_at' => now()]
        );
    }

    /**
     * Check if GenieACS is enabled
     */
    public static function isEnabled()
    {
        return self::getValue('genieacs_enabled') === 'true';
    }

    /**
     * Get all settings as array
     */
    public static function getAllSettings()
    {
        return self::pluck('setting_value', 'setting_key')->toArray();
    }
}
