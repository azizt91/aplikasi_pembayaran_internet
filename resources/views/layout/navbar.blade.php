<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>


                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">

                        {{-- <div class="topbar-divider d-none d-sm-block"></div> --}}

                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                @php
                                    // Check which guard is authenticated
                                    $user = auth()->check() ? auth()->user() : auth()->guard('pelanggan')->user();
                                    $profilePicturePath = ($user && $user->profile_picture) 
                                        ? asset('storage/' . $user->profile_picture) 
                                        : asset('template/img/undraw_profile.svg');
                                @endphp
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                                {{ $user->nama ?? '' }}
                                <br>
                                <small>{{ $user->level ?? ($user->status ?? '') }}</small>
                                </span>
                                <img class="img-profile rounded-circle"
                                    src="{{ $profilePicturePath }}" style="width: 50px; height: 50px;">
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">
                                <h6 class="dropdown-header d-flex align-items-center">
                                    <img class="img-profile rounded-circle mr-3" src="{{ $profilePicturePath }}" style="width: 50px; height: 50px;"/>
                                    <div class="dropdown-user-details">
                                        <div class="dropdown-user-details-name text-gray-800">{{ $user->nama ?? '' }}</div>
                                        <div class="dropdown-user-details-email">{{ $user->email ?? '' }}</div>
                                    </div>
                                </h6>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-danger"></i>
                                    Logout
                                </a>
                                @include('sweetalert::alert')
                            </div>
                        </li>



                    </ul>
                </nav>
