@extends('template.app') 

@section('title', 'Bayar Tagihan')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">BAYAR TAGIHAN</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse">
                            <i class="fa fa-minus"></i>
                        </button>
                        <button type="button" class="btn btn-box-tool" data-widget="remove">
                            <i class="fa fa-remove"></i>
                        </button>
                    </div>
                </div>

                @if(isset($kode))
                    <?php
                    $tanggal = now();

                    $tagihan = \App\Models\Tagihan::find($kode);

                    if ($tagihan) {
                        $tagihan->status = 'LS';
                        $tagihan->tgl_bayar = $tanggal;
                        $tagihan->save();

                        echo "<script>
                            Swal.fire({
                                title: 'Pembayaran Berhasil',
                                text: '',
                                icon: 'success',
                                confirmButtonText: 'OK'
                            }).then((result) => {
                                if (result.value) {
                                    window.location = '{{ route('lunas-tagihan') }}';
                                }
                            })
                        </script>";
                    } else {
                        echo "<script>
                            Swal.fire({
                                title: 'Pembayaran Gagal',
                                text: '',
                                icon: 'error',
                                confirmButtonText: 'OK'
                            }).then((result) => {
                                if (result.value) {
                                    window.location = '{{ route('lunas-tagihan') }}';
                                }
                            })
                        </script>";
                    }
                    ?>
                @endif
            </div>
        </div>
    </div>
@endsection
