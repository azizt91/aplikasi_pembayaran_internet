@extends('layout.app')

@section('contents')
<div class="row">
    <div class="col-xl-4">
        <!-- Profile picture card-->
       
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Foto Profile</h6>	
                </div>
                    <div class="card-body text-center">
                        <!-- Profile picture image-->
                        <img class="img-account-profile rounded-circle mb-2" src="{{ asset('storage/' . $pelanggan->profile_picture) }}" alt="Profile Picture" style="width: 100px; height: 100px;"/>
                        <!-- Profile picture help block-->
                        <div class="small font-italic text-muted mb-4">JPG or PNG no larger than 5 MB</div>
                        <!-- Profile picture upload button-->
                        <form action="{{ route('profile.picture.upload') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input class=" mb-3" type="file" id="inputProfilePicture" name="profile_picture" accept="image/*">
                            <button type="submit" class="btn btn-primary">Upload Picture</button>
                        </form>
                    </div>
            </div>
    </div>
    
    <div class="col-xl-8">
        <!-- Account details card-->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Detail Akun</h6>	
                </div>
            <div class="card-body">
                <form method="POST" action="{{ route('update_profile') }}" enctype="multipart/form-data">
                    @csrf
                    <!-- Form Group (username)-->
                    <div class="mb-3">
                        <label class="small mb-1" for="inputUsername">Nama</label>
                        <input class="form-control" id="inputUsername" name="nama" type="text" placeholder="Enter your name" value="{{ $pelanggan->nama }}" />
                    </div>
                    <div class="mb-3">
                        <label class="small mb-1" for="inputLocation">Alamat</label>
                        <input class="form-control" id="inputLocation" name="alamat" type="text" placeholder="Enter your location" value="{{ $pelanggan->alamat }}" />                            
                    </div>
                    <div class="mb-3"> 
                        <label class="small mb-1" for="inputPhone">WhatsApp</label>
                        <input class="form-control" id="inputPhone" name="whatsapp" type="tel" placeholder="Enter your phone number" value="{{ $pelanggan->whatsapp }}" />                       
                    </div>
                    <div class="mb-3"> 
                        <label class="small mb-1" for="inputEmailAddress">Email address</label>
                        <input class="form-control" id="inputEmailAddress" name="email" type="email" placeholder="Enter your email address" value="{{ $pelanggan->email }}" />                       
                    </div>
                    <div class="mb-3"> 
                        <label class="small mb-1" for="newPassword">New Password (Password harus berupa 4 digit angka)</label>
                        <input class="form-control" id="password" name="password" type="password" placeholder="Enter new password" />                       
                    </div>
                    <!-- Save changes button-->
                    <button class="btn btn-primary" type="submit">Save changes</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection