@extends('template.app')

@section('contents')
<div class="alert alert-info" role="alert">
    Data Tagihan - {{ DateTime::createFromFormat('m', $bulan)->format('F') }} {{ $tahun }}
</div>

  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-primary">Data Tagihan</h6>	
    </div>
        <div class="card-body">
            @if(count($tagihanList) > 0)
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-sm" id="dataTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>ID PELANGGAN</th>
                            <th>Nama</th>
                            <th>Tagihan</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tagihanList as $no => $data)
                            <tr>
                                <td>{{ $no + 1 }}</td>
                                <td>{{ $data->id_pelanggan }}</td>
                                <td>{{ $data->pelanggan->nama }}</td>
                                <td>{{ rupiah($data->tagihan) }}</td>
                                <td>
                                    @if($data->status === 'BL' || !isset($data->tgl_bayar))
                                        <span class="badge bg-danger text-white rounded-pill">Belum Bayar</span>
                                    @else
                                        <span class="badge bg-success text-white rounded-pill">Lunas ({{ $data->tgl_bayar }})</span>
                                    @endif
                                </td>
                                <td>
                                    <form action="{{ route('bayar-tagihan', ['kode' => $data->id]) }}" method="POST" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-info btn-sm" onclick="return confirm('Apakah yakin sudah lunas?')">
                                            BAYAR
                                        </button>
                                    </form>
                                    <a href="https://api.whatsapp.com/send?phone={{ $data->pelanggan->whatsapp }}&text=Sdr/i%20{{ $data->pelanggan->nama }},%20Anda%20belum%20melakukan%20pembayaran%20Tagihan%20Internet%20untuk%20Bulan%20{{ $data->bulan }}%20Tahun%20{{ $data->tahun }}%20*Admin Selinggo-Net*" target="_blank" title="Pesan WhatsApp" class="btn btn-success btn-sm">WA</a>                                            
                                </td>                                 
                            </tr>
                        @endforeach    
                    </tbody>
                </table>
            </div>
            @else
            <div class="text-center">
                <img class="img-fluid px-3 px-sm-4 mt-3 mb-4" style="width: 25rem;" src="http://127.0.0.1:8000/template/img/empty.svg" alt="...">
                <p>Tidak ada tagihan.</p>
            </div>            
            @endif
            <div class="row">
                <div class="col-md-12">
                    <a href="{{ route('buka-tagihan') }}" class="btn btn-primary">Kembali</a>
                </div>
            </div>
        </div>
  </div>
@endsection





{{-- @extends('template.app')

@section('contents')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">DATA TAGIHAN</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                @if(count($tagihanList) > 0)
                    <div class="table-responsive">
                        <table id="example1" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>ID PELANGGAN</th>
                                    <th>Nama</th>
                                    <th>Tagihan</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tagihanList as $no => $data)
                                    <tr>
                                        <td>{{ $no + 1 }}</td>
                                        <td>{{ $data->id_pelanggan }}</td>
                                        <td>{{ $data->pelanggan->nama }}</td>
                                        <td>{{ $data->tagihan }}</td>
                                        <td>
                                            @if($data->status === 'Belum Lunas' || !isset($data->tgl_bayar))
                                                <span class="label label-danger">Belum Bayar</span>
                                            @else
                                                <span class="label label-primary">Lunas ({{ $data->tgl_bayar }})</span>
                                            @endif
                                        </td>
                                        <td>
                                            <form action="{{ route('bayar-tagihan', ['kode' => $data->id]) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-info" onclick="return confirm('Apakah yakin sudah lunas?')">
                                                    <i class="glyphicon glyphicon-ok"></i> BAYAR
                                                </button>
                                            </form>
                                            
                                            <a href="https://api.whatsapp.com/send?phone={{ $data->pelanggan->whatsapp }}&text=Sdr/i%20{{ $data->pelanggan->nama }},%20Anda%20belum%20melakukan%20pembayaran%20Tagihan%20Internet%20untuk%20Bulan%20{{ $data->bulan }}%20Tahun%20{{ $data->tahun }}%20*Admin Selinggo-Net*" target="_blank" title="Pesan WhatsApp" class="btn btn-success">
                                                <b>WA</b>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p>Tidak ada tagihan.</p>
                @endif
            </div>
            <!-- /.box-body -->
            <div class="box-footer">
                <a href="{{ route('buka-tagihan') }}" class="btn btn-primary">Kembali</a>
            </div>
        </div>
    </section>
@endsection --}}


