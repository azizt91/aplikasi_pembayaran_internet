<?php
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TagihanController;
use App\Http\Controllers\PaketController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PelangganAuthController;
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


Route::controller(AuthController::class)->group(function () {
	Route::get('register', 'register')->name('register');
	Route::post('register', 'registerSimpan')->name('register.simpan');
	Route::get('login', 'login')->name('login');
	Route::post('login', 'loginAksi')->name('login.aksi');
	Route::get('logout', 'logout')->middleware('auth')->name('logout');
});

Route::get('/', function () {
    return view('auth/pelanggan-login');
});

Route::middleware('auth')->group(function () {

Route::prefix('tagihan')->group(function () {
    Route::get('', [TagihanController::class, 'index'])->name('tagihan');
    Route::post('/store-tagihan', [TagihanController::class, 'storeTagihan'])->name('store.tagihan');
    Route::get('/buka-tagihan', [TagihanController::class, 'bukaTagihan'])->name('buka-tagihan');
    Route::get('/data-tagihan', [TagihanController::class, 'dataTagihan'])->name('data-tagihan');
    Route::get('/lunas-tagihan', [TagihanController::class, 'lunasTagihan'])->name('lunas-tagihan');
    Route::post('/bayar-tagihan/{kode}', [TagihanController::class, 'bayarTagihan'])->name('bayar-tagihan');
    Route::get('/cetak-struk/{id}', [TagihanController::class, 'cetakStruk'])->name('cetak-struk');
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
    Route::get('hapus/{id_pelanggan}', 'hapus')->name('pelanggan.hapus');
    Route::get('pelanggan/{id_pelanggan}', 'show')->name('pelanggan.show');
});


// Route::controller(DashboardController::class)->prefix('dashboard')->group(function () {
//     Route::get('', 'dashboard')->name('dashboard');
// });

Route::get('/dashboard', [DashboardController::class, 'showDashboard'])->name('dashboard');


Route::get('users', [\App\Http\Controllers\UserController::class, 'index'])->name('users.index');
Route::get('/users/create', [\App\Http\Controllers\UserController::class, 'create'])->name('users.create');
Route::post('/users', [\App\Http\Controllers\UserController::class, 'store'])->name('users.store');
Route::get('/users/{user}/edit', [\App\Http\Controllers\UserController::class, 'edit'])->name('users.edit');
Route::put('/users/{id}', [\App\Http\Controllers\UserController::class, 'update'])->name('users.update');
Route::delete('/users/{user}', [\App\Http\Controllers\UserController::class, 'destroy'])->name('users.destroy');
});

Route::get('/pelanggan-login', [PelangganAuthController::class, 'showLoginForm'])->name('pelanggan.login');
Route::post('/pelanggan-login', [PelangganAuthController::class, 'login']);
Route::middleware('auth:pelanggan')->group(function () {
    Route::get('/dashboard-pelanggan', [PelangganAuthController::class, 'dashboard'])->name('dashboard-pelanggan');
    Route::get('/belum-lunas', [PelangganAuthController::class, 'belumLunas'])->name('tagihan.belum_lunas');
    Route::get('/sudah-lunas', [PelangganAuthController::class, 'sudahLunas'])->name('tagihan.sudah_lunas');
    Route::get('/riwayat-pembayaran', [PelangganAuthController::class, 'riwayatPembayaran'])->name('tagihan.riwayat_pembayaran');
    Route::get('/profile', [PelangganAuthController::class, 'profile'])->name('profile');
    Route::get('/profile/edit', [PelangganAuthController::class, 'editProfile'])->name('edit_profile');
    Route::post('/profile/update', [PelangganAuthController::class,'updateProfile'])->name('update_profile');
    Route::get('/profile/show', [PelangganAuthController::class, 'showProfile'])->name('show_profile');
    Route::post('/profile/upload-picture', [PelangganAuthController::class, 'uploadProfilePicture'])->name('profile.picture.upload');
    Route::get('/tagihan/invoice-pembayaran/{id}', [PelangganAuthController::class, 'invoicePembayaran'])->name('tagihan.invoice_pembayaran');
    Route::get('/payment', [PelangganAuthController::class, 'showPaymentPage'])->name('payment');
});

Route::post('/logout-pelanggan', [PelangganAuthController::class, 'logout'])->name('pelanggan.logout');
Route::post('/cetak-struk/{id}', [TagihanController::class, 'cetakStruk'])->name('cetak.struk');
Route::get('/pelanggan-lunas', [TagihanController::class, 'lunas'])->name('pelanggan.lunas');
Route::get('/pelanggan-belum-lunas', [TagihanController::class, 'belumLunas'])->name('pelanggan.belumLunas');










