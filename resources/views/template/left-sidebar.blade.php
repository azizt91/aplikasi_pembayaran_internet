        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{route('dashboard')}}">
                <div class="sidebar-brand-icon">
                    {{-- <i class="fas fa-wifi"></i> --}}
                    <img src="{{ asset('template/img/AP.png') }}" alt="Wifi Logo" style="width: 30px; height: 30px;">
                </div>
                <div class="sidebar-brand-text mx-2">Apik Corporation</div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item {{request()->is('dashboard') ? 'active' : ''}}">
                <a class="nav-link" href="{{route('dashboard')}}">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                Olah Data
            </div>

            <!-- Nav Item - Data Paket -->
            <li class="nav-item {{request()->is('paket','paket/tambah') ? 'active' : ''}}">
                <a class="nav-link" href="{{ route('paket') }}">
                    <i class="fas fa-fw fa-paper-plane"></i>
                    <span>Data Paket</span></a>
            </li>

            <!-- Nav Item - Data Pelanggan -->
            <li class="nav-item {{request()->is('pelanggan','pelanggan/tambah') ? 'active' : ''}}">
                <a class="nav-link" href="{{ route('pelanggan') }}">
                    <i class="fas fa-fw fa-user-friends"></i>
                    <span>Data Pelanggan</span></a>
            </li>

            <!-- Nav Item - Pages Collapse Menu
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTwo"
                    aria-expanded="true" aria-controls="collapseTwo">
                    <i class="fas fa-fw fa-cog"></i>
                    <span>Components</span>
                </a>
                <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Custom Components:</h6>
                        <a class="collapse-item" href="buttons.html">Buttons</a>
                        <a class="collapse-item" href="cards.html">Cards</a>
                    </div>
                </div>
            </li> -->

            <!-- Nav Item - Utilities Collapse Menu -->
            <!-- <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseUtilities"
                    aria-expanded="true" aria-controls="collapseUtilities">
                    <i class="fas fa-fw fa-wrench"></i>
                    <span>Utilities</span>
                </a>
                <div id="collapseUtilities" class="collapse" aria-labelledby="headingUtilities"
                    data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Custom Utilities:</h6>
                        <a class="collapse-item" href="utilities-color.html">Colors</a>
                        <a class="collapse-item" href="utilities-border.html">Borders</a>
                        <a class="collapse-item" href="utilities-animation.html">Animations</a>
                        <a class="collapse-item" href="utilities-other.html">Other</a>
                    </div>
                </div>
            </li> -->

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                TAGIHAN & PEMBAYARAN
            </div>

            <!-- Nav Item - Pages Collapse Menu -->
            <!-- <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePages"
                    aria-expanded="true" aria-controls="collapsePages">
                    <i class="fas fa-fw fa-folder"></i>
                    <span>Pages</span>
                </a>
                <div id="collapsePages" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Login Screens:</h6>
                        <a class="collapse-item" href="login.html">Login</a>
                        <a class="collapse-item" href="register.html">Register</a>
                        <a class="collapse-item" href="forgot-password.html">Forgot Password</a>
                        <div class="collapse-divider"></div>
                        <h6 class="collapse-header">Other Pages:</h6>
                        <a class="collapse-item" href="404.html">404 Page</a>
                        <a class="collapse-item" href="blank.html">Blank Page</a>
                    </div>
                </div>
            </li> -->

            <!-- Nav Item - Buat Tagihan -->
            <li class="nav-item {{request()->is('tagihan') ? 'active' : ''}}">
                <a class="nav-link" href="{{ route('tagihan') }}">
                    <i class="fas fa-fw fa-edit"></i>
                    <span>Buat Tagihan</span></a>
            </li>

            <!-- Nav Item - Data Tagihan -->
            <li class="nav-item {{request()->is('tagihan/buka-tagihan') ? 'active' : ''}}">
                <a class="nav-link" href="{{ route('buka-tagihan') }}">
                    <i class="fas fa-fw fa-table"></i>
                    <span>Data Tagihan</span></a>
            </li>

            <!-- Nav Item - Pembayaran Lunas -->
            <li class="nav-item {{request()->is('tagihan/lunas-tagihan') ? 'active' : ''}}">
                <a class="nav-link" href="{{ route('lunas-tagihan') }}">
                    <i class="fas fa-fw fa-money-bill-wave"></i>
                    <span>Pembayaran Lunas</span></a>
            </li>

            <!-- Nav Item - Pembayaran Lunas -->
            <li class="nav-item {{request()->is('banks') ? 'active' : ''}}">
                <a class="nav-link" href="{{ route('banks.index') }}">
                    {{-- <i class="fas fa-fw fa-money-bill-wave"></i> --}}
                    <i class="fas fa-fw fa-university"></i>
                    <span>Rekening Bank</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                OTHER
            </div>

            <!-- Nav Item - Cara Pembayaran -->
            <li class="nav-item {{request()->is('users','users/create') ? 'active' : ''}}">
                <a class="nav-link" href="{{ route('users.index') }}">
                    <i class="fas fa-fw fa-user"></i>
                    <span>Pengguna Sistem</span></a>
            </li>

            <!-- Nav Item - Pengeluaran -->
            <li class="nav-item {{ request()->is('pengeluaran*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('pengeluaran.index') }}">
                    <i class="fas fa-fw fa-money-bill-wave"></i>
                    <span>Pengeluaran</span>
                </a>
            </li>

            <!-- Nav Item - Settings -->
            <li class="nav-item {{ request()->is('settings*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('settings.index') }}">
                    <i class="fas fa-fw fa-cog"></i>
                    <span>Pengaturan</span>
                </a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">

            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>


            <!-- Sidebar Message -->
            <!-- <div class="sidebar-card d-none d-lg-flex">
                <img class="sidebar-card-illustration mb-2" src="img/undraw_rocket.svg" alt="...">
                <p class="text-center mb-2"><strong>SB Admin Pro</strong> is packed with premium features, components, and more!</p>
                <a class="btn btn-success btn-sm" href="https://startbootstrap.com/theme/sb-admin-pro">Upgrade to Pro!</a>
            </div> -->

        </ul>
        <!-- End of Sidebar -->
