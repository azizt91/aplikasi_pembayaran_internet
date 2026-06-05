<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Pengeluaran extends Model
{
    use HasFactory;

    protected $fillable = ['deskripsi', 'jumlah', 'tanggal', 'bulan', 'tahun'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $tanggal = Carbon::parse($model->tanggal);
            $model->bulan = $tanggal->month;
            $model->tahun = $tanggal->year;
        });

        static::updating(function ($model) {
            $tanggal = Carbon::parse($model->tanggal);
            $model->bulan = $tanggal->month;
            $model->tahun = $tanggal->year;
        });
    }
}
