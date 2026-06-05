@extends('template.app')

@section('contents')
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Tagihan Lunas Bulan Ini</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="dataTable" class="table table-bordered table-striped table-sm">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Pelanggan</th>
                            <th>Bulan & Tahun</th>
                            <th>Tagihan</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
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
                        @endphp
                        @foreach ($pelangganLunas as $index => $item)
                            <tr>
                                <td class="small">{{ $index + 1 }}</td>
                                <td class="small">{{ $item->nama }}</td>
                                <td class="small">{{ $namaBulan[\Carbon\Carbon::now()->month] }} {{ \Carbon\Carbon::now()->year }}</td>
                                <td class="small">
                                    @php
                                        $tagihanLunas = $item->tagihan->where('status', 'LS')->filter(function ($tagihan) {
                                            return $tagihan->status === 'LS' && $tagihan->bulan == \Carbon\Carbon::now()->month && $tagihan->tahun == \Carbon\Carbon::now()->year;
                                        })->first();
                                    @endphp
                                    @if ($tagihanLunas && $tagihanLunas->id_pelanggan === $item->id_pelanggan)
                                        {{ rupiah($tagihanLunas->tagihan) }}
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td class="small">
                                    <span class="badge bg-success" style="color: #ffffff;">LUNAS</span>
                                </td>
                                <td class="small">
                                    @php
                                        $bulanIni = \Carbon\Carbon::now()->month;
                                        $tahunIni = \Carbon\Carbon::now()->year;

                                        $tagihanLunas = $item->tagihan()->where('status', 'LS')
                                            ->where('id_pelanggan', $item->id_pelanggan)
                                            ->where('bulan', $bulanIni) // Periksa bulan tagihan
                                            ->where('tahun', $tahunIni) // Periksa tahun tagihan
                                            ->first();
                                    @endphp
                                    @if ($tagihanLunas)
                                        {{ \Carbon\Carbon::parse($tagihanLunas->tgl_bayar)->format('d-M-Y') }}
                                    @else
                                        N/A
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                <a href="{{ route('dashboard') }}" class="btn btn-primary">Kembali</a>
            </div>
        </div>
    </div>
@endsection


