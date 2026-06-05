@extends('template.app')

@section('contents')
  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-primary">Data Paket</h6>
    </div>
    <div class="card-body">
    @if (auth()->user()->level == 'Admin')
      <a href="{{ route('paket.tambah') }}" class="btn btn-primary mb-3"><i class="fas fa-fw fa-plus"></i> Paket</a>
      @endif
      <div class="table-responsive">
          <table class="table table-striped table-bordered table-sm" id="dataTable" width="100%">
          <thead>
            <tr>
              <th>No</th>
              <th>ID Paket</th>
							<th>Paket</th>
							<th>Tarif</th>
              @if (auth()->user()->level == 'Admin')
              <th>Aksi</th>
              @endif
            </tr>
          </thead>
          <tbody>
            {{-- @php($no = 1) --}}
            @foreach ($data as $row)
              <tr>
                {{-- <th>{{ $no++ }}</th> --}}
                <th>{{ $loop->iteration}}</th>
                <td>{{ $row->id_paket }}</td>
                <td>{{ $row->paket }}</td>
                <td>{{ 'Rp ' . number_format($row->tarif, 0, ',', '.') }}</td>
                <td>

                  <a href="{{ route('paket.edit', $row->id_paket) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                  <a href="{{ route('paket.hapus', $row->id_paket) }}" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></a>

                </td>
              </tr>
            @endforeach
            @include('sweetalert::alert')
          </tbody>
        </table>
      </div>
    </div>
  </div>
@endsection



