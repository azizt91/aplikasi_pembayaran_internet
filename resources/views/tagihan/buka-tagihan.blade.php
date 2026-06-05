@extends('template.app')

@section('contents')
<style>
    .wa-unsent {
        color: #6c757d; /* abu-abu */
        font-size: 1.2rem;
        margin: 0 2px;
    }
    .wa-sent {
        color: #28a745; /* hijau */
        font-size: 1.2rem;
        margin: 0 2px;
    }
</style>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Data Tagihan Belum Lunas</h6>
    </div>
    <div class="card-body">
        <!-- Filter Form -->
        <form action="{{ route('buka-tagihan') }}" method="GET" class="mb-4">
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
                        @for($year = 2021; $year <= date('Y')+5; $year++)
                            <option value="{{ $year }}" {{ $tahun == $year ? 'selected' : '' }}>{{ $year }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                </div>
                @if(count($tagihanList) > 0)
                <div class="col-md-5 text-right">
                    <button type="button" class="btn btn-success mr-1" id="btnBroadcast">
                        <i class="fab fa-whatsapp"></i> Broadcast WhatsApp
                    </button>
                    <a href="{{ route('export-tagihan', ['bulan' => $bulan, 'tahun' => $tahun]) }}" class="btn btn-info">
                        <i class="fas fa-file-excel"></i> Export Excel
                    </a>
                </div>
                @endif
            </div>
        </form>

        @if(count($tagihanList) > 0)
        <div class="table-responsive">
            <table class="table table-striped table-bordered table-sm" id="tabelTagihan" width="100%">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 40px;">
                            <input type="checkbox" id="checkAll">
                        </th>
                        <th style="width:45px">No</th>
                        <th>ID PELANGGAN</th>
                        <th>Nama</th>
                        <th>WhatsApp</th>
                        <th>Tagihan</th>
                        <th>Status</th>
                        <th class="text-center">Broadcast</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tagihanList as $no => $data)
                    <tr>
                        <td class="text-center">
                            @if(!empty($data->pelanggan->whatsapp) && strlen(preg_replace('/[^0-9]/', '', $data->pelanggan->whatsapp)) >= 10)
                                <input type="checkbox" class="check-item" value="{{ $data->id }}">
                            @endif
                        </td>
                        <td class="nomor-urut"></td>
                        <td>{{ $data->id_pelanggan }}</td>
                        <td>{{ $data->pelanggan->nama }}</td>
                        <td>{{ $data->pelanggan->whatsapp ?? '-' }}</td>
                        <td>{{ rupiah($data->tagihan) }}</td>
                        <td>
                            @if($data->status === 'BL' || !isset($data->tgl_bayar))
                            <span class="badge bg-danger text-white rounded-pill">Belum Bayar</span>
                            @else
                            <span class="badge bg-success text-white rounded-pill">Lunas ({{ $data->tgl_bayar }})</span>
                            @endif
                        </td>
                        {{-- Kolom Broadcast: 2 icon WA, warna berubah sesuai broadcast_count --}}
                        <td class="text-center" id="broadcast-cell-{{ $data->id }}" data-id="{{ $data->id }}">
                            <i class="fab fa-whatsapp broadcast-icon-1
                                {{ ($data->broadcast_count ?? 0) >= 1 ? 'wa-sent' : 'wa-unsent' }}"
                               title="Broadcast ke-1{{ ($data->broadcast_count ?? 0) >= 1 ? ': Sudah dikirim' : ': Belum dikirim' }}"></i>
                            <i class="fab fa-whatsapp broadcast-icon-2
                                {{ ($data->broadcast_count ?? 0) >= 2 ? 'wa-sent' : 'wa-unsent' }}"
                               title="Broadcast ke-2{{ ($data->broadcast_count ?? 0) >= 2 ? ': Sudah dikirim' : ': Belum dikirim' }}"></i>
                        </td>
                        <td>
                            <form action="{{ route('bayar-tagihan', ['kode' => $data->id]) }}" method="POST" class="d-inline form-lunas">
                                @csrf
                                <button type="button" class="btn btn-info btn-sm btn-lunas" title="Bayar">
                                    <i class="fas fa-money-bill-wave"></i>
                                </button>
                            </form>
                            @php
                                $bulanArray = [
                                    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                                    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                                    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                                ];
                                $namaBulan = $bulanArray[(int)$data->bulan] ?? $data->bulan;
                                $periode = $namaBulan . ' ' . $data->tahun;
                                $nominalTagihan = 'Rp ' . number_format($data->tagihan, 0, ',', '.');
                                
                                $pesanWa = str_replace(
                                    ['{nama_pelanggan}', '{periode}', '{nominal_tagihan}'],
                                    [$data->pelanggan->nama, $periode, $nominalTagihan],
                                    $waTemplate ?? ''
                                );
                            @endphp
                            <a href="https://api.whatsapp.com/send?phone={{ $data->pelanggan->whatsapp }}&text={{ urlencode($pesanWa) }}"
                               target="_blank"
                               title="Pesan WhatsApp"
                               class="btn btn-success btn-sm">
                                <i class="fab fa-whatsapp"></i>
                            </a>
                            <form action="{{ route('delete-tagihan', ['id' => $data->id]) }}" method="POST" class="d-inline form-hapus">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-danger btn-sm btn-hapus" title="Hapus">
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
        <div class="text-center py-5">
            <img class="img-fluid px-3 px-sm-4 mt-3 mb-4" style="width: 20rem;" src="{{ asset('template/img/empty.svg') }}" alt="No Data">
            <p class="text-muted">Tidak ada tagihan untuk periode ini.</p>
        </div>
        @endif
    </div>
