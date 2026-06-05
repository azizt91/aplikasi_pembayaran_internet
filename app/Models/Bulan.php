<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bulan extends Model
{
    use HasFactory;

    protected $table = 'bulan';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'bulan'];

    public function tagihan()
    {
        return $this->hasMany(Tagihan::class, 'bulan');
    }
}
