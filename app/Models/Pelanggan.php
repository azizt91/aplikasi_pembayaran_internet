<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Pelanggan extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $table = 'pelanggan';
    // Use 'id' as primary key for Sanctum compatibility (integer)
    // 'id_pelanggan' is just a unique identifier (string)
    protected $primaryKey = 'id';
    protected $fillable = ['id_pelanggan', 'nama', 'alamat', 'whatsapp', 'email', 'password', 'level', 'id_paket', 'ip_address', 'profile_picture', 'fcm_token', 'status'];
    
    public function paket()
    {
        return $this->belongsTo(Paket::class, 'id_paket');
    }

    public function tagihan()
    {
        return $this->hasMany(Tagihan::class, 'id_pelanggan', 'id_pelanggan');
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




    






    

