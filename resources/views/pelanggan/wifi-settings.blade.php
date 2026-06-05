@extends('layout.app')

@section('contents')
<div class="container-fluid">
    <style>
        @media (max-width: 576px) {
            .container-fluid { padding: 0 10px !important; }
            .card-body { padding: 1rem !important; }
        }
    </style>

    <div class="alert alert-info shadow-sm" role="alert">
        <div class="d-flex">
            <div class="mr-3"><i class="fas fa-info-circle fa-2x"></i></div>
            <div>
                <h6 class="font-weight-bold">Informasi Penting:</h6>
                <ul class="mb-0 pl-3" style="font-size: 0.9rem;">
                    <li>Kosongkan kolom <b>Password</b> jika hanya ingin mengubah nama WiFi.</li>
                    <li>Perubahan membutuhkan waktu 1-2 menit hingga router restart.</li>
                    <li>Setelah berhasil, Anda harus login ulang ke WiFi baru.</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-white">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-router"></i> Konfigurasi WiFi
            </h6>
        </div>
        <div class="card-body">
            <form action="{{ route('wifi-settings.update') }}" method="POST" id="wifi-form">
                @csrf
                
                <div class="form-group">
                    <label for="new_ssid" class="font-weight-bold">Nama WiFi (SSID)</label>
                    <input type="text" class="form-control @error('new_ssid') is-invalid @enderror" 
                           id="new_ssid" name="new_ssid" 
                           {{-- Tampilkan SSID lama, jika error 'Tidak tersedia' kosongkan saja --}}
                           value="{{ old('new_ssid', ($currentWifi['ssid'] == 'Tidak tersedia' ? '' : $currentWifi['ssid'])) }}"
                           placeholder="Masukkan Nama WiFi Baru" 
                           maxlength="32" required>
                    <small class="form-text text-muted">Nama yang akan muncul di pencarian WiFi HP Anda.</small>
                    @error('new_ssid')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <hr class="my-4">
                
                <h6 class="text-muted mb-3"><i class="fas fa-lock"></i> Ganti Password (Opsional)</h6>

                <div class="form-group">
                    <label for="new_password">Password Baru</label>
                    <div class="input-group">
                        {{-- HAPUS value, HAPUS required --}}
                        <input type="password" class="form-control @error('new_password') is-invalid @enderror" 
                               id="new_password" name="new_password" 
                               placeholder="Kosongkan jika tidak ingin mengubah password" 
                               minlength="8" autocomplete="new-password">
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary" type="button" id="toggle-new-password">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        @error('new_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <small class="form-text text-muted">Minimal 8 karakter. Biarkan kosong jika password tetap sama.</small>
                </div>

                <div class="form-group" id="confirm-box" style="display: none;"> {{-- Hide by default --}}
                    <label for="confirm_password">Konfirmasi Password Baru</label>
                    <div class="input-group">
                        <input type="password" class="form-control" 
                               id="confirm_password" name="confirm_password" 
                               placeholder="Ketik ulang password baru" 
                               minlength="8">
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary" type="button" id="toggle-confirm-password">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    <div class="invalid-feedback" id="match-error" style="display:none">
                        Password tidak cocok!
                    </div>
                </div>

                <div class="mt-4 d-flex justify-content-between align-items-center">
                    <a href="{{ route('dashboard-pelanggan') }}" class="btn btn-light text-secondary">
                        <i class="fas fa-arrow-left"></i> Batal
                    </a>
                    <button type="submit" class="btn btn-primary px-4" id="submit-btn">
                        <i class="fas fa-save mr-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
{{-- Gunakan SweetAlert agar lebih cantik (Opsional, tapi direkomendasikan jika sudah install) --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // Toggle Password Visibility
    function setupToggle(inputId, btnId) {
        const btn = document.getElementById(btnId);
        const input = document.getElementById(inputId);
        
        btn.addEventListener('click', function() {
            const icon = this.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    }

    setupToggle('new_password', 'toggle-new-password');
    setupToggle('confirm_password', 'toggle-confirm-password');

    // Logika Tampilkan Konfirmasi Password & Validasi
    const newPassInput = document.getElementById('new_password');
    const confirmBox = document.getElementById('confirm-box');
    const confirmInput = document.getElementById('confirm_password');
    const form = document.getElementById('wifi-form');

    // Jika user mulai mengetik password, tampilkan kolom konfirmasi
    newPassInput.addEventListener('input', function() {
        if (this.value.length > 0) {
            confirmBox.style.display = 'block';
            confirmInput.required = true;
        } else {
            confirmBox.style.display = 'none';
            confirmInput.required = false;
            confirmInput.value = ''; // Reset
        }
    });

    // Handle Submit
    form.addEventListener('submit', function(e) {
        e.preventDefault();

        const ssid = document.getElementById('new_ssid').value.trim();
        const pass = newPassInput.value;
        const confirm = confirmInput.value;

        // Validasi Sederhana
        if (!ssid) {
            Swal.fire('Error', 'Nama WiFi (SSID) tidak boleh kosong', 'error');
            return;
        }

        if (pass.length > 0) {
            if (pass.length < 8) {
                Swal.fire('Error', 'Password minimal 8 karakter', 'warning');
                return;
            }
            if (pass !== confirm) {
                Swal.fire('Error', 'Konfirmasi password tidak cocok', 'error');
                return;
            }
        }

        // Konfirmasi Akhir
        Swal.fire({
            title: 'Simpan Perubahan?',
            text: "Koneksi WiFi Anda akan terputus sebentar.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Simpan',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading state
                const btn = document.getElementById('submit-btn');
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
                btn.disabled = true;
                
                // Submit form native
                form.submit();
            }
        });
    });
</script>
@endpush
@endsection