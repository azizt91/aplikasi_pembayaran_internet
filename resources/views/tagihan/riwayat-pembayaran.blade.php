@extends('layout.app')

@section('contents')
            <div class="table-responsive">
              <table class="table table-sm" width="100%">
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
                  <h6 class="mb-3"><strong>TAGIHAN PEMBAYARAN ANDA</strong></h6>
                    @foreach ($riwayatPembayaranLunas as $tagihan)
                    <tr>
                        <td>{{ $namaBulan[$tagihan->bulan] }} {{ $tagihan->tahun }}</td>
                        <td><strong>{{ rupiah($tagihan->tagihan) }}</strong></td>
                        <td>
                            <a href="{{ route('tagihan.invoice_pembayaran', ['id' => $tagihan->id]) }}" class="text-blue"><i class="fas fa-angle-right"></i></a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
              </table>
            </div>
@endsection