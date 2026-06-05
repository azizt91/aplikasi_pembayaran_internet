<!DOCTYPE html>
<html lang="en">

<head>

  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>Apik Corporation - Login</title>
  
  <!-- PWA  -->
  <meta name="theme-color" content="#ffffff"/>
  <link rel="apple-touch-icon" href="{{ asset('template') }}/img/logo_ac.png">
  <link rel="manifest" href="{{ asset('/manifest.json') }}">

  <!-- Custom fonts for this template-->
  <link href="{{ asset('template/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
  <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
  <link rel="icon" type="image" href="{{ asset('template/img/AP.png') }}"/>

  <!-- Custom styles for this template-->
  <link href="{{ asset('template/css/sb-admin-2.min.css') }}" rel="stylesheet">

</head>

<body class="bg-gradient-primary">

  <div class="container">

    <!-- Outer Row -->
    <div class="row justify-content-center">

      <div class="col-xl-10 col-lg-12 col-md-9">

        <div class="card o-hidden border-0 shadow-lg my-5">
          <div class="card-body p-0">
            <!-- Nested Row within Card Body -->
            <div class="row">
              <div class="col-lg-6 d-none d-lg-block bg-login-image"></div>
              <div class="col-lg-6">
                <div class="p-5">
                  <div class="text-center">
                    <img src="{{ asset('template') }}/img/AP.png" alt="Logo" class="img-fluid logo" style="max-width: 75px;">
                    <h1 class="h4 text-gray-900 mb-4">Selamat Datang!</h1>
                    <p class="text-muted small mb-4">Silakan masuk ke akun Anda</p>
                  </div>
                  <form action="{{ route('login.aksi') }}" method="POST" class="user">
                    @csrf
										@if ($errors->any())
										<div class="alert alert-danger">
											<ul>
												@foreach ($errors->all() as $error)
												<li>{{ $error }}</li>
												@endforeach
											</ul>
										</div>
										@endif
                    <div class="form-group">
                      <input name="email" type="email" class="form-control form-control-user" id="exampleInputEmail" aria-describedby="emailHelp" placeholder="Masukkan Alamat Email...">
                    </div>
                    <div class="form-group">
                      <input name="password" type="password" class="form-control form-control-user" id="exampleInputPassword" placeholder="Password">
                    </div>
                    <div class="form-group">
                      <div class="custom-control custom-checkbox small">
                        <input name="remember" type="checkbox" class="custom-control-input" id="customCheck">
                        <label class="custom-control-label" for="customCheck">Ingat Saya</label>
                      </div>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block btn-user">Login</button>

                  </form>
                  <hr>
                  <div class="text-center">
                    <small class="text-muted">
                      <i class="fas fa-info-circle"></i> 
                      Lupa username atau password? 
                      <a href="https://wa.me/6285169968884?text=Halo%20Admin%2C%20saya%20mengalami%20kendala%20login.%20Saya%20lupa%20username%2Fpassword%20akun%20saya.%20Mohon%20bantuannya.%20Terima%20kasih." 
                         target="_blank" 
                         class="text-primary font-weight-bold">
                        <i class="fab fa-whatsapp"></i> Hubungi Admin
                      </a>
                    </small>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>

    </div>

  </div>

  <!-- Bootstrap core JavaScript-->
  <script src="{{ asset('template/vendor/jquery/jquery.min.js') }}"></script>
  <script src="{{ asset('template/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

  <!-- Core plugin JavaScript-->
  <script src="{{ asset('template/vendor/jquery-easing/jquery.easing.min.js') }}"></script>

  <!-- Custom scripts for all pages-->
  <script src="{{ asset('template/js/sb-admin-2.min.js') }}"></script>

  <!-- PWA Service Worker -->
  <script src="{{ asset('/sw.js') }}"></script>
  <script>
    if ("serviceWorker" in navigator) {
        navigator.serviceWorker.register("/sw.js").then(
        (registration) => {
          console.log("Service worker registration succeeded:", registration);
        },
        (error) => {
          console.error(`Service worker registration failed: ${error}`);
        },
      );
    } else {
      console.error("Service workers are not supported.");
    }
  </script>
</body>

</html>
