@extends('layout.app')

@section('contents')

<!-- Welcome Header -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <div class="card-body py-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-white">
                        <h4 class="mb-1 font-weight-bold">Selamat Datang, {{ $pelanggan->nama }}! 👋</h4>
                        <p class="mb-0 opacity-75">{{ Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</p>
                    </div>
                    <div class="text-white text-right d-none d-md-block">
                        <div class="mb-1"><i class="fas fa-wifi"></i> <strong>{{ $paket->paket ?? 'N/A' }}</strong></div>
                        <div class="small opacity-75">Paket Internet Anda</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">

    <!-- Tagihan Bulan Ini Card -->

    <div class="col-lg-8 mb-4">
        @if($statusTagihan === 'BL')
        <!-- Tagihan Belum Lunas -->
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h6 class="text-muted mb-1"><i class="fas fa-file-invoice"></i> Tagihan {{ $bulanTagihan }}</h6>
                        <span class="badge badge-danger"><i class="fas fa-exclamation-circle"></i> Belum Dibayar</span>
                    </div>
                    <div class="text-right">
                        <i class="fas fa-receipt fa-2x text-danger opacity-50"></i>
                    </div>
                </div>
                <div class="mb-4">
                    <h2 class="mb-0 font-weight-bold text-danger">{{ rupiah($nominalTagihanBulanIni) }}</h2>
                    <small class="text-muted">Total tagihan yang harus dibayar</small>
                </div>
                <div class="alert alert-warning mb-3">
                    <i class="fas fa-info-circle"></i> Segera lakukan pembayaran untuk menghindari pemutusan layanan
                </div>
                <a href="{{ route('payment', ['id' => $idTagihanBulanIni]) }}" class="btn btn-danger btn-block">
                    <i class="fas fa-credit-card"></i> Bayar Sekarang
                </a>
            </div>
        </div>
        @elseif($statusTagihan === 'LS')
        <!-- Tagihan Sudah Lunas -->
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h6 class="text-muted mb-1"><i class="fas fa-file-invoice"></i> Tagihan {{ $bulanTagihan }}</h6>
                        <span class="badge badge-success"><i class="fas fa-check-circle"></i> Sudah Dibayar</span>
                    </div>
                    <div class="text-right">
                        <i class="fas fa-check-circle fa-2x text-success opacity-50"></i>
                    </div>
                </div>
                <div class="mb-4">
                    <h2 class="mb-0 font-weight-bold text-success">{{ rupiah($nominalTagihanBulanIni) }}</h2>
                    <small class="text-muted">Dibayarkan pada {{ $tglBayar }}</small>
                </div>
                <div class="alert alert-success mb-3">
                    <i class="fas fa-check-circle"></i> Terima kasih! Pembayaran Anda telah kami terima
                </div>
                <a href="{{ route('tagihan.riwayat_pembayaran') }}" class="btn btn-outline-success btn-block">
                    <i class="fas fa-history"></i> Lihat Riwayat
                </a>
            </div>
        </div>
        @else
        <!-- Tidak Ada Tagihan -->
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-4 text-center">
                <div class="mb-3">
                    <i class="fas fa-smile fa-4x text-success opacity-50"></i>
                </div>
                <h5 class="font-weight-bold mb-2">Tidak Ada Tagihan</h5>
                <p class="text-muted mb-4">Belum ada tagihan untuk bulan {{ $bulanTagihan }}</p>
                <a href="{{ route('tagihan.riwayat_pembayaran') }}" class="btn btn-outline-primary">
                    <i class="fas fa-history"></i> Lihat Riwayat Pembayaran
                </a>
            </div>
        </div>
        @endif
    </div>

    <!-- Info Paket -->
    <div class="col-lg-4 mb-4">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body p-4">
                <h6 class="text-muted mb-3"><i class="fas fa-box"></i> Paket Anda</h6>
                <h4 class="font-weight-bold mb-2">{{ $paket->paket ?? 'N/A' }}</h4>
                <h3 class="text-primary mb-0">{{ rupiah($paket->tarif ?? 0) }}</h3>
                <small class="text-muted">per bulan</small>
            </div>
        </div>
        
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <h6 class="text-muted mb-3"><i class="fas fa-user"></i> Info Akun</h6>
                <div class="mb-2">
                    <small class="text-muted d-block">ID Pelanggan</small>
                    <strong>{{ $pelanggan->id_pelanggan }}</strong>
                </div>
                <div class="mb-2">
                    <small class="text-muted d-block">Status</small>
                    @if($pelanggan->status == 'aktif')
                        <span class="badge badge-success"><i class="fas fa-check-circle"></i> Aktif</span>
                    @else
                        <span class="badge badge-danger"><i class="fas fa-times-circle"></i> Nonaktif</span>
                    @endif
                </div>
                @if($pelanggan->ip_address)
                <div>
                    <small class="text-muted d-block">IP Address</small>
                    <code class="text-primary">{{ $pelanggan->ip_address }}</code>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Summary Cards -->
<div class="row">
    

    <!-- Card Belum Bayar -->
    <div class="col-md-6 mb-4">
        <a href="{{ route('tagihan.belum_lunas') }}" class="text-decoration-none">
            <div class="card border-0 shadow-sm h-100 hover-lift">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="text-muted small mb-2"><i class="fas fa-exclamation-circle"></i> Belum Dibayar</div>
                            <h3 class="mb-1 font-weight-bold text-danger">{{ $jumlahTagihanBelumLunas }}</h3>
                            <p class="mb-0 text-muted small">Total: {{ rupiah($totalTagihanBelumLunas) }}</p>
                        </div>
                        <div class="bg-danger bg-opacity-10 p-3 rounded">
                            <i class="fas fa-times-circle fa-2x text-danger"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 pt-0 pb-3 px-4">
                    <small class="text-danger"><i class="fas fa-arrow-right"></i> Lihat Detail</small>
                </div>
            </div>
        </a>
    </div>

    <!-- Card Sudah Bayar -->
    <div class="col-md-6 mb-4">
        <a href="{{ route('tagihan.sudah_lunas') }}" class="text-decoration-none">
            <div class="card border-0 shadow-sm h-100 hover-lift">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="text-muted small mb-2"><i class="fas fa-check-circle"></i> Sudah Dibayar</div>
                            <h3 class="mb-1 font-weight-bold text-success">{{ $jumlahTagihanLunas }}</h3>
                            <p class="mb-0 text-muted small">Total: {{ rupiah($totalTagihanLunas) }}</p>
                        </div>
                        <div class="bg-success bg-opacity-10 p-3 rounded">
                            <i class="fas fa-check-circle fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 pt-0 pb-3 px-4">
                    <small class="text-success"><i class="fas fa-arrow-right"></i> Lihat Detail</small>
                </div>
            </div>
        </a>
    </div>

  </div>

@push('styles')
<style>
    .hover-lift {
        transition: all 0.3s ease;
    }
    
    .hover-lift:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.15) !important;
    }
    
    .opacity-75 {
        opacity: 0.75;
    }
    
    .opacity-50 {
        opacity: 0.5;
    }
    
    .bg-opacity-10 {
        opacity: 0.1;
    }
    
    .card {
        border-radius: 0.75rem;
    }
    
    .badge {
        padding: 0.5rem 0.75rem;
        font-weight: 600;
    }
    
    .btn-block {
        display: block;
        width: 100%;
    }
    
    /* Gradient card animation */
    .card[style*="gradient"] {
        position: relative;
        overflow: hidden;
    }
    
    .card[style*="gradient"]::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent);
        transition: left 0.5s;
    }
    
    .card[style*="gradient"]:hover::before {
        left: 100%;
    }
</style>
@endpush

@endsection
