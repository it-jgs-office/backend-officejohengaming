<?php

use App\Http\Controllers\Api\AsetMesApiController;
use App\Http\Controllers\Api\AsetRukoApiController;
use App\Http\Controllers\Api\AsetSayaApiController;
use App\Http\Controllers\Api\AsetTimApiController;
use App\Http\Controllers\Api\AssetApiController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\DigitalAssetApiController;
use App\Http\Controllers\Api\MeetingApiController;
use App\Http\Controllers\Api\MeetingRequestIntegrationController;
use App\Http\Controllers\Api\PaymentApprovalApiController;
use App\Http\Controllers\Api\PembayaranApiController;
use App\Http\Controllers\Api\PeralatanKantorApiController;
use App\Http\Controllers\Api\RoomController;
use App\Http\Controllers\Api\SimCardApiController;
use App\Http\Controllers\Api\SosialMediaApiController;
use App\Http\Controllers\Api\UserApiController;
use App\Http\Controllers\Api\VehicleApiController;
use App\Http\Controllers\Api\WeeklyMeetingApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('api.auth');

Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
});

Route::middleware('api.auth')->group(function () {

    Route::get('rooms', [RoomController::class, 'index']);
    Route::middleware('admin')->group(function () {
        Route::post('rooms', [RoomController::class, 'store']);
        Route::put('rooms/{room}', [RoomController::class, 'update']);
        Route::delete('rooms/{room}', [RoomController::class, 'destroy']);
    });

    Route::get('bookings', [BookingController::class, 'index']);
    Route::post('bookings', [BookingController::class, 'store']);
    Route::delete('bookings/{booking}', [BookingController::class, 'destroy']);

    Route::get('meetings', [MeetingApiController::class, 'index']);
    Route::get('meetings/{meeting}', [MeetingApiController::class, 'show']);
    Route::get('weekly-meetings', [WeeklyMeetingApiController::class, 'index']);
    // Trusted integration endpoints for the meeting request interface.
    Route::prefix('integration')->group(function () {
        Route::get('meeting-requests', [MeetingRequestIntegrationController::class, 'index']);
        Route::post('meeting-requests', [MeetingRequestIntegrationController::class, 'store']);
        Route::put('meeting-requests/{meeting}/approve', [MeetingRequestIntegrationController::class, 'approve']);
        Route::put('meeting-requests/{meeting}/reject', [MeetingRequestIntegrationController::class, 'reject']);
    });


    Route::apiResource('assets', AssetApiController::class)->parameters(['assets' => 'asset']);
    Route::apiResource('aset-ruko', AsetRukoApiController::class)->parameters(['aset_ruko' => 'ruko']);
    Route::apiResource('aset-tim', AsetTimApiController::class)->parameters(['aset_tim' => 'asetTim']);
    Route::apiResource('digital-assets', DigitalAssetApiController::class)->parameters(['digital_assets' => 'digitalAsset']);
    Route::get('aset-saya', [AsetSayaApiController::class, 'index']);
    Route::get('aset-mes', [AsetMesApiController::class, 'index']);
    Route::get('users', [UserApiController::class, 'index']);
    Route::get('vehicles', [VehicleApiController::class, 'index']);
    Route::get('sim-cards', [SimCardApiController::class, 'index']);
    Route::get('peralatan-kantor', [PeralatanKantorApiController::class, 'index']);
    Route::get('sosial-media', [SosialMediaApiController::class, 'index']);

    Route::prefix('pembayaran')->group(function () {
        Route::get('/', [PembayaranApiController::class, 'index']);
        Route::post('/', [PembayaranApiController::class, 'store']);
        Route::put('{id}', [PembayaranApiController::class, 'update']);
        Route::delete('{id}', [PembayaranApiController::class, 'destroy']);
        Route::post('ipl-bulk', [PembayaranApiController::class, 'bulkIpl']);
        Route::post('ipl-ruko-bulk', [PembayaranApiController::class, 'storeBulkIplRuko']);
        Route::post('token-reading', [PembayaranApiController::class, 'storeTokenReading']);
        Route::get('token-readings', [PembayaranApiController::class, 'indexTokenReadings']);
        Route::delete('token-reading/{id}', [PembayaranApiController::class, 'destroyTokenReading']);
        Route::post('token-topup', [PembayaranApiController::class, 'storeTokenPayment']);
        Route::get('token-topups', [PembayaranApiController::class, 'indexTokenTopups']);
        Route::delete('token-topup/{id}', [PembayaranApiController::class, 'destroyTokenPayment']);
        Route::post('internet-usage', [PembayaranApiController::class, 'storeInternetUsage']);
        Route::delete('internet-usage/{id}', [PembayaranApiController::class, 'destroyInternetUsage']);
        Route::get('internet-payments', [PembayaranApiController::class, 'indexInternetPayments']);
        Route::get('internet-checks', [PembayaranApiController::class, 'indexInternetChecks']);
        Route::get('digital-asset-payments', [PembayaranApiController::class, 'indexDigitalAssetPayments']);
        Route::get('ipl-ruko-payments', [PembayaranApiController::class, 'indexIplRukoPayments']);
        Route::get('tagihan', [PembayaranApiController::class, 'indexTagihan']);
        Route::get('pengajuan', [PembayaranApiController::class, 'indexPengajuan']);
    });

    Route::prefix('payment-approvals')->group(function () {
        Route::get('/', [PaymentApprovalApiController::class, 'index']);
        Route::post('{id}/approve', [PaymentApprovalApiController::class, 'approve']);
        Route::post('{id}/reject', [PaymentApprovalApiController::class, 'reject']);
    });
});

