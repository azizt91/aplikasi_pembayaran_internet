<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Pelanggan extends Authenticatable
{
    use HasFactory, Notifiable;

    // protected $guard = 'pelanggan'; 

    protected $table = 'pelanggan';
    protected $primaryKey = 'id_pelanggan';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $fillable = ['id_pelanggan', 'nama', 'alamat', 'whatsapp', 'email', 'password', 'level', 'id_paket', 'jatuh_tempo','profile_picture', 'status'];

    public function paket()
    {
        return $this->belongsTo(Paket::class, 'id_paket');
    }

    public function tagihan()
    {
        return $this->hasMany(Tagihan::class, 'id_pelanggan');
    }

    public function getProfilePicturePathAttribute()
    {
        if ($this->profile_picture) {
            return asset('storage/' . $this->profile_picture);
        } else {
            return asset('template/img/undraw_profile.svg');
        }
    }

}




    

