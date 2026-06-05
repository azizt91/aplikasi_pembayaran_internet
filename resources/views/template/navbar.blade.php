<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
    <div class="navbar-nav align-items-center">
        <div class="nav-item d-flex align-items-center">
            <span class="d-none d-md-block">Jam :</span>&nbsp;
          <div class="clock d-none d-md-block"></div>
        </div>
    </div>

                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                          <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                              data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                              <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                              {{ auth()->user()->nama ?? '' }}
                              <br>
                              <small>{{ auth()->user()->level ?? '' }}</small>
                              </span>
                              @php
                              $profilePicturePath = auth()->user()->profile_picture ? asset('storage/' . auth()->user()->profile_picture) : asset('template/img/undraw_profile.svg');
                              @endphp
                              <img class="img-profile rounded-circle"
                                  src="{{ $profilePicturePath }}" style="width: 50px; height: 50px;">
                          </a>
                          <!-- Dropdown - User Information -->
                          <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                              aria-labelledby="userDropdown">
                              <h6 class="dropdown-header d-flex align-items-center">
                                  <img class="img-profile rounded-circle mr-3" src="{{ $profilePicturePath }}" style="width: 50px; height: 50px;"/>
                                  <div class="dropdown-user-details">
                                      <div class="dropdown-user-details-name text-gray-800">{{ auth()->user()->nama ?? '' }}</div>
                                      <div class="dropdown-user-details-email">{{ auth()->user()->email ?? '' }}</div>
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

                <script>
                    function clock() {
                        var time = new Date(),
                            hours = time.getHours(),
                            minutes = time.getMinutes(),
                            seconds = time.getSeconds();

                        var ampm = hours >= 12 ? 'PM' : 'AM'; // Menentukan apakah pagi atau sore

                        hours = hours % 12;
                        hours = hours ? hours : 12; // Format jam 12 jam

                        document.querySelectorAll('.clock')[0].innerHTML = harold(hours) + ":" + harold(minutes) + ":" + harold(seconds) + " " + ampm;

                        function harold(standIn) {
                            if (standIn < 10) {
                                standIn = '0' + standIn
                            }
                            return standIn;
                        }
                    }
                    setInterval(clock, 1000);
                </script>
