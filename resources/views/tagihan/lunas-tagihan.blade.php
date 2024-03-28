@extends('template.app')

@section('contents')
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Tagihan Lunas</h6>
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
                            <th>Aksi</th>
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
                        @php
                            $no = 1;
                            $data = DB::table('pelanggan as p')
                                ->join('tagihan as t', 'p.id_pelanggan', '=', 't.id_pelanggan')
                                ->where('t.status', 'LS')
                                ->orderByDesc('t.tgl_bayar')
                                ->get();
                        @endphp

                        @foreach ($data as $item)
                            <tr>
                                <td class="small">{{ $no++ }}</td>
                                <td class="small">{{ $item->id_pelanggan }} - {{ $item->nama }}</td>
                                <td class="small">{{ $namaBulan[$item->bulan] }} {{ $item->tahun }}</td>
                                <td class="small">{{ rupiah($item->tagihan) }}</td>
                                <td class="small">
                                    @if ($item->status == 'BL')
                                        <span class="badge bg-danger" style="color: #ffffff;">Belum Bayar</span>
                                    @elseif ($item->status == 'LS')
                                        <span class="badge bg-success" style="color: #ffffff;">LUNAS</span>
                                    @endif
                                </td>
                                <td class="small">{{ date("d-M-Y", strtotime($item->tgl_bayar)) }}</td>
                                <td>
                                    <a href="{{ route('cetak-struk', ['id' => $item->id]) }}" target="_blank" title="Cetak Struk" class="btn-sm btn-primary">
                                        <i class="fas fa-print"></i> Struk
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
