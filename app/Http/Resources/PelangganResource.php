<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PelangganResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id_pelanggan' => $this->id_pelanggan,
            'nama' => $this->nama,
            'alamat' => $this->alamat,
            'whatsapp' => $this->whatsapp,
            'email' => $this->email,
            'level' => $this->level,
            'status' => $this->status,
            'ip_address' => $this->ip_address,
            'profile_picture' => $this->profile_picture ? asset('storage/' . $this->profile_picture) : null,
            'paket' => [
                'id_paket' => $this->paket->id_paket ?? null,
                'nama_paket' => $this->paket->paket ?? null,
                'tarif' => $this->paket->tarif ?? null,
            ],
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
