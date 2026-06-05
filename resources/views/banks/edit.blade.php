@extends('template.app')

@section('contents')
  <form action="{{ route('banks.update', $bank->id) }}" method="post" enctype="multipart/form-data">
    @csrf
    @method('PUT') {{-- Use PUT for update --}}

    <div class="row">
      <div class="col-12">
        <div class="card shadow mb-4">
          <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Form Edit Bank</h6>
          </div>
          <div class="card-body">
            <div class="form-group">
              <label for="nama_bank">Nama Bank</label>
              <input type="text" class="form-control" id="nama_bank" name="nama_bank" value="{{ $bank->nama_bank }}">
            </div>
            <div class="form-group">
              <label for="pemilik_rekening">Pemilik Rekening</label>
              <input type="text" class="form-control" id="pemilik_rekening" name="pemilik_rekening" value="{{ $bank->pemilik_rekening }}">
            </div>
            <div class="form-group">
              <label for="nomor_rekening">Nomor Rekening</label>
              <input type="text" class="form-control" id="nomor_rekening" name="nomor_rekening" value="{{ $bank->nomor_rekening }}">
            </div>
            <div class="form-group">
              <label for="url_icon">Upload Icon</label>
              <input type="file" class="form-control" id="url_icon" name="url_icon">
            </div>
            @if ($bank->url_icon)
              <div class="form-group">
                <label>Icon Saat Ini:</label>
                <div>
                  <img src="{{ asset('storage/' . $bank->url_icon) }}" alt="Bank Icon" width="100">
                </div>
              </div>
            @endif
          </div>
          <div class="card-footer">
              <a href="{{ route('banks.index') }}" class="btn btn-secondary">Batal</a>
              <input type="submit" name="Simpan" value="Simpan" class="btn btn-primary">
          </div>
        </div>
      </div>
    </div>
  </form>
@endsection
