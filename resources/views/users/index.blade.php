@extends('template.app')

@section('contents')
  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-primary">Pengguna Sistem</h6>	
    </div>
    <div class="card-body">
      <a href="{{ route('users.create') }}" class="btn btn-primary mb-3"><i class="fas fa-fw fa-plus"></i> User</a>
      <div class="table-responsive">
        <table class="table table-striped table-bordered table-sm" id="dataTable">
          <thead>
            <tr>
              <th scope="col">#</th>
              <th scope="col">{{ __('Nama') }}</th>
              <th scope="col">{{ __('Email') }}</th>
              <th scope="col">{{ __('Level') }}</th>
              <th scope="col">{{ __('Actions') }}</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($users as $user)
              <tr>
                <td>{{ $loop->iteration }}</td>
                <td><span class="fw-normal">{{ $user->nama }}</span></td>
                <td><span class="fw-normal">{{ $user->email }}</span></td>
                <td><span class="fw-normal">{{ $user->level }}</span></td>
                <td>
                    <a href="{{ route('users.edit', $user->id) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                    <form action="{{ route('users.destroy', $user->id) }}" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i></button>
                    </form>
                </td>
              </tr>              
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
@endsection
