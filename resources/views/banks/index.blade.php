@extends('template.app')

@section('contents')
<div class="card shadow mb-4">
  <div class="card-header py-3">
    <h6 class="m-0 font-weight-bold text-primary">Data Bank</h6>  
  </div>
  <div class="card-body">
    <a href="{{ route('banks.create') }}" class="btn btn-primary mb-3"><i class="fas fa-fw fa-plus"></i> Bank</a>
    <div class="table-responsive">
      <table class="table table-striped table-bordered table-sm" id="dataTable">
        <thead>
          <tr>
            <th scope="col">#</th>
            <th scope="col">{{ __('Icon') }}</th>
            <th scope="col">{{ __('Nama Bank') }}</th>
            <th scope="col">{{ __('Pemilik Rekening') }}</th>
            <th scope="col">{{ __('Nomor Rekening') }}</th>
            <th scope="col">{{ __('Actions') }}</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($banks as $bank)
            <tr>
              <td>{{ $loop->iteration }}</td>
              <td>
                  
                <img src="{{ $bank->url_icon }}" style="width: 100px; height: 50px;">
              </td>
              <td><span class="fw-normal">{{ $bank->nama_bank }}</span></td>
              <td><span class="fw-normal">{{ $bank->pemilik_rekening }}</span></td>
              <td><span class="fw-normal">{{ $bank->nomor_rekening }}</span></td>
              <td>
                <a href="{{ route('banks.edit', $bank->id) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                <button onclick="confirmDelete('{{ route('banks.destroy', $bank->id) }}')" class="btn btn-danger btn-sm">
                    <i class="fas fa-trash"></i>
                </button>
              </td>
            </tr>              
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>

<script>
    function confirmDelete(url) {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Anda tidak akan bisa mengembalikan ini!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Membuat dan mengirim form secara dinamis
                var form = document.createElement('form');
                form.action = url;
                form.method = 'POST';
                form.innerHTML = `
                    @csrf
                    @method("DELETE")
                `;
                document.body.appendChild(form);
                form.submit();
            }
        })
    }
</script>

@endsection