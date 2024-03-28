@extends('template.app')

@section('contents')
  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-primary">Data Pelanggan</h6>	
    </div>
    <div class="card-body">
      <a href="{{ route('pelanggan.tambah') }}" class="btn btn-primary mb-3"><i class="fas fa-fw fa-plus"></i> Pelanggan</a>
      <div class="table-responsive">
        <table class="table table-striped table-bordered table-sm text-xl" id="dataTable" width="100%">
          <thead>
            <tr>
              <th>No</th>
              <th>ID</th>
			        <th>Nama</th>
			        <th>Alamat</th>
              <th>WhatsApp</th>
              <th>E-Mail</th>
              <th>Paket</th>
              <th>Status</th>
              <th>Jatuh Tempo</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($pelanggan as $row)
              <tr>
                <th><small>{{ $loop->iteration}}</small></th>
                <td><small>{{ $row->id_pelanggan }}</small></td>
                <td><small>{{ $row->nama }}</small></td>
                <td><small>{{ $row->alamat }}</small></td>
                <td><small>{{ $row->whatsapp }}</small></td>
                <td><small>{{ $row->email }}</small></td>
                <td><small>{{ $row->paket->paket }}</small></td>
                {{-- <td><small>{{ $row->status }}</small></td> --}}
                <td>
                  @if($row->status == 'aktif')
                      <span class="badge bg-success text-white rounded-pill">aktif</span>
                  @else
                      <span class="badge bg-danger text-white rounded-pill">nonaktif</span>
                  @endif
                </td>              
                <td><small>{{ $row->jatuh_tempo }}</small></td>
                <td>
                  <a href="{{ route('pelanggan.edit', $row->id_pelanggan) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                  <a href="{{ route('pelanggan.hapus', $row->id_pelanggan) }}" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></a>
                  <a href="{{ route('pelanggan.show', $row->id_pelanggan) }}" class="btn btn-info btn-sm"><i class="fas fa-eye"></i></a>
                </td>
              </tr>
            @endforeach
            @include('sweetalert::alert')
          </tbody>
        </table>
        <!-- {{$pelanggan->links()}} -->
        <!-- {!! $pelanggan->appends(Request::except('search'))->render()!!} -->
      </div>
    </div>
  </div>
@endsection
