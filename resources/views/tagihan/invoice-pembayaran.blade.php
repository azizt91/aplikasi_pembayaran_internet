@extends('layout.app')

@section('contents')

@php
$namaBulan = [
    1 => 'Januari',
    2 => 'Februari',
    3 => 'Maret',
    4 => 'April',
    5 => 'Mei',
    6 => 'Juni',
    7 => 'Juli',
    8 => 'Agustus',
    9 => 'September',
    10 => 'Oktober',
    11 => 'November',
    12 => 'Desember',
];

$namaHari = [
    'Sunday' => 'Minggu',
    'Monday' => 'Senin',
    'Tuesday' => 'Selasa',
    'Wednesday' => 'Rabu',
    'Thursday' => 'Kamis',
    'Friday' => 'Jumat',
    'Saturday' => 'Sabtu'
];
@endphp

<!-- Invoice-->
<div class="card mb-3">
    <div class="card-header p-4 p-md-5 border-bottom-0 bg-primary text-white-50">
        <div class="row justify-content-between align-items-center">
            <div class="h3 text-white mb-0">Pembayaran Berhasil</div>
        </div>
    </div>
    <div class="card-body p-4 p-md-5">

            <p>{{ date('d-M-Y', strtotime($tagihan->tgl_bayar)) }}</p>
            <p><h6>DETAIL PEMBAYARAN</h6></p>
            Periode Tagihan<p>
            <strong>{{ $namaBulan[$tagihan->bulan] }} {{ $tagihan->tahun }}</strong></p>
            <p><h6>DETAIL TAGIHAN</h6></p>
            
            <!-- Invoice table-->
            <div class="table">
                <table class="table table-borderless mb-0">
                    <thead class="border-bottom">
                        <tr></tr>
                    </thead>
                    <tbody>
                        <!-- Invoice items -->
                        <tr class="border-bottom">
                            <td>Pembayaran Internet</td>
                            <td>{{ rupiah($tagihan->tagihan) }}</td>
                        </tr>
                        <!-- Invoice total-->
                        <tr>
                            <td>Total Tagihan:</td>
                            <td style="color: blue;"><strong>{{ rupiah($tagihan->tagihan) }}</strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>
    </div>
</div>
@endsection