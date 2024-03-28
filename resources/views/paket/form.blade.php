@extends('template.app')

@section('contents')
  <form action="{{ isset($paket) ? route('paket.update', $paket->id_paket) : route('paket.tambah.simpan') }}" method="post">
    @csrf
    @if(isset($paket))
        @method('POST') {{-- Use POST for update --}}
    @endif

    <div class="row">
      <div class="col-12">
        <div class="card shadow mb-4">
          <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">{{ isset($paket) ? 'Form Edit Paket' : 'Form Tambah Paket' }}</h6>
          </div>
          <div class="card-body">
            <div class="form-group">
              <label for="id_paket">ID Paket</label>
              <input type="text" class="form-control" id="id_paket" name="id_paket" value="{{ isset($paket) ? $paket->id_paket : '' }}" {{ isset($paket) ? 'readonly' : '' }}>
            </div>
            <div class="form-group">
              <label for="paket">Paket</label>
              <input type="text" class="form-control" id="paket" name="paket" value="{{ isset($paket) ? $paket->paket : '' }}">
            </div>
            <div class="form-group">
              <label for="paket">Tarif</label>
              <input type="text" class="form-control" id="tarif" name="tarif" value="{{ isset($paket) ? $paket->tarif : '' }}">
            </div>
          </div>
          <div class="card-footer">
              <a href="{{ route('paket') }}" class="btn btn-secondary">Batal</a>
              <input type="submit" name="Simpan" value="Simpan" class="btn btn-primary">
          </div>
        </div>
      </div>
    </div>
  </form>
@endsection

