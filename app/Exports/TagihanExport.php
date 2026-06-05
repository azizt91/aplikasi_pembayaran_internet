<?php

namespace App\Exports;

use App\Models\Tagihan;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\Exportable;

class TagihanExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    protected $bulan;
    protected $tahun;

    public function __construct($bulan, $tahun)
    {
        $this->bulan = $bulan;
        $this->tahun = $tahun;
    }

    public function query()
    {
        return Tagihan::query()
            ->select('id_pelanggan','tagihan', 'status', 'bulan', 'tahun') // Pilih kolom yang akan diekspor
            ->with(['pelanggan' => function ($query) {
                $query->select('id_pelanggan', 'nama', 'whatsapp', 'alamat'); // Pilih kolom pelanggan yang diperlukan
            }])
            ->where('bulan', $this->bulan)
            ->where('tahun', $this->tahun)
            ->where('status', 'BL');
    }

    public function map($tagihan): array
    {
        return [
            $tagihan->id_pelanggan,
            optional($tagihan->pelanggan)->nama, // Dapatkan nama pelanggan
            optional($tagihan->pelanggan)->alamat,
            "'" . optional($tagihan->pelanggan)->whatsapp, // Tambahkan tanda petik sebelum nomor WhatsApp pelanggan
            $this->convertMonth($tagihan->bulan), // Konversi bulan ke nama bulan
            $tagihan->tahun,
            $tagihan->tagihan,
            $tagihan->status === 'BL' ? 'Belum Bayar' : $tagihan->status
        ];
    }

    public function headings(): array
    {
        return [
            'ID Pelanggan',
            'Nama Pelanggan',
            'Alamat',
            'WhatsApp',
            'Bulan',
            'Tahun',
            'Tagihan',
            'Status'
        ];
    }

    private function convertMonth($monthNumber)
    {
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        return $months[$monthNumber] ?? 'Unknown';
    }
}
