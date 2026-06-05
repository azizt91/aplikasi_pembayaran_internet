@extends('template.app')

@section('contents')
<div class="alert alert-info" role="alert">
    Data Tagihan - {{ DateTime::createFromFormat('m', $bulan)->format('F') }} {{ $tahun }}
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Data Tagihan</h6>
        <a href="{{ route('export-tagihan', ['bulan' => $bulan, 'tahun' => $tahun]) }}" class="btn btn-success">Export to Excel</a>
    </div>
    <div class="card-body">
        @if(count($tagihanList) > 0)
        <div class="table-responsive">
            <table class="table table-striped table-bordered table-sm text-xl" id="dataTable" width="100%">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>ID PELANGGAN</th>
                        <th>Nama</th>
                        <th>Tagihan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tagihanList as $no => $data)
                    <tr>
                        <td>{{ $no + 1 }}</td>
                        <td>{{ $data->id_pelanggan }}</td>
                        <td>{{ $data->pelanggan->nama }}</td>
                        <td>{{ rupiah($data->tagihan) }}</td>
                        <td>
                            @if($data->status === 'BL' || !isset($data->tgl_bayar))
                            <span class="badge bg-danger text-white rounded-pill">Belum Bayar</span>
                            @else
                            <span class="badge bg-success text-white rounded-pill">Lunas ({{ $data->tgl_bayar }})</span>
                            @endif
                        </td>
                        <td>
                            <form action="{{ route('bayar-tagihan', ['kode' => $data->id]) }}" method="POST" class="d-inline form-lunas">
                                @csrf
                                <button type="button" class="btn btn-info btn-sm btn-lunas">
                                    <i class="fas fa-money-bill-wave"></i>
                                </button>
                            </form>
                            {{-- <form id="form-lunas" action="{{ route('bayar-tagihan', ['kode' => $data->id]) }}" method="POST" class="d-inline">
                                @csrf
                                <input type="hidden" name="pembayaran_via" id="pembayaran_via" value="cash">
                                <button type="button" class="btn btn-info btn-sm btn-lunas" onclick="showPaymentOptions()">
                                    <i class="fas fa-money-bill-wave"></i>
                                </button>
                            </form> --}}
                            <a href="https://api.whatsapp.com/send?phone={{ $data->pelanggan->whatsapp }}&text=Sdr/i%20{{ $data->pelanggan->nama }},%20Anda%20belum%20melakukan%20pembayaran%20Tagihan%20Internet%20untuk%20Bulan%20{{ $data->bulan }}%20Tahun%20{{ $data->tahun }}%20*Admin Selinggo-Net*" target="_blank" title="Pesan WhatsApp" class="btn btn-success btn-sm">
                                <i class="fab fa-whatsapp"></i>
                            </a>
                            <form action="{{ route('delete-tagihan', ['id' => $data->id]) }}" method="POST" class="d-inline form-hapus">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-danger btn-sm btn-hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center">
            <img class="img-fluid px-3 px-sm-4 mt-3 mb-4" style="width: 25rem;" src="http://127.0.0.1:8000/template/img/empty.svg" alt="...">
            <p>Tidak ada tagihan.</p>
        </div>
        @endif
        <div class="row">
            <div class="col-md-12">
                <a href="{{ route('buka-tagihan') }}" class="btn btn-primary">Kembali</a>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Confirm Lunas
    const lunasButtons = document.querySelectorAll('.btn-lunas');
    lunasButtons.forEach(button => {
        button.addEventListener('click', function () {
            Swal.fire({
                title: 'Apakah yakin sudah lunas?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, lunas!'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.closest('form').submit();
                }
            })
        });
    });

    // Confirm Hapus
    const hapusButtons = document.querySelectorAll('.btn-hapus');
    hapusButtons.forEach(button => {
        button.addEventListener('click', function () {
            Swal.fire({
                title: 'Apakah yakin ingin menghapus tagihan ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus!'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.closest('form').submit();
                }
            })
        });
    });
});
</script>

{{-- <script>
    function showPaymentOptions() {
        Swal.fire({
            title: 'Pembayaran Via',
            icon: 'question',
            showCancelButton: true,
            showDenyButton: true,
            confirmButtonText: 'Cash',
            denyButtonText: `Transfer`,
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('pembayaran_via').value = 'cash';
                document.getElementById('form-lunas').submit();
            } else if (result.isDenied) {
                document.getElementById('pembayaran_via').value = 'transfer';
                document.getElementById('form-lunas').submit();
            }
        })
    }
</script> --}}

{{-- <script>
    function showPaymentOptions(id) {
        Swal.fire({
            title: 'Pembayaran Via',
            icon: 'question',
            showCancelButton: true,
            showDenyButton: true,
            confirmButtonText: 'Cash',
            denyButtonText: `Transfer`,
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(`pembayaran_via-${id}`).value = 'cash';
                document.getElementById(`form-lunas-${id}`).submit();
            } else if (result.isDenied) {
                document.getElementById(`pembayaran_via-${id}`).value = 'transfer';
                document.getElementById(`form-lunas-${id}`).submit();
            }
        })
    }
</script> --}}

@endsection



