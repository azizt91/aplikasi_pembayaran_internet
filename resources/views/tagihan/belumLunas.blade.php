@extends('template.app')

@section('contents')
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Tagihan Belum Lunas Bulan Ini</h6>
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
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pelangganBelumLunas as $index => $item)
                            <tr>
                                <td class="small">{{ $index + 1 }}</td>
                                <td class="small">{{ $item->nama }}</td>
                                <td class="small">{{ \Carbon\Carbon::now()->format('F Y') }}</td>
                                <td class="small">
                                    @php
                                        // Bulan dan tahun saat ini
                                        $bulanIni = \Carbon\Carbon::now()->month;
                                        $tahunIni = \Carbon\Carbon::now()->year;
                                
                                        // Mencari tagihan yang belum lunas untuk pelanggan ini pada bulan dan tahun saat ini
                                        $tagihanBelumLunas = $item->tagihan->filter(function ($tagihan) use ($bulanIni, $tahunIni) {
                                            return $tagihan->status !== 'LS' && $tagihan->bulan == $bulanIni && $tagihan->tahun == $tahunIni;
                                        });
                                    @endphp
                                
                                    @if ($tagihanBelumLunas->isNotEmpty())
                                        @foreach ($tagihanBelumLunas as $tagihan)
                                            {{ rupiah($tagihan->tagihan) }} <br>
                                        @endforeach
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td class="small">
                                    <span class="badge bg-danger" style="color: #ffffff;">BELUM LUNAS</span>
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
