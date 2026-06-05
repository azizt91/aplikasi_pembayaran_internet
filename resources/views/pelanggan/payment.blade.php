@extends('layout.app')

@section('contents')

@push('styles')
<style>
    .method-direct {
        width: 100%;
        padding: 15px;
        margin-bottom: 15px;
        border: 2px solid #e3e6f0;
        border-radius: 10px;
        background: white;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .method-direct:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        border-color: #4e73df;
    }
    
    .method-direct img {
        max-width: 80px;
        max-height: 40px;
        object-fit: contain;
    }
    
    .table-detail td {
        padding: 12px;
        vertical-align: middle;
    }
    
    .table-detail td:first-child {
        font-weight: 600;
        color: #5a5c69;
        width: 40%;
    }
    
    .confirmation-section {
        padding: 20px;
        background: #f8f9fc;
        border-radius: 10px;
        margin-top: 20px;
    }
    
    .btn-whatsapp {
        background: #25D366;
        color: white;
        border: none;
        padding: 12px 30px;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s;
    }
    
    .btn-whatsapp:hover {
        background: #128C7E;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(37, 211, 102, 0.3);
    }
    
    .card-modern {
        border: none;
        border-radius: 15px;
        box-shadow: 0 0 20px rgba(0,0,0,0.08);
        margin-bottom: 20px;
    }
    
    .card-header-modern {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 15px 15px 0 0 !important;
        padding: 20px;
    }
</style>
@endpush

@php
    $namaBulan = [
        1 => 'Januari',
        2 => 'Februari',
        3 => 'Maret',
        4 => 'April',
        5 => 'Mei',
        6 => 'Juni',
        7 => 'Juli',
        8 => 'Agustus',
        9 => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Desember',
    ];
@endphp

