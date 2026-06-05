@extends('template.app')

@section('contents')
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Data Pengeluaran</h6>
    </div>

    <div class="card-body">
        <div class="row mb-3">
            <div class="col-12 col-md-6 d-flex justify-content-start mb-2 mb-2">
                <a href="{{ route('pengeluaran.create') }}" class="btn btn-primary">
                    <i class="fas fa-fw fa-plus"></i> Pengeluaran
                </a>
            </div>

            <div class="col-12 col-md-6">
                <form method="GET" action="{{ route('pengeluaran.index') }}" 
                      class="d-flex flex-wrap align-items-center justify-content-between">
                    <div class="form-group mb-2 flex-fill mr-2">
                        <input type="date" name="tanggal_awal" class="form-control" 
                               value="{{ request('tanggal_awal') }}" required>
                    </div>
                    <div class="form-group mb-2 flex-fill mr-2">
                        <input type="date" name="tanggal_akhir" class="form-control" 
                               value="{{ request('tanggal_akhir') }}" required>
                    </div>
                    <div class="d-flex">
                        <button type="submit" class="btn btn-primary mr-2 mb-2">Filter</button>
                        <a href="{{ route('pengeluaran.index') }}" class="btn btn-secondary mb-2">Reset</a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tabel Pengeluaran -->
        <div class="table-responsive">
            <table class="table table-striped table-bordered table-sm text-xl" id="dataTable" width="100%">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Deskripsi</th>
                        <th>Jumlah</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pengeluarans as $pengeluaran)
                    <tr>
                        <td><small>{{ $loop->iteration }}</small></td>
                        <td><small>{{ $pengeluaran->deskripsi }}</small></td>
                        <td><small>{{ rupiah($pengeluaran->jumlah) }}</small></td>
                        <!--<td><small>{{ $pengeluaran->tanggal }}</small></td>-->
                        <td><small>{{ \Carbon\Carbon::parse($pengeluaran->tanggal)->format('d-M-Y') }}</small></td>
                        <td class="d-flex">
                            <a href="{{ route('pengeluaran.edit', $pengeluaran->id) }}" 
                               class="btn btn-warning btn-sm mr-1">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button class="btn btn-danger btn-sm" 
                                    onclick="confirmDelete({{ $pengeluaran->id }})">
                                <i class="fas fa-trash"></i>
                            </button>
                            <form id="delete-form-{{ $pengeluaran->id }}" 
                                  action="{{ route('pengeluaran.destroy', $pengeluaran->id) }}" 
                                  method="POST" style="display: none;">
                                @csrf
                                @method('DELETE')
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            <h5>Total Pengeluaran: {{ rupiah($totalPengeluaran) }}</h5>
        </div>
    </div>
</div>

<!-- SweetAlert untuk Konfirmasi Hapus -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script type="text/javascript">
    function confirmDelete(id) {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Anda tidak dapat mengembalikan data ini!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, hapus!'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        }).catch((error) => {
            Swal.fire('Oops!', 'Terjadi kesalahan. Coba lagi nanti.', 'error');
        });
    }
</script>

<!-- CSS untuk Responsivitas -->
<style>
    @media (max-width: 768px) {
        input[type="date"] {
            width: 100% !important;
        }
        .btn {
            margin-top: 5px;
        }
    }
</style>
@endsection

