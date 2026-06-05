@extends('template.app')

@section('contents')
  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-primary">Data Pelanggan Nonaktif</h6>
    </div>
    <div class="card-body">
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
              <th>IP PPPoE/Static</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($pelanggan as $row)
              @if ($row->status != 'aktif')
              <tr>
                <th><small>{{ $loop->iteration}}</small></th>
                <td><small>{{ $row->id_pelanggan }}</small></td>
                <td><small>{{ $row->nama }}</small></td>
                <td><small>{{ $row->alamat }}</small></td>
                <td><small>{{ $row->whatsapp }}</small></td>
                <td><small>{{ $row->email }}</small></td>
                <td><small>{{ $row->paket->paket }}</small></td>
                <td><small>{{ $row->ip_address ?? '-' }}</small></td>
                <td>
                    <span class="badge bg-danger text-white rounded-pill">nonaktif</span>
                </td>
              </tr>
              @endif
            @endforeach
            @include('sweetalert::alert')
          </tbody>
        </table>
      </div>
      <div class="mt-3">
        <a href="{{ route('dashboard') }}" class="btn btn-primary">Kembali</a>
    </div>
    </div>
  </div>
@endsection
