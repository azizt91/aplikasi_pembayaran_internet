        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{route('dashboard-pelanggan')}}">
                <div class="sidebar-brand-icon">
                    {{-- <i class="fas fa-wifi"></i> --}}
                    <img src="{{ asset('template/img/AP.png') }}" alt="Wifi Logo" style="width: 30px; height: 30px;">
                </div>
                <div class="sidebar-brand-text mx-2">Apik Corporation</div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item {{request()->is('dashboard-pelanggan') ? 'active' : ''}}">
                <a class="nav-link" href="{{route('dashboard-pelanggan')}}">
                    <i class="fas fa-fw fa-home"></i>
                    <span>Beranda</span></a>
            </li>


            <!-- Nav Item - Profil -->
            <li class="nav-item {{request()->is('profile') ? 'active' : ''}}">
                <a class="nav-link" href="{{ route('profile') }}">
                    <i class="fas fa-fw fa-user"></i>
                    <span>Profile</span></a>
            </li>

            <!-- Nav Item - Riwayat -->
            <li class="nav-item {{request()->is('riwayat-pembayaran','tagihan/invoice-pembayaran/id') ? 'active' : ''}}">
                <a class="nav-link" href="{{ route('tagihan.riwayat_pembayaran') }}">
                    <i class="fas fa-fw fa-history"></i>
                    <span>Riwayat</span></a>
            </li>

            <!-- Nav Item - WiFi Settings -->
            <li class="nav-item {{request()->is('wifi-settings*') ? 'active' : ''}}">
                <a class="nav-link" href="{{ route('wifi-settings.index') }}">
                    <i class="fas fa-fw fa-wifi"></i>
                    <span>Pengaturan WiFi</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">

            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>

        </ul>