</div>

<!-- Broadcast WhatsApp Modal -->
<div class="modal fade" id="broadcastModal" tabindex="-1" role="dialog" aria-labelledby="broadcastModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="broadcastModalLabel">
                    <i class="fab fa-whatsapp"></i> Broadcast Pesan WhatsApp
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    <strong>Info:</strong> Pesan akan dikirim ke <span id="jumlahTerpilih" class="font-weight-bold">0</span> pelanggan yang dipilih.
                    Anda dapat mengedit pesan sebelum mengirim.
                </div>
                <div class="form-group">
                    <label for="pesanBroadcast"><strong>Isi Pesan:</strong></label>
                    <textarea class="form-control" id="pesanBroadcast" rows="10">{{ $waTemplate ?? '' }}</textarea>
                    <small class="form-text text-muted">
                        <i class="fas fa-lightbulb text-warning"></i> Variabel: 
                        <code>{nama_pelanggan}</code>, 
                        <code>{periode}</code>, 
                        <code>{nominal_tagihan}</code>
                    </small>
                </div>
                <div class="form-group mt-3">
                    <label for="limitBatch"><strong>Limit Per-Batch:</strong></label>
                    <div class="input-group">
                        <input type="number" class="form-control" id="limitBatch" value="20" min="1">
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary" type="button" id="btnLepasLimit">Lepaskan Limitasi</button>
                        </div>
                    </div>
                    <small class="form-text text-danger mt-1">
                        Untuk mengurangi resiko WhatsApp kena Blokir, Jangan mengirim pesan secara massal sekaligus. Silakan kirim secara berkala dengan limit di atas, lalu jeda 10-15 menit untuk batch selanjutnya.
                    </small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times"></i> Batal
                </button>
                <button type="button" class="btn btn-success" id="btnKirimBroadcast">
                    <i class="fab fa-whatsapp"></i> Kirim Sekarang
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
$(document).ready(function () {
    // Inisialisasi DataTables sendiri — pakai ID tabelTagihan
    // agar tidak konflik dengan datatables-demo.js yang pakai #dataTable
    var table = $('#tabelTagihan').DataTable({
        "pageLength": 10,
        "lengthMenu": [[10, 25, 50, 100, 200, -1], [10, 25, 50, 100, 200, "All"]],
        "columnDefs": [
            { "orderable": false, "targets": [0, 1, 7, 8] }, // checkbox, No, Broadcast, Aksi
            { "searchable": false, "targets": [0, 1, 7, 8] }
        ],
        "order": [[2, 'asc']],
        "drawCallback": function () {
            var api = this.api();
            var start = api.page.info().start;
            api.column(1, { page: 'current' }).nodes().each(function (cell, i) {
                cell.innerHTML = start + i + 1;
            });
            
            // Reset checkAll saat tabel di-redraw (pindah halaman, search, dll)
            $('#checkAll').prop('checked', false);
        }
    });

    // ==========================================
    // CheckAll handler — hanya ceklis halaman aktif
    // ==========================================
    $('#checkAll').on('change', function () {
        var isChecked = this.checked;
        // Hanya ceklis baris di halaman yang aktif/tampil saat ini
        var rows = table.rows({ page: 'current' }).nodes();
        $(rows).find('input.check-item').prop('checked', isChecked);
    });

    // ==========================================
    // Tombol Broadcast — buka modal
    // ==========================================
    $('#btnBroadcast').on('click', function () {
        var checkedIds = table.$('input.check-item:checked').map(function () {
            return $(this).val();
        }).get();

        if (checkedIds.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan',
                text: 'Pilih minimal 1 pelanggan untuk broadcast!',
            });
            return;
        }

        // Update jumlah terpilih di modal
        $('#jumlahTerpilih').text(checkedIds.length);
        $('#broadcastModal').modal('show');
    });

    $('#btnLepasLimit').on('click', function() {
        $('#limitBatch').val(table.$('input.check-item:checked').length);
    });

    // ==========================================
    // Kirim Broadcast via AJAX
    // ==========================================
    $('#btnKirimBroadcast').on('click', function () {
        var btn = $(this);
        var checkedIds = table.$('input.check-item:checked').map(function () {
            return $(this).val();
        }).get();

        var pesan = $('#pesanBroadcast').val().trim();
        var limit = parseInt($('#limitBatch').val());

        if (checkedIds.length === 0) {
            Swal.fire('Error', 'Tidak ada pelanggan yang dipilih!', 'error');
            return;
        }

        if (pesan === '') {
            Swal.fire('Error', 'Pesan tidak boleh kosong!', 'error');
            return;
        }

        // Terapkan limit batch
        var batchIds = checkedIds;
        if (!isNaN(limit) && limit > 0 && limit < checkedIds.length) {
            batchIds = checkedIds.slice(0, limit);
        }

        // Konfirmasi sebelum kirim
        Swal.fire({
            title: 'Konfirmasi Broadcast',
            html: 'Kirim pesan WhatsApp ke <strong>' + batchIds.length + '</strong> pelanggan (Batch ini)?<br><br>' + 
                  (batchIds.length < checkedIds.length ? '<small class="text-info">Tersisa ' + (checkedIds.length - batchIds.length) + ' pelanggan untuk batch selanjutnya.</small>' : ''),
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fab fa-whatsapp"></i> Ya, Kirim Batch!',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then(function (result) {
            if (result.isConfirmed) {
                // Loading state
                btn.prop('disabled', true);
                btn.html('<i class="fas fa-spinner fa-spin"></i> Mengirim...');

                $.ajax({
                    url: '{{ route("broadcast-whatsapp") }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        ids: batchIds,
                        pesan: pesan
                    },
                    success: function (response) {
                        $('#broadcastModal').modal('hide');

                        var detailHtml = 'Terkirim: <strong>' + response.sent + '</strong>, Dilewati: <strong>' + response.skipped + '</strong>';
                        if (response.errors && response.errors.length > 0) {
                            detailHtml += '<br><br><small class="text-danger">Errors:<br>' + response.errors.join('<br>') + '</small>';
                        }

                        Swal.fire({
                            icon: 'success',
                            title: 'Broadcast Selesai!',
                            html: detailHtml,
                        });

                        // Update UI icon broadcast count
                        if (response.broadcast_counts) {
                            $.each(response.broadcast_counts, function(id, count) {
                                var cell = $('#broadcast-cell-' + id);
                                if (cell.length) {
                                    if (count >= 1) {
                                        cell.find('.broadcast-icon-1')
                                            .removeClass('wa-unsent').addClass('wa-sent')
                                            .attr('title', 'Broadcast ke-1: Sudah dikirim');
                                    }
                                    if (count >= 2) {
                                        cell.find('.broadcast-icon-2')
                                            .removeClass('wa-unsent').addClass('wa-sent')
                                            .attr('title', 'Broadcast ke-2: Sudah dikirim');
                                    }
                                }
                            });
                        }

                        // Uncheck hanya yang baru saja diproses (batchIds)
                        $.each(batchIds, function(index, id) {
                            table.$('input.check-item[value="' + id + '"]').prop('checked', false);
                        });

                        // Cek apakah masih ada sisa checkbox untuk update checkAll
                        var remaining = table.$('input.check-item:checked').length;
                        if (remaining === 0) {
                            $('#checkAll').prop('checked', false);
                        }
                    },
                    error: function (xhr) {
                        var msg = 'Terjadi kesalahan saat mengirim broadcast.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        Swal.fire('Error', msg, 'error');
                    },
                    complete: function () {
                        btn.prop('disabled', false);
                        btn.html('<i class="fab fa-whatsapp"></i> Kirim Sekarang');
                    }
                });
            }
        });
    });

    // ==========================================
    // Confirm Lunas
    // ==========================================
    function confirmAction(button, title, text, confirmText, confirmColor) {
        Swal.fire({
            title: title,
            text: text,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: confirmColor,
            cancelButtonColor: '#6c757d',
            confirmButtonText: confirmText,
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then(function (result) {
            if (result.isConfirmed) {
                button.closest('form').submit();
            }
        });
    }

    // Confirm Lunas — delegasi event ke document agar tetap bekerja dengan DataTables
    $(document).on('click', '.btn-lunas', function () {
        confirmAction(
            this,
            'Konfirmasi Pembayaran',
            'Apakah Anda yakin ingin menandai tagihan ini sebagai LUNAS?',
            'Ya, Lunas!',
            '#28a745'
        );
    });

    // Confirm Hapus — delegasi event ke document
    $(document).on('click', '.btn-hapus', function () {
        confirmAction(
            this,
            'Hapus Tagihan?',
            'Data tagihan ini akan dihapus permanen!',
            'Ya, Hapus!',
            '#dc3545'
        );
    });
});
</script>
@endsection

