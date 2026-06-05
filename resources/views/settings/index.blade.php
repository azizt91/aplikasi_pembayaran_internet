@extends('template.app')

@section('title', 'Pengaturan Aplikasi')

@section('contents')
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-cog"></i> Konfigurasi GenieACS
        </h6>
    </div>
    <div class="card-body">
        <!-- Info Card -->
        <div class="alert alert-info" role="alert">
            <i class="fas fa-info-circle"></i>
            <strong>Informasi:</strong> GenieACS digunakan untuk mengizinkan pelanggan mengganti SSID dan Password WiFi mereka sendiri.
        </div>

        <form action="{{ route('settings.genieacs.update') }}" method="POST">
            @csrf
            
            <!-- Enable/Disable GenieACS -->
            <div class="form-group">
                <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" id="genieacs_enabled" name="genieacs_enabled" 
                           {{ ($settings['genieacs_enabled'] ?? 'false') === 'true' ? 'checked' : '' }}>
                    <label class="custom-control-label" for="genieacs_enabled">
                        <strong>Aktifkan GenieACS</strong>
                    </label>
                </div>
                <small class="form-text text-muted">Izinkan pelanggan mengganti SSID & Password WiFi</small>
            </div>

            <hr>

            <!-- GenieACS URL -->
            <div class="form-group">
                <label for="genieacs_url">URL GenieACS <span class="text-danger">*</span></label>
                <input type="url" class="form-control @error('genieacs_url') is-invalid @enderror" 
                       id="genieacs_url" name="genieacs_url" 
                       value="{{ old('genieacs_url', $settings['genieacs_url'] ?? '') }}"
                       placeholder="http://192.168.1.10:7547 atau http://acs.example.com">
                <small class="form-text text-muted">URL server GenieACS Anda</small>
                @error('genieacs_url')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- GenieACS Username -->
            <div class="form-group">
                <label for="genieacs_username">Username (Opsional)</label>
                <input type="text" class="form-control @error('genieacs_username') is-invalid @enderror" 
                       id="genieacs_username" name="genieacs_username" 
                       value="{{ old('genieacs_username', $settings['genieacs_username'] ?? '') }}"
                       placeholder="Kosongkan jika tidak ada autentikasi">
                <small class="form-text text-muted">Username untuk akses GenieACS API (jika diperlukan)</small>
                @error('genieacs_username')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- GenieACS Password -->
            <div class="form-group">
                <label for="genieacs_password">Password (Opsional)</label>
                <div class="input-group">
                    <input type="password" class="form-control @error('genieacs_password') is-invalid @enderror" 
                           id="genieacs_password" name="genieacs_password" 
                           placeholder="Kosongkan jika tidak ingin mengubah">
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary" type="button" id="toggle-password">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
                <small class="form-text text-muted">Password untuk akses GenieACS API (jika diperlukan)</small>
                @error('genieacs_password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <hr>

            <!-- Action Buttons -->
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan Pengaturan
                </button>
                <button type="button" class="btn btn-info" id="test-connection-btn">
                    <i class="fas fa-plug"></i> Test Koneksi
                </button>
                <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Fonnte WhatsApp Settings Card -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-success">
            <i class="fab fa-whatsapp"></i> Pengaturan Fonnte WhatsApp
        </h6>
    </div>
    <div class="card-body">
        <!-- Info Card -->
        <div class="alert alert-success" role="alert">
            <i class="fas fa-info-circle"></i>
            <strong>Informasi:</strong> Fonnte digunakan untuk mengirim broadcast pesan WhatsApp ke pelanggan. Dapatkan token di <a href="https://fonnte.com" target="_blank">fonnte.com</a>.
        </div>

        <form action="{{ route('settings.fonnte.update') }}" method="POST">
            @csrf

            <!-- Fonnte Token -->
            <div class="form-group">
                <label for="fonnte_token">Token Fonnte <span class="text-danger">*</span></label>
                <div class="input-group">
                    <input type="password" class="form-control" 
                           id="fonnte_token" name="fonnte_token" 
                           value="{{ old('fonnte_token', $appSetting->fonnte_token ?? '') }}"
                           placeholder="Masukkan token Fonnte Anda">
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary" type="button" id="toggle-fonnte-token">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
                <small class="form-text text-muted">Token API dari akun Fonnte Anda</small>
            </div>

            <!-- WhatsApp Template -->
            <div class="form-group">
                <label for="wa_template">Template Pesan WhatsApp</label>
                <textarea class="form-control" id="wa_template" name="wa_template" rows="8"
                          placeholder="Tulis template pesan WhatsApp...">{{ old('wa_template', $appSetting->wa_template ?? '') }}</textarea>
                <small class="form-text text-muted">
                    <i class="fas fa-lightbulb text-warning"></i> Anda dapat menggunakan variabel dinamis berikut:<br>
                    <code>{nama_pelanggan}</code> — Nama pelanggan<br>
                    <code>{periode}</code> — Periode tagihan (misal: Juni 2026)<br>
                    <code>{nominal_tagihan}</code> — Nominal tagihan (misal: Rp 150.000)
                </small>
            </div>

            <hr>

            <!-- Action Buttons -->
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i> Simpan Pengaturan Fonnte
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Test Result Modal -->
<div class="modal fade" id="testResultModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Hasil Test Koneksi</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" id="test-result-body">
                <!-- Result will be inserted here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
// Toggle password visibility
document.getElementById('toggle-password').addEventListener('click', function() {
    const passwordInput = document.getElementById('genieacs_password');
    const icon = this.querySelector('i');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
});

// Test connection
document.getElementById('test-connection-btn').addEventListener('click', function() {
    const btn = this;
    const originalHtml = btn.innerHTML;
    
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Testing...';
    
    fetch('{{ route("settings.genieacs.test") }}')
        .then(response => response.json())
        .then(data => {
            const resultBody = document.getElementById('test-result-body');
            
            if (data.success) {
                resultBody.innerHTML = `
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> ${data.message}
                    </div>
                `;
            } else {
                resultBody.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-times-circle"></i> ${data.message}
                    </div>
                `;
            }
            
            $('#testResultModal').modal('show');
        })
        .catch(error => {
            const resultBody = document.getElementById('test-result-body');
            resultBody.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-times-circle"></i> Error: ${error.message}
                </div>
            `;
            $('#testResultModal').modal('show');
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = originalHtml;
        });
});

// Toggle Fonnte token visibility
document.getElementById('toggle-fonnte-token').addEventListener('click', function() {
    const tokenInput = document.getElementById('fonnte_token');
    const icon = this.querySelector('i');
    
    if (tokenInput.type === 'password') {
        tokenInput.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        tokenInput.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
});
</script>
@endsection

@endsection
