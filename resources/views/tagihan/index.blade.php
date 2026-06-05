@extends('template.app') 

@section('contents')

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Buat Tagihan</h6>	
    </div>
    <div class="card-body">
        <!-- Formulir -->
        <form class="form-horizontal" action="{{ route('store.tagihan') }}" method="post">
            @csrf
            <div class="container">
                <!-- Dropdown Bulan -->
                <div class="row mb-3">
                    <label class="col-md-2 control-label">Bulan</label>
                    <div class="col-md-4">
                        <select name="bulan" id="bulan" class="custom-select" style="width: 100%;" required>
                            <option value="">Pilih Bulan</option>
                            @foreach($bulanList as $bulan)
                                <option value="{{ $bulan['id'] }}">{{ $bulan['bulan'] }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
        
                <!-- Dropdown Tahun -->
                <div class="row mb-3">
                    <label class="col-md-2 control-label">Tahun</label>
                    <div class="col-md-4">
                        <select name="tahun" id="tahun" class="custom-select" style="width: 100%;" required>
                            <option value="">Pilih Tahun</option>
                            @for($year = date('Y'); $year <= date('Y')+5; $year++)
                                <option>{{ $year }}</option>
                            @endfor
                        </select>
                    </div>
                </div>

                <!-- Info Pelanggan Aktif -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="alert alert-info mb-0">
                            <i class="fas fa-info-circle mr-2"></i>
                            Tagihan akan dibuat untuk <strong>{{ $jumlahPelangganAktif }} pelanggan aktif</strong>
                        </div>
                    </div>
                </div>

                <!-- Tombol Submit -->
                <div class="row mt-3">
                    <div class="col-md-12">
                        <a href="{{ route('buka-tagihan') }}" class="btn btn-warning">Batal</a>
                        <button type="submit" class="btn btn-primary" name="Simpan">
                            <i class="fas fa-file-invoice mr-1"></i> Buat Tagihan
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
