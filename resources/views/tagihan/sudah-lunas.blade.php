@extends('layout.app')

@section('contents')
    <div class="card shadow mb-4">
        <div class="card-header py-3">
          <h6 class="m-0 font-weight-bold text-primary">Tagihan Lunas</h6>	
        </div>
        <div class="card-body">
            <div class="table-responsive">
              <table class="table table-striped table-sm" width="100%">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Bulan</th>
                    <th>Tagihan</th>
                    <th>Status</th>
                    <th>Pembayaran Via</th>
                    <th>Action</th>
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
      
                  @forelse ($tagihanSudahLunas as $tagihan)
                      <tr>
                          <th>{{ $loop->iteration}}</th>
                          <td>{{ $namaBulan[$tagihan->bulan] }} {{ $tagihan->tahun }}</td>
                          <td>{{ rupiah($tagihan->tagihan) }}</td>
                          <td>
                              @if ($tagihan->status === 'LS')
                              <span class="badge bg-success text-white rounded-pill">Lunas</span>
                              @endif
                          </td>
                          <td class="small">
                            @if ($tagihan->pembayaran_via == 'online')
                                <span class="badge badge-pill badge-success" style="color: #ffffff;">ONLINE</span>
                            @elseif ($tagihan->pembayaran_via == 'cash')
                                <span class="badge badge-pill badge-info" style="color: #ffffff;">CASH</span>
                            @endif
                          </td>
                          <td>
                            {{-- <a href="#" class="btn btn-primary btn-sm"><i class="fas fa-print"> Struk</i></a> --}}
                            {{-- <form action="{{ route('cetak.struk', $tagihan->id) }}" method="POST">
                              @csrf
                              <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-print"></i> Struk</button>
                            </form> --}}
                            <form action="{{ route('cetak.struk', $tagihan->id) }}" method="POST">
                              @csrf
                              <button type="button" id="btnPrint" class="btn btn-primary btn-sm"><i class="fas fa-print"></i> Struk</button>
                            </form>
                          </td>  
                      </tr>
                  @empty
                      <tr>
                          <td colspan="4">Tidak ada tagihan belum lunas</td>
                      </tr>
                  @endforelse
      
                </tbody>
              </table>
            </div>
          </div>
@endsection