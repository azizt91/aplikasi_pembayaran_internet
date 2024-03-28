@extends('template.app')

@section('contents')
<form action="{{ isset($pelanggan) ? route('pelanggan.update', $pelanggan->id_pelanggan) : route('pelanggan.tambah.simpan') }}" method="post">
  @csrf
  @if(isset($pelanggan))
      @method('PUT') {{-- Use PUT for update --}}
  @endif
  <div class="row">
      <div class="col-12">
        <div class="card shadow mb-4">
          <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">{{ isset($pelanggan) ? 'Form Edit Pelanggan' : 'Form Tambah Pelanggan' }}</h6>
          </div>
          <div class="card-body">
            <div class="form-group">
              <label for="id_paket">ID Pelanggan</label>
              <input type="text" class="form-control" id="id_pelanggan" name="id_pelanggan" value="{{ isset($pelanggan) ? $pelanggan->id_pelanggan : '' }}" {{ isset($pelanggan) ? 'readonly' : '' }}>
            </div>
            <div class="form-group">
              <label for="pelanggan">Nama</label>
              <input type="text" class="form-control" id="nama" name="nama" value="{{ isset($pelanggan) ? $pelanggan->nama : '' }}">
            </div>
            <div class="form-group">
              <label for="pelanggan">Alamat</label>
              <input type="text" class="form-control" id="alamat" name="alamat" value="{{ isset($pelanggan) ? $pelanggan->alamat : '' }}">
            </div>
            <div class="form-group">
              <label for="pelanggan">WhatsApp</label>
              <input type="text" class="form-control" id="whatsapp" name="whatsapp" value="{{ isset($pelanggan) ? $pelanggan->whatsapp : '' }}">
            </div>
            <div class="form-group">
              <label for="pelanggan">Email</label>
              <input type="text" class="form-control" id="email" name="email" value="{{ isset($pelanggan) ? $pelanggan->email : '' }}">
            </div>
            <div class="form-group">
              <label for="id_paket">Paket</label>
              <select name="id_paket" id="id_paket" class="custom-select">
                  <option value="" selected disabled hidden>-- Pilih Paket --</option>
                  @foreach ($paket as $row)
                      <option value="{{ $row->id_paket }}" {{ isset($pelanggan) ? ($pelanggan->id_paket == $row->id_paket ? 'selected' : '') : '' }}>
                          {{ $row->paket }} | {{ $row->tarif }}
                      </option>
                  @endforeach
              </select>
            </div>
            <div class="form-group">
              <label for="status">Status</label>
              <select name="status" id="status" class="custom-select">
                  <option value="" selected disabled hidden>-- Pilih Status --</option>
                  @foreach ($status as $option)
                      <option value="{{ $option }}" {{ old('status', $pelanggan->status ?? '') == $option ? 'selected' : '' }}>
                          {{ ucfirst($option) }}
                      </option>
                  @endforeach
              </select>
            </div>          
            <div class="form-group">
              <label for="id_paket">Jatuh Tempo</label>
              <input type="text" class="form-control" id="jatuh_tempo" name="jatuh_tempo" value="{{ isset($pelanggan) ? $pelanggan->jatuh_tempo : '' }}">
            </div>
          <div class="card-footer">
            <a href="{{ route('pelanggan') }}" class="btn btn-secondary">Batal</a>
							<input type="submit" name="Simpan" value="Simpan" class="btn btn-primary btn-sm">
          </div>
        </div>
      </div>
    </div>
  </form>
@endsection
