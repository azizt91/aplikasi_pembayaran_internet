@extends('template.app')

@section('contents')
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Tagihan Lunas</h6>
        </div>
        <div class="card-body">
            <!-- Filter Form -->
            <form action="{{ route('lunas-tagihan') }}" method="GET" class="mb-4">
                <div class="row">
                    <div class="col-md-3">
                        <select name="bulan" class="form-control">
                            @foreach ($bulanList as $b)
                                <option value="{{ $b->id }}" {{ $bulan == $b->id ? 'selected' : '' }}>
                                    {{ $b->bulan }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="tahun" class="form-control">
                            @foreach ($tahunList as $t)
                                <option value="{{ $t }}" {{ $tahun == $t ? 'selected' : '' }}>
                                    {{ $t }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter"></i> Filter
                        </button>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table id="dataTable" class="table table-bordered table-striped table-sm">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Pelanggan</th>
                            <th>Bulan</th>
                            <th>Tagihan</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                            <th>Pembayaran Via</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $namaBulan = [
                                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
                            ];
                            $no = 1;
                        @endphp

                        @foreach ($tagihanLunas as $item)
                            <tr>
                                <td class="small">{{ $no++ }}</td>
                                <td class="small">{{ $item->pelanggan->nama ?? '-' }}</td>
                                <td class="small">{{ $namaBulan[$item->bulan] ?? $item->bulan }} {{ $item->tahun }}</td>
                                <td class="small">{{ rupiah($item->tagihan) }}</td>
                                <td class="small">
                                    <span class="badge badge-pill badge-success" style="color: #ffffff;">LUNAS</span>
                                </td>
                                <td class="small">{{ $item->tgl_bayar ? date("d-M-Y", strtotime($item->tgl_bayar)) : '-' }}</td>
                                <td class="small">
                                    @if ($item->pembayaran_via == 'online')
                                        <span class="badge badge-pill badge-success" style="color: #ffffff;">ONLINE</span>
                                    @elseif ($item->pembayaran_via == 'cash')
                                        <span class="badge badge-pill badge-info" style="color: #ffffff;">CASH</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('cetak-struk', ['id' => $item->id]) }}" target="_blank" title="Cetak Struk" class="btn btn-primary btn-sm">
                                        <i class="fas fa-print"></i>
                                    </a>
                                    <form action="{{ route('rollback-tagihan', ['id' => $item->id]) }}" method="POST" class="d-inline form-rollback">
                                        @csrf
                                        <button type="button" class="btn btn-warning btn-sm btn-rollback" title="Rollback ke Belum Lunas">
                                            <i class="fas fa-undo"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.btn-rollback').forEach(button => {
        button.addEventListener('click', function () {
            Swal.fire({
                title: 'Rollback Status Tagihan?',
                text: 'Status tagihan akan dikembalikan ke Belum Lunas. Lanjutkan?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#f39c12',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Rollback!',
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
