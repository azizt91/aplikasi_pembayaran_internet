<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TagihanResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'reference' => $this->reference,
            'bulan' => $this->bulan,
            'tahun' => $this->tahun,
            'periode' => $this->bulan . '/' . $this->tahun,
            'id_pelanggan' => $this->id_pelanggan,
            'pelanggan' => [
                'id_pelanggan' => $this->pelanggan->id_pelanggan ?? null,
                'nama' => $this->pelanggan->nama ?? null,
                'whatsapp' => $this->pelanggan->whatsapp ?? null,
                'email' => $this->pelanggan->email ?? null,
            ],
            'tagihan' => $this->tagihan,
            'tagihan_formatted' => 'Rp ' . number_format($this->tagihan, 0, ',', '.'),
            'status' => $this->status,
            'status_label' => $this->status === 'BL' ? 'Belum Lunas' : 'Lunas',
            'tgl_bayar' => $this->tgl_bayar,
            'pembayaran_via' => $this->pembayaran_via,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
