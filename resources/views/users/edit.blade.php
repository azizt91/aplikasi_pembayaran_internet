{{-- @extends('template.app')

@section('contents')
<div class="row">
    <div class="col-12">
      <div class="card shadow mb-4">
        <div class="card-header py-3">
          <h6 class="m-0 font-weight-bold text-primary">Edit User</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('users.update', ['id' => $user->id]) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label for="nama">Nama</label>
                    <input id="nama" type="text" class="form-control @error('nama') is-invalid @enderror" name="nama" value="{{ old('nama', $user->nama) }}" required autocomplete="nama" autofocus>
                  @error('nama')
                    <span class="invalid-feedback">{{ $message }}</span>
                  @enderror
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email', $user->email) }}" required autocomplete="email">
                    @error('email')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" placeholder="{{ __('New Password') }}" required autocomplete="new-password">
                    @error('password')
                      <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="password-confirm">Password Confirm</label>
                    <input id="password-confirm" type="password" class="form-control" name="password_confirmation" placeholder="{{ __('Confirm New Password') }}" required autocomplete="new-password">
                    @error('password-confirm')
                      <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
                <div class="card-footer">
                    <a href="{{ route('users.index') }}" class="btn btn-secondary">Batal</a>
                    <input type="submit" name="Simpan" value="Update" class="btn btn-primary">
                </div>
            </form>
        </div>
      </div>
    </div>
</div>

@endsection --}}

@extends('template.app')

@section('contents')
<div class="row">
  <div class="col-12">
    <div class="card shadow mb-4">
      <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Edit User</h6>
      </div>
      <div class="card-body">
          <form action="{{ route('users.update', ['id' => $user->id]) }}" method="POST" enctype="multipart/form-data">
              @csrf
              @method('PUT')
              <div class="form-group">
                  <label for="nama">Nama</label>
                  <input id="nama" type="text" class="form-control @error('nama') is-invalid @enderror" name="nama" value="{{ old('nama', $user->nama) }}" required autocomplete="nama" autofocus>
                @error('nama')
                  <span class="invalid-feedback">{{ $message }}</span>
                @enderror
              </div>
              <div class="form-group">
                  <label for="email">Email</label>
                  <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email', $user->email) }}" required autocomplete="email">
                  @error('email')
                      <span class="invalid-feedback">{{ $message }}</span>
                  @enderror
              </div>
              <div class="form-group">
                  <label for="password">Password</label>
                  <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" placeholder="{{ __('New Password') }}" autocomplete="new-password">
                  @error('password')
                    <span class="invalid-feedback">{{ $message }}</span>
                  @enderror
              </div>
              <div class="form-group">
                  <label for="password-confirm">Password Confirm</label>
                  <input id="password-confirm" type="password" class="form-control" name="password_confirmation" placeholder="{{ __('Confirm New Password') }}" autocomplete="new-password">
                  @error('password-confirm')
                    <span class="invalid-feedback">{{ $message }}</span>
                  @enderror
              </div>
              <div class="form-group">
                  <label for="profile_picture">Foto Profil</label>
                  <input id="profile_picture" type="file" class="form-control @error('profile_picture') is-invalid @enderror" name="profile_picture">
                  @error('profile_picture')
                    <span class="invalid-feedback">{{ $message }}</span>
                  @enderror
                  @if($user->profile_picture)
                    <div class="mt-2">
                        <img src="{{ asset('storage/' . $user->profile_picture) }}" class="img-profile rounded-circle" style="width: 100px; height: 100px;">
                    </div>
                  @endif
              </div>
              <div class="card-footer">
                  <a href="{{ route('users.index') }}" class="btn btn-secondary">Batal</a>
                  <input type="submit" name="Simpan" value="Update" class="btn btn-primary">
              </div>
          </form>
      </div>
    </div>
  </div>
</div>

@endsection
