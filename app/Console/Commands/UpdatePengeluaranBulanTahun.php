<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Pengeluaran;
use Carbon\Carbon;

class UpdatePengeluaranBulanTahun extends Command
{
    protected $signature = 'update:pengeluaran-bulan-tahun';
    protected $description = 'Update bulan dan tahun in pengeluarans table based on tanggal column';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        Pengeluaran::all()->each(function ($pengeluaran) {
            $tanggal = Carbon::parse($pengeluaran->tanggal);
            $pengeluaran->bulan = $tanggal->month;
            $pengeluaran->tahun = $tanggal->year;
            $pengeluaran->save();
        });

        $this->info('Bulan dan tahun telah diperbarui untuk semua pengeluaran.');
    }
}
