@extends('layout.app')

@section('title', 'Welcome, ' . (auth()->user()->nama ?? ''))


@section('contents')

  <div class="row">

    <div class="col-xl-4 col-md-6 mb-4">
      <div class="card h-100 py-2">
          <div class="card-body ">
              <div class="row no-gutters align-items-center">
                  <div class="col mr-0">

                      @if ($statusTagihan === 'BL')
                      <div class="text-xs font-weight-bold text-primary mb-1">
                          Tagihan Anda
                      </div>
                      @else
                      <div class="text-xs font-weight-bold text-primary mb-1">
                          Tagihan Sebelumnya 
                      </div>
                      @endif
                      
                      <div class="h5 mb-1 font-weight-bold text-gray-800">{{ $nominalTagihanBulanIni }}</div>
                      @if ($statusTagihan === 'BL')
                      <div class="text-xs font-weight-bold text-default mb-1"><i class="fas fa-exclamation-circle"></i>
                          Jatuh tempo {{ $pelanggan->jatuh_tempo }}
                      </div>
                      @else
                      <div class="text-xs font-weight-bold text-default mb-1"><i class="fas fa-exclamation-circle"></i>
                          Dibayarkan pada {{ $tglBayar }}
                      </div>
                      @endif
                  </div>
                  <div class="col-auto position-absolute" style="top: 8px; right: 12px;">
                      @if ($statusTagihan === 'BL')
                      <div>
                      <span class="badge bg-danger text-white text-xs rounded-pill"><i class="fas fa-times"></i> Belum Bayar</span>
                      </div>
                      @else
                      <div>
                      <span class="badge bg-success text-white text-xs rounded-pill"><i class="fas fa-check"></i> Sudah Dibayar</span>
                      </div>
                      @endif
                  </div>
              </div>
          </div>
      </div>
    </div>
  
    <div class="col-xl-4 col-md-6 mb-4">
      <div class="card bg-danger text-white h-100">
          <div class="card-body">
              <div class="d-flex justify-content-between align-items-center">
                  <div class="me-3">
                      <div class="text-white-75 small">Belum Bayar</div>
                      <div class="text-lg fw-bold">{{ $jumlahTagihanBelumLunas }}</div>
                  </div>
                  <i class="fas fa-fw fa-frown fa-2x text-white-300"></i>
              </div>
          </div>
          <div class="card-footer d-flex align-items-center justify-content-between small bg-danger" style="border-top: 1px solid #bc0f0f;">
              <a class="text-white stretched-link" href="{{ route('tagihan.belum_lunas') }}">Lihat Detail</a>
              <div class="text-white"><i class="fas fa-angle-right"></i></div>
          </div>
      </div>
    </div>

    <div class="col-xl-4 col-md-6 mb-4">
      <div class="card bg-success text-white h-100">
          <div class="card-body">
              <div class="d-flex justify-content-between align-items-center">
                  <div class="me-3">
                      <div class="text-white-75 small">Sudah Dibayar</div>
                      <div class="text-lg fw-bold">{{ $jumlahTagihanLunas }}</div>
                  </div>
                  <i class="fas fa-fw fa-smile fa-2x text-white-300"></i>
              </div>
          </div>
          <div class="card-footer d-flex align-items-center justify-content-between small bg-success" style="border-top: 1px solid #00ad42;">
              <a class="text-white stretched-link"  href="{{ route('tagihan.sudah_lunas') }}">Lihat Detail</a>
              <div class="text-white"><i class="fas fa-angle-right"></i></div>
          </div>
      </div>
    </div>

  </div>

@endsection
