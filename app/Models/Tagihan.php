<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tagihan extends Model
{
    use HasFactory;

    protected $table = 'tagihan';
    protected $primaryKey = 'id';
    protected $fillable = [
        'bulan',
        'tahun',
        'id_pelanggan',
        'tagihan',
        'status',
        'tgl_bayar',
    ];

    public function bulan()
    {
        return $this->belongsTo(Bulan::class, 'bulan');
    }

    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class, 'id_pelanggan', 'id_pelanggan');
    }

    public static function getDataByMonthYearAndStatus($bulan, $tahun, $status)
    {
        return static::where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->where('status', $status)
            ->get();
    }

    
}