<!-- Page Header -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <div class="card-body py-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-white">
                        <h4 class="mb-1 font-weight-bold"><i class="fas fa-credit-card"></i> Pembayaran Tagihan</h4>
                        <p class="mb-0 opacity-75">Selesaikan pembayaran Anda dengan mudah dan aman</p>
                    </div>
                    <div class="text-white text-right d-none d-md-block">
                        <a href="{{ route('dashboard-pelanggan') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-12">
            <!-- Card Detail Tagihan -->
            <div class="card card-modern">
                <div class="card-header card-header-modern">
                    <h5 class="m-0 font-weight-bold">
                        <i class="fas fa-file-invoice"></i> Detail Tagihan
                    </h5>
                </div>
                <div class="card-body p-4">
                    <table class="table table-borderless table-detail">
                        <tr>
                            <td><i class="fas fa-hashtag text-primary"></i> Tagihan ID</td>
                            <td><strong>{{ $tagihan->id }}</strong></td>
                        </tr>
                        <tr>
                            <td><i class="fas fa-user text-primary"></i> Atas Nama</td>
                            <td><strong>{{ $tagihan->pelanggan->nama }}</strong></td>
                        </tr>
                        <tr>
                            <td><i class="fas fa-calendar text-primary"></i> Periode</td>
                            <td><strong>{{ $namaBulan[$tagihan->bulan] }} {{ $tagihan->tahun }}</strong></td>
                        </tr>
                        <tr>
                            <td><i class="fas fa-money-bill-wave text-success"></i> Jumlah Tagihan</td>
                            <td><h4 class="mb-0 text-danger font-weight-bold">{{ rupiah($tagihan->tagihan) }}</h4></td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Card Metode Pembayaran -->
            <div class="card card-modern">
                <div class="card-header card-header-modern">
                    <h5 class="m-0 font-weight-bold">
                        <i class="fas fa-university"></i> Pilih Metode Transfer
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row">
                        @foreach ($banks as $bank)
                        <div class="col-md-6 col-sm-6 mb-3">
                            <button type="button" class="method-direct"
                                    onclick="showBankDetails('{{ $bank->nama_bank }}', '{{ $bank->pemilik_rekening }}', '{{ $bank->nomor_rekening }}', '{{ rupiah($tagihan->tagihan) }}')">
                                <img src="{{ asset($bank->url_icon) }}" alt="{{ $bank->nama_bank }}">
                            </button>
                        </div>
                        @endforeach
                    </div>
                    
                    <div class="confirmation-section text-center">
                        <p class="mb-3">
                            <i class="fas fa-info-circle text-primary"></i> 
                            Silahkan konfirmasi pembayaran Anda dengan mengirimkan bukti transfer
                        </p>
                        <button class="btn btn-whatsapp" onclick="openWhatsApp()">
                            <i class="fab fa-whatsapp"></i> Konfirmasi via WhatsApp
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

            <!--<div class="col-lg-8 col-md-12">-->
                <!-- Card for Payment Methods -->
            <!--    <div class="card shadow">-->
            <!--        <div class="card-header bg-primary text-white">-->
            <!--            <h6 class="m-0 font-weight-bold">Pilih Metode Pembayaran Payment Gateway</h6>-->
            <!--        </div>-->
            <!--        <div class="card-body">-->
            <!--            <div class="accordion" id="paymentAccordion">-->
                            <!-- Virtual Account Group -->
            <!--                <div class="card">-->
            <!--                    <div class="card-header" id="headingVA">-->
            <!--                        <h2 class="mb-0">-->
            <!--                            <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseVA" aria-expanded="true" aria-controls="collapseVA">-->
            <!--                                Virtual Account <span class="fas fa-chevron-right"></span>-->
            <!--                            </button>-->
            <!--                        </h2>-->
            <!--                    </div>-->
            <!--                    <div id="collapseVA" class="collapse show" aria-labelledby="headingVA" data-parent="#paymentAccordion">-->
            <!--                        <div class="card-body">-->
            <!--                            <div class="payment-grid">-->
            <!--                                @foreach ($channels as $channel)-->
            <!--                                    @if($channel->active && $channel->group == 'Virtual Account')-->
            <!--                                        <form action="{{ route('transaction.store') }}" method="POST">-->
            <!--                                            @csrf-->
            <!--                                            <input type="hidden" name="id" value="{{ $tagihan->id }}">-->
            <!--                                            <input type="hidden" name="method" value="{{ $channel->code }}">-->
            <!--                                            <button type="submit" class="payment-method btn btn-light">-->
            <!--                                                <div>-->
            <!--                                                    <img src="{{ $channel->icon_url }}" height="30">-->
            <!--                                                    <p class="mt-1 text-xs text-gray-600">{{ $channel->name }}</p>-->
            <!--                                                </div>-->
            <!--                                            </button>-->
            <!--                                        </form>-->
            <!--                                    @endif-->
            <!--                                @endforeach-->
            <!--                            </div>-->
            <!--                        </div>-->
            <!--                    </div>-->
            <!--                </div>-->
                            <!-- Convenience Store Group -->
            <!--                <div class="card">-->
            <!--                    <div class="card-header" id="headingCS">-->
            <!--                        <h2 class="mb-0">-->
            <!--                            <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapseCS" aria-expanded="false" aria-controls="collapseCS">-->
            <!--                                Convenience Store <span class="fas fa-chevron-right"></span>-->
            <!--                            </button>-->
            <!--                        </h2>-->
            <!--                    </div>-->
            <!--                    <div id="collapseCS" class="collapse show" aria-labelledby="headingCS" data-parent="#paymentAccordion">-->
            <!--                        <div class="card-body">-->
            <!--                            <div class="payment-grid">-->
            <!--                                @foreach ($channels as $channel)-->
            <!--                                    @if($channel->active && $channel->group == 'Convenience Store')-->
            <!--                                        <form action="{{ route('transaction.store') }}" method="POST">-->
            <!--                                            @csrf-->
            <!--                                            <input type="hidden" name="id" value="{{ $tagihan->id }}">-->
            <!--                                            <input type="hidden" name="method" value="{{ $channel->code }}">-->
            <!--                                            <button type="submit" class="payment-method btn btn-light">-->
            <!--                                                <div>-->
            <!--                                                    <img src="{{ $channel->icon_url }}" height="30">-->
            <!--                                                    <p class="mt-1 text-xs text-gray-600">{{ $channel->name }}</p>-->
            <!--                                                </div>-->
            <!--                                            </button>-->
            <!--                                        </form>-->
            <!--                                    @endif-->
            <!--                                @endforeach-->
            <!--                            </div>-->
            <!--                        </div>-->
            <!--                    </div>-->
            <!--                </div>-->
                            <!-- E-Wallet Group -->
            <!--                <div class="card">-->
            <!--                    <div class="card-header" id="headingEW">-->
            <!--                        <h2 class="mb-0">-->
            <!--                            <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapseEW" aria-expanded="false" aria-controls="collapseEW">-->
            <!--                                E-Wallet <span class="fas fa-chevron-right"></span>-->
            <!--                            </button>-->
            <!--                        </h2>-->
            <!--                    </div>-->
            <!--                    <div id="collapseEW" class="collapse show" aria-labelledby="headingEW" data-parent="#paymentAccordion">-->
            <!--                        <div class="card-body">-->
            <!--                            <div class="payment-grid">-->
            <!--                                @foreach ($channels as $channel)-->
            <!--                                    @if($channel->active && $channel->group == 'E-Wallet')-->
            <!--                                        <form action="{{ route('transaction.store') }}" method="POST">-->
            <!--                                            @csrf-->
            <!--                                            <input type="hidden" name="id" value="{{ $tagihan->id }}">-->
            <!--                                            <input type="hidden" name="method" value="{{ $channel->code }}">-->
            <!--                                            <button type="submit" class="payment-method btn btn-light">-->
            <!--                                                <div>-->
            <!--                                                    <img src="{{ $channel->icon_url }}" height="30">-->
            <!--                                                    <p class="mt-1 text-xs text-gray-600">{{ $channel->name }}</p>-->
            <!--                                                </div>-->
            <!--                                            </button>-->
            <!--                                        </form>-->
            <!--                                    @endif-->
            <!--                                @endforeach-->
            <!--                            </div>-->
            <!--                        </div>-->
            <!--                    </div>-->
            <!--                </div>-->
            <!--            </div>-->
            <!--        </div>-->
            <!--    </div>-->
            <!--</div>-->

