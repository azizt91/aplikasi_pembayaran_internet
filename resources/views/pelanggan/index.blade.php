@extends('template.app')

@section('contents')
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Data Pelanggan</h6>
    </div>
    <div class="card-body">
        <a href="{{ route('pelanggan.tambah') }}" class="btn btn-primary mb-3"><i class="fas fa-fw fa-plus"></i> Pelanggan</a>
        <div class="table-responsive">
            <table class="table table-striped table-bordered table-sm text-xl" id="dataTable" width="100%">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>ID</th>
                        <th>Nama</th>
                        <th>Alamat</th>
                        <th>WhatsApp</th>
                        <th>E-Mail</th>
                        <th>Password</th>

                        <th>Paket</th>
                        <th>IP Remote</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pelanggan as $row)
                    <tr>
                        <th><small>{{ $loop->iteration }}</small></th>
                        <td><small>{{ $row->id_pelanggan }}</small></td>
                        <td><small>{{ $row->nama }}</small></td>
                        <td><small>{{ $row->alamat }}</small></td>
                        <td><small>{{ $row->whatsapp }}</small></td>
                        <td><small>{{ $row->email }}</small></td>
                        <td><small>{{ $row->password }}</small></td>

                        <td><small>{{ $row->paket->paket }}</small></td>
                        <td><small>{{ $row->ip_address ?? '-' }}</small></td>
                        <td>
                            @if($row->status == 'aktif')
                            <span class="badge bg-success text-white rounded-pill">aktif</span>
                            @else
                            <span class="badge bg-danger text-white rounded-pill">nonaktif</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('pelanggan.edit', $row->id) }}" class="btn btn-warning btn-sm" title="Edit"><i class="fas fa-edit"></i></a>
                                <form action="{{ route('pelanggan.hapus', $row->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-danger btn-sm btn-hapus" title="Hapus"><i class="fas fa-trash"></i></button>
                                </form>
                                <a href="{{ route('pelanggan.show', $row->id) }}" class="btn btn-info btn-sm" title="Detail"><i class="fas fa-eye"></i></a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                    @include('sweetalert::alert')
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Tambahkan JavaScript konfirmasi menggunakan SweetAlert -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {

        // Confirm Hapus
        const hapusButtons = document.querySelectorAll('.btn-hapus');
        hapusButtons.forEach(button => {
            button.addEventListener('click', function () {
                Swal.fire({
                    title: 'Hapus Pelanggan?',
                    text: "Data pelanggan akan dihapus permanen!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.closest('form').submit();
                    }
                })
            });
        });
        });
</script>
@endsection