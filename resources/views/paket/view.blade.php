@extends('template.app')

@section('contents')
  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-primary">Data Paket</h6>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-striped table-bordered table-sm" id="dataTable" width="100%">
          <thead>
            <tr>
              <th>No</th>
              <th>ID Paket</th>
              <th>Paket</th>
              <th>Tarif</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($data as $row)
              <tr>
                <th>{{ $loop->iteration }}</th>
                <td>{{ $row->id_paket }}</td>
                <td>{{ $row->paket }}</td>
                <td>{{ 'Rp ' . number_format($row->tarif, 0, ',', '.') }}</td>
              </tr>
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