@push('scripts')
<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
function showBankDetails(namaBank, pemilikRekening, nomorRekening, jumlahTagihan) {
    console.log('showBankDetails called', namaBank, pemilikRekening, nomorRekening, jumlahTagihan);
    // Daftar metode e-Wallet
    const eWallets = ['ShopeePay', 'DANA', 'GoPay', 'OVO'];

    // Cek apakah namaBank termasuk dalam daftar e-Wallet
    const isEwallet = eWallets.includes(namaBank);

    // Tentukan label yang akan ditampilkan berdasarkan tipe pembayaran
    const rekeningOrPhoneLabel = isEwallet ? 'Nomor HP' : 'Nomor Rekening';
    const infoTitle = isEwallet ? 'Informasi e-Wallet' : 'Informasi Bank';
    const icon = isEwallet ? 'fa-mobile-alt' : 'fa-university';

    Swal.fire({
        title: `<strong>${infoTitle}</strong>`,
        icon: 'info',
        html: `
            <div style="text-align: left; padding: 20px;">
                <div style="margin-bottom: 20px; padding: 15px; background: #f8f9fc; border-radius: 10px;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 15px; padding-bottom: 10px; border-bottom: 2px solid #e3e6f0;">
                        <span style="color: #5a5c69; font-weight: 600;"><i class="fas ${icon}"></i> ${isEwallet ? 'e-Wallet' : 'Bank'}</span>
                        <strong style="color: #4e73df;">${namaBank}</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 15px; padding-bottom: 10px; border-bottom: 2px solid #e3e6f0;">
                        <span style="color: #5a5c69; font-weight: 600;"><i class="fas fa-user"></i> Atas Nama</span>
                        <strong>${pemilikRekening}</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 15px; padding-bottom: 10px; border-bottom: 2px solid #e3e6f0;">
                        <span style="color: #5a5c69; font-weight: 600;"><i class="fas fa-hashtag"></i> ${rekeningOrPhoneLabel}</span>
                        <div>
                            <strong id="rekening-number" style="color: #1cc88a;">${nomorRekening}</strong>
                            <button onclick="copyRekeningNumber()" class="btn btn-sm btn-primary ml-2" style="padding: 5px 10px;">
                                <i class="fas fa-copy"></i> Copy
                            </button>
                        </div>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding: 15px; background: white; border-radius: 8px;">
                        <span style="color: #5a5c69; font-weight: 600;"><i class="fas fa-money-bill-wave"></i> Jumlah Transfer</span>
                        <strong style="color: #e74a3b; font-size: 1.2rem;">${jumlahTagihan}</strong>
                    </div>
                </div>
                <div style="background: #fff3cd; padding: 15px; border-radius: 10px; border-left: 4px solid #ffc107;">
                    <small style="color: #856404;">
                        <i class="fas fa-info-circle"></i> 
                        <strong>Penting:</strong> Transfer sesuai nominal yang tertera dan konfirmasi pembayaran via WhatsApp
                    </small>
                </div>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: '<i class="fas fa-check"></i> Mengerti',
        cancelButtonText: '<i class="fas fa-times"></i> Tutup',
        confirmButtonColor: '#4e73df',
        cancelButtonColor: '#858796',
        width: '600px',
        customClass: {
            popup: 'swal-modern',
            confirmButton: 'btn btn-primary',
            cancelButton: 'btn btn-secondary'
        }
    });
}

function copyRekeningNumber() {
    const rekeningNumber = document.getElementById("rekening-number").innerText;
    navigator.clipboard.writeText(rekeningNumber).then(() => {
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: 'Nomor rekening telah disalin',
            timer: 2000,
            showConfirmButton: false
        });
    }).catch(err => {
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Gagal menyalin nomor rekening'
        });
    });
}

function openWhatsApp() {
    window.open("https://api.whatsapp.com/send?phone=+6285642828131&text=Halo%2C%20saya%20ingin%20konfirmasi%20pembayaran.");
}
</script>
@endpush

@endsection
