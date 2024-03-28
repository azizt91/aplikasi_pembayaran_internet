@extends('template.app')

@section('contents')
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Data Tagihan</h6>	
    </div>
    <div class="card-body">
        <form class="form-horizontal" action="{{ route('data-tagihan') }}" method="GET">
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
                                @for($year = 2021; $year <= date('Y')+5; $year++)
                                    <option>{{ $year }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                </div>
        
                <!-- Tombol Submit -->
                <div class="row">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary" name="Lihat">Lihat</button>
                    </div>
                </div>
            </div>
        </form>        
    </div>
</div>
@endsection
