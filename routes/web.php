<?php
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TagihanController;
use App\Http\Controllers\PaketController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PelangganAuthController;
use App\Http\Controllers\PengeluaranController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\TripayCallbackController;
use App\Http\Controllers\BankController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\WifiSettingController;
use App\Exports\TagihanExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/generate', function(){
   \Illuminate\Support\Facades\Artisan::call('storage:link');
   echo 'ok';
});

Route::controller(AuthController::class)->group(function () {
	Route::get('register', 'register')->name('register');
	Route::post('register', 'registerSimpan')->name('register.simpan');
	Route::get('login', 'login')->name('login');
	Route::post('login', 'loginAksi')->name('login.aksi');
	Route::get('logout', 'logout')->middleware('auth')->name('logout');
});

Route::get('/', function () {
    return view('auth/login');
});

Route::middleware('auth')->group(function () {

Route::controller(TagihanController::class)->prefix('tagihan')->group(function () {
    Route::get('', 'index')->name('tagihan');
    Route::post('/store-tagihan', 'storeTagihan')->name('store.tagihan');
    Route::get('/buka-tagihan', 'bukaTagihan')->name('buka-tagihan');
    Route::get('/data-tagihan', 'dataTagihan')->name('data-tagihan');
    Route::get('/lunas-tagihan', 'lunasTagihan')->name('lunas-tagihan');
    Route::post('/bayar-tagihan/{kode}', 'bayarTagihan')->name('bayar-tagihan');
    Route::post('/rollback-tagihan/{id}', 'rollbackTagihan')->name('rollback-tagihan');
    Route::get('/cetak-struk/{id}', 'cetakStruk')->name('cetak-struk');
    Route::delete('/delete-tagihan/{id}', 'deleteTagihan')->name('delete-tagihan');
    Route::post('/broadcast-whatsapp', 'broadcastWhatsapp')->name('broadcast-whatsapp');
});

Route::controller(PaketController::class)->prefix('paket')->group(function () {
    Route::get('', 'index')->name('paket');
    Route::get('tambah', 'tambah')->name('paket.tambah');
    Route::post('tambah', 'simpan')->name('paket.tambah.simpan');
    Route::get('edit/{id_paket}', 'edit')->name('paket.edit');
    Route::post('edit/{id_paket}', 'update')->name('paket.update');
    Route::get('hapus/{id_paket}', 'hapus')->name('paket.hapus');
});

Route::controller(PelangganController::class)->prefix('pelanggan')->group(function () {
    Route::get('', 'index')->name('pelanggan');
    Route::get('tambah', 'tambah')->name('pelanggan.tambah');
    Route::post('tambah', 'simpan')->name('pelanggan.tambah.simpan');
    Route::get('edit/{id_pelanggan}', 'edit')->name('pelanggan.edit');
    Route::put('edit/{id_pelanggan}', 'update')->name('pelanggan.update');
    Route::delete('hapus/{id_pelanggan}', 'hapus')->name('pelanggan.hapus');
    Route::get('pelanggan/{id_pelanggan}', 'show')->name('pelanggan.show');
});


// Route::controller(DashboardController::class)->prefix('dashboard')->group(function () {
//     Route::get('', 'dashboard')->name('dashboard');
// });

Route::get('/dashboard', [DashboardController::class, 'showDashboard'])->name('dashboard');


Route::controller(\App\Http\Controllers\UserController::class)->prefix('users')->group(function () {
    Route::get('', 'index')->name('users.index');
    Route::get('/create', 'create')->name('users.create');
    Route::post('', 'store')->name('users.store');
    Route::get('/{user}/edit', 'edit')->name('users.edit');
    Route::put('/{id}', 'update')->name('users.update');
    Route::delete('/{user}', 'destroy')->name('users.destroy');
});

// Settings Routes
Route::controller(SettingController::class)->prefix('settings')->group(function () {
    Route::get('', 'index')->name('settings.index');
    Route::post('/genieacs', 'updateGenieACS')->name('settings.genieacs.update');
    Route::get('/genieacs/test', 'testGenieACS')->name('settings.genieacs.test');
    Route::post('/fonnte', 'updateFonnte')->name('settings.fonnte.update');
});
});

// Route lama pelanggan login - redirect ke unified login
Route::get('/pelanggan-login', function() {
    return redirect()->route('login');
})->name('pelanggan.login');
Route::middleware('auth:pelanggan')->group(function () {
Route::controller(PelangganAuthController::class)->group(function () {
    Route::get('/dashboard-pelanggan', 'dashboard')->name('dashboard-pelanggan');
    Route::get('/belum-lunas', 'belumLunas')->name('tagihan.belum_lunas');
    Route::get('/sudah-lunas', 'sudahLunas')->name('tagihan.sudah_lunas');
    Route::get('/riwayat-pembayaran', 'riwayatPembayaran')->name('tagihan.riwayat_pembayaran');
    Route::get('/profile', 'profile')->name('profile');
    Route::get('/profile/edit', 'editProfile')->name('edit_profile');
    Route::post('/profile/update', 'updateProfile')->name('update_profile');
    Route::get('/profile/show', 'showProfile')->name('show_profile');
    Route::post('/profile/upload-picture', 'uploadProfilePicture')->name('profile.picture.upload');
    Route::get('/tagihan/invoice-pembayaran/{id}', 'invoicePembayaran')->name('tagihan.invoice_pembayaran');
});
    Route::post('/transaction', [TransactionController::class, 'store'])->name('transaction.store');
    Route::get('/transaction/{reference}', [TransactionController::class, 'show'])->name('transaction.show');
    
    // WiFi Settings Routes
    Route::controller(WifiSettingController::class)->prefix('wifi-settings')->group(function () {
        Route::get('', 'index')->name('wifi-settings.index');
        Route::post('', 'update')->name('wifi-settings.update');
        Route::delete('/{id}', 'destroy')->name('wifi-settings.destroy');
    });
});

// Unified logout untuk admin dan pelanggan
Route::post('/logout-pelanggan', [AuthController::class, 'logout'])->name('pelanggan.logout');
Route::post('/cetak-struk/{id}', [TagihanController::class, 'cetakStruk'])->name('cetak.struk');
Route::get('/generate-pdf/{id}', [TagihanController::class, 'generatePdf'])->name('generate-pdf');
Route::get('/pelanggan-lunas', [TagihanController::class, 'lunas'])->name('pelanggan.lunas');
Route::get('/pelanggan-belum-lunas', [TagihanController::class, 'belumLunas'])->name('pelanggan.belumLunas');
Route::get('/pelanggan/aktif', [PelangganController::class, 'aktif'])->name('pelanggan.aktif');
Route::get('/pelanggan/nonaktif', [PelangganController::class, 'nonaktif'])->name('pelanggan.nonaktif');
Route::get('/paket/view', [PaketController::class, 'viewPaket'])->name('paket.view');
Route::resource('pengeluaran', PengeluaranController::class)->except(['show']);

Route::post('/callback', [TripayCallbackController::class, 'handle']);


Route::resource('banks', BankController::class);

Route::get('/tagihan/{id}/payment', [PelangganAuthController::class, 'showPaymentPage'])->name('payment');
Route::get('/pembayaran', [PelangganAuthController::class, 'showPembayaranPage'])->name('pembayaran');

Route::get('export-tagihan/{bulan}/{tahun}', function ($bulan, $tahun) {
    $fileName = "tagihan_{$bulan}_{$tahun}.xlsx"; // Buat nama file dinamis
    return Excel::download(new TagihanExport($bulan, $tahun), $fileName, \Maatwebsite\Excel\Excel::XLSX);
})->name('export-tagihan');















