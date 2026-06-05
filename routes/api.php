<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TagihanController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });


Route::controller(TagihanController::class)->group(function () {
    Route::get('tagihan', 'indexApi')->name('api.tagihan.index');
    Route::post('tagihan', 'storeTagihanApi')->name('api.tagihan.store');
    Route::get('buka-tagihan', 'bukaTagihanApi')->name('api.buka.tagihan');
    Route::post('data-tagihan', 'dataTagihanApi')->name('api.data.tagihan');
    Route::post('bayar-tagihan/{kode}', 'bayarTagihanApi')->name('api.bayar.tagihan');
    Route::get('lunas-tagihan', 'lunasTagihanApi')->name('api.lunas.tagihan');
    Route::get('belum-lunas', 'belumLunasApi')->name('api.belum.lunas');
    Route::delete('delete-tagihan/{id}', 'deleteTagihanApi')->name('api.delete.tagihan');
});

Route::controller(AuthController::class)->group(function () {
    Route::post('register', 'registerApi')->name('api.register');
    Route::post('login', 'loginApi')->name('api.login');
    Route::post('logout', 'logoutApi')->name('api.logout')->middleware('auth:sanctum');
});

Route::controller(DashboardController::class)->group(function () {
    Route::get('dashboard', 'showDashboardApi')->name('api.dashboard');
});

// ============================================
// RESTFUL API ROUTES (ADMIN & PUBLIC)
// ============================================
use App\Http\Controllers\API\PelangganApiController;
use App\Http\Controllers\API\PaketApiController;
use App\Http\Controllers\API\BankApiController;

// Public endpoints
Route::get('paket', [PaketApiController::class, 'index']);
Route::get('paket/{id}', [PaketApiController::class, 'show']);
Route::get('bank', [BankApiController::class, 'index']);

// Protected endpoints (require authentication)
Route::middleware('auth:sanctum')->group(function () {
    // Pelanggan Management
    Route::apiResource('pelanggan', PelangganApiController::class);
    Route::get('pelanggan/{id}/tagihan', [PelangganApiController::class, 'tagihan']);

    // Paket Management
    Route::post('paket', [PaketApiController::class, 'store']);
    Route::put('paket/{id}', [PaketApiController::class, 'update']);
    Route::delete('paket/{id}', [PaketApiController::class, 'destroy']);

    // Bank Management
    Route::post('bank', [BankApiController::class, 'store']);
    Route::put('bank/{id}', [BankApiController::class, 'update']);
    Route::delete('bank/{id}', [BankApiController::class, 'destroy']);
});

// ============================================
// MOBILE APP API ROUTES
// ============================================
use App\Http\Controllers\API\MobileAuthController;
use App\Http\Controllers\API\MobileDashboardController;
use App\Http\Controllers\API\MobileTagihanController;
use App\Http\Controllers\API\MobileRiwayatController;
use App\Http\Controllers\API\MobileWiFiController;
use App\Http\Controllers\API\MobileProfileController;
use App\Http\Controllers\API\MobileFcmController;
use App\Http\Controllers\API\MobileNotificationController;

Route::prefix('mobile')->group(function () {
    // Public routes
    Route::post('login', [MobileAuthController::class, 'login']);
    Route::get('profile/photo/{filename}', [MobileProfileController::class, 'getPhoto'])->name('api.mobile.profile.photo');

    // Protected routes (auth:sanctum)
    Route::middleware('auth:sanctum')->group(function () {
        // Auth
        Route::get('me', [MobileAuthController::class, 'me']);
        Route::post('logout', [MobileAuthController::class, 'logout']);
        Route::put('update-email', [MobileAuthController::class, 'updateEmail']);
        Route::put('update-password', [MobileAuthController::class, 'updatePassword']);

        // FCM Token Management
        Route::post('fcm/register', [MobileFcmController::class, 'register']);
        Route::post('fcm/unregister', [MobileFcmController::class, 'unregister']);

        // Dashboard
        Route::get('dashboard', [MobileDashboardController::class, 'index']);

        // Tagihan
        Route::get('tagihan', [MobileTagihanController::class, 'index']);
        Route::get('tagihan/{id}', [MobileTagihanController::class, 'show']);
        Route::get('payment-methods', [MobileTagihanController::class, 'paymentMethods']);

        // Riwayat
        Route::get('riwayat', [MobileRiwayatController::class, 'index']);

        // Profile Management
        Route::put('profile', [MobileProfileController::class, 'update']);
        Route::post('profile/photo', [MobileProfileController::class, 'uploadPhoto']);
        Route::put('profile/password', [MobileProfileController::class, 'changePassword']);

        // WiFi Settings
        Route::prefix('wifi')->group(function () {
            Route::get('/', [MobileWiFiController::class, 'index']);
            Route::get('connected-devices', [MobileWiFiController::class, 'connectedDevices']);
            Route::get('debug-genieacs', [MobileWiFiController::class, 'debugGenieACS']); // Debug endpoint
            Route::post('change-ssid', [MobileWiFiController::class, 'changeSSID']);
            Route::post('change-password', [MobileWiFiController::class, 'changePassword']);
            Route::get('history', [MobileWiFiController::class, 'history']);
            Route::delete('history/{id}', [MobileWiFiController::class, 'deleteHistory']);
        });

        // Notifications
        Route::prefix('notifications')->group(function () {
            Route::get('/', [MobileNotificationController::class, 'index']);
            Route::get('unread-count', [MobileNotificationController::class, 'unreadCount']);
            Route::post('{id}/read', [MobileNotificationController::class, 'markAsRead']);
            Route::post('read-all', [MobileNotificationController::class, 'markAllAsRead']);
            Route::delete('delete-all', [MobileNotificationController::class, 'deleteAll']);
        });
    });
});
