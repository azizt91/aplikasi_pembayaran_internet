@extends('template.app') 

@section('contents')

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Buat Tagihan</h6>	
    </div>
    <div class="card-body">
        <!-- Formulir -->
        <form class="form-horizontal" action="{{ route('store.tagihan') }}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="container">
                <!-- Dropdown Bulan -->
                <div class="row">
                    <label class="col-md-2 control-label">Bulan</label>
                    <div class="col-md-4">
                        <div class="form-group">
                            <select name="bulan" id="bulan" class="custom-select" style="width: 100%;" required>
                                <option selected="selected">Pilih Bulan</option>
                                @foreach($bulanList as $bulan)
                                    <option value="{{ $bulan['id'] }}">{{ $bulan['bulan'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
        
                <!-- Dropdown Tahun -->
                <div class="row">
                    <label class="col-md-2 control-label">Tahun</label>
                    <div class="col-md-4">
                        <div class="form-group">
                            <select name="tahun" id="tahun" class="custom-select" style="width: 100%;" required>
                                <option>Pilih Tahun</option>
                                @for($year = date('Y'); $year <= date('Y')+5; $year++)
                                    <option>{{ $year }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table-sm table-bordered" width="100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>ID pelanggan</th>
                                <th>Nama</th>
                                <th>Paket</th>
                                <th>Tagihan (Rp.)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pelangganList as $key => $data)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>
                                        <input type="text" name="id_pelanggan[]" class="form-control" value="{{ $data['id_pelanggan'] }}" readonly>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" value="{{ $data['nama'] }}" readonly>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" value="{{ $data['paket']['paket'] }}" readonly>
                                    </td>
                                    <td>
                                        <input type="text" name="tarif" class="form-control" value="{{ 'Rp ' . number_format($data['paket']['tarif'], 0, ',', '.') }}" readonly>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Tombol Submit -->
                <div class="row mt-3">
                    <div class="col-md-12">
                        <a href="{{ route('buka-tagihan') }}" class="btn btn-warning">Batal</a>
                        <button type="submit" class="btn btn-primary" name="Simpan">Buat Tagihan</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

