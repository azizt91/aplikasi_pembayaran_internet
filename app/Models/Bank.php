<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_bank',
        'pemilik_rekening',
        'nomor_rekening',
        'url_icon',
        'is_active',
    ];
    
    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function getUrlIconAttribute($value)
    {
        return asset('storage/' . $value);
    }
}

