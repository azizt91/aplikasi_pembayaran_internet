@extends('template.app')

@section('title', 'Dashboard')

@section('contents')

  <div class="row">

    <!-- Earnings (Monthly) Card Example -->
    <div class="col-xl-4 col-md-6 mb-4">
      <div class="card border-left-primary shadow h-100 py-2">
        <div class="card-body">
          <div class="row no-gutters align-items-center">
            <div class="col mr-2">
              <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                Paket</div>
              <div class="h5 mb-0 font-weight-bold text-gray-800">{{$jumlah_paket}}</div>
            </div>
            <div class="col-auto">
              <i class="fas fa-fw fa-paper-plane fa-2x text-gray-300"></i>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Earnings (Monthly) Card Example -->
    <div class="col-xl-4 col-md-6 mb-4">
      <div class="card border-left-success shadow h-100 py-2">
        <div class="card-body">
          <div class="row no-gutters align-items-center">
            <div class="col mr-2">
              <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                Pelanggan Aktif</div>
              <div class="h5 mb-0 font-weight-bold text-gray-800">{{$jumlah_pelanggan_aktif}}</div>
            </div>
            <div class="col-auto">
              <i class="fas fa-fw fa-user-friends fa-2x text-gray-300"></i>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-xl-4 col-md-6 mb-4">
      <div class="card border-left-danger shadow h-100 py-2">
          <div class="card-body">
              <div class="row no-gutters align-items-center">
                  <div class="col mr-2">
                      <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                          Pelanggan Nonaktif</div>
                      <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $jumlah_pelanggan_nonaktif }}</div>
                  </div>
                  <div class="col-auto">
                      <i class="fas fa-fw fa-user-times fa-2x text-gray-300"></i>
                  </div>
              </div>
          </div>
      </div>
    </div>

    <div class="col-xl-4 col-md-6 mb-4">
      <div class="card border-left-success shadow h-100 py-2">
          <div class="card-body">
              <div class="row no-gutters align-items-center">
                  <div class="col mr-2">
                      <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                          Lunas Bulan Ini</div>
                      <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $jumlah_pelanggan_lunas }}</div>
                  </div>
                  <div class="col-auto">
                      <i class="fas fa-fw fa-user-check fa-2x text-gray-300"></i>
                  </div>
              </div>
          </div>
          <div class="card-footer d-flex align-items-center justify-content-between small" style="background-color: #ffffff;">
              <a class="text-success stretched-link" href="{{ route('pelanggan.lunas') }}">Lihat Detail</a>
              <div class="text-success"><i class="fas fa-angle-right"></i></div>
          </div>
      </div>
    </div>
  
  
  

    <!-- Jumlah Pelanggan Belum Lunas (BL) -->
    {{-- <div class="col-xl-4 col-md-6 mb-4">
      <div class="card border-left-danger shadow h-100 py-2">
          <div class="card-body">
              <div class="row no-gutters align-items-center">
                  <div class="col mr-2">
                      <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                          Belum Bayar Bulan Ini</div>
                      <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $jumlah_pelanggan_belum_lunas }}</div>
                  </div>
                  <div class="col-auto">
                      <i class="fas fa-fw fa-user-times fa-2x text-gray-300"></i>
                  </div>
                  <div class="card-footer d-flex align-items-center justify-content-between small bg-danger" style="border-top: 1px solid #bc0f0f;">
                    <a class="text-white stretched-link" href="{{ route('pelanggan.belumLunas') }}">Lihat Detail</a>
                    <div class="text-white"><i class="fas fa-angle-right"></i></div>
                  </div>
              </div>
          </div>
      </div>
    </div> --}}
    <div class="col-xl-4 col-md-6 mb-4">
      <div class="card border-left-danger shadow h-100 py-2">
          <div class="card-body">
              <div class="row no-gutters align-items-center">
                  <div class="col mr-2">
                      <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                          Belum Bayar Bulan Ini</div>
                      <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $jumlah_pelanggan_belum_lunas }}</div>
                  </div>
                  <div class="col-auto">
                      <i class="fas fa-fw fa-user-times fa-2x text-gray-300"></i>
                  </div>
              </div>
          </div>
          <div class="card-footer d-flex align-items-center justify-content-between small" style="background-color: #ffffff;">
              <a class="text-danger stretched-link" href="{{ route('pelanggan.belumLunas') }}">Lihat Detail</a>
              <div class="text-danger"><i class="fas fa-angle-right"></i></div>
          </div>
      </div>
    </div>
  

    <!-- Earnings (Monthly) Card Example -->
    <div class="col-xl-4 col-md-6 mb-4">
      <div class="card border-left-info shadow h-100 py-2">
          <div class="card-body">
              <div class="row no-gutters align-items-center">
                  <div class="col mr-2">
                      <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                          Pendapatan bulan ini</div>
                      <div class="h5 mb-0 font-weight-bold text-gray-800">{{ rupiah($tagihanBulanIni) }}</div>
                      

                  </div>
                  <div class="col-auto">
                      <i class="fas fa-fw fa-money-bill-wave fa-2x text-gray-300"></i>
                  </div>
              </div>
          </div>
      </div>
    </div>

</div>

{{-- <div class="row">

  <div class="col-xl-12 col-lg-7">

      <!-- Area Chart -->
      <div class="card shadow mb-4">
          <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary">Area Chart</h6>
          </div>
          <div class="card-body">
              <div class="chart-area">
                <canvas id="myAreaChart" width="400" height="400"></canvas>
              </div>
          </div>
      </div>

  </div>

</div> --}}

{{-- </div>


  <!-- Content Row -->

  <div class="row">


    <div class="card shadow mb-4">
      <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Monitoring Pelanggan</h6>	
      </div>
      <div class="card-body">
        <div class="chart-area">
            <canvas id="chartPendapatan" width="300" height="200"></canvas>
        </div>
    </div>
  </div> --}}

@endsection
