<?php

use App\Http\Controllers\Api\CustomerAuthController as ApiCustomerAuthController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\CustomerDataController;
use App\Http\Controllers\Api\SettingsController;
use App\Http\Controllers\Api\Stocks\StocksController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('customers')->group(function () {
    Route::post('login', [ApiCustomerAuthController::class, 'login']);
    Route::post('register', [ApiCustomerAuthController::class, 'register']);
    Route::post('reset-password/send-otp', [ApiCustomerAuthController::class, 'send_forgot_email_otp']);
    Route::post('reset-password/verify', [ApiCustomerAuthController::class, 'verifyAndResetPassword']);
});


Route::prefix('settings')->group(function () {
    Route::get('/', [SettingsController::class, 'settings']);
});



Route::middleware('auth.customer:api')->group(function () {
    Route::prefix('customers')->group(function () {
        Route::get('profile', [CustomerController::class, 'profile']);
        Route::post('update/profile', [CustomerController::class, 'edit']);
        Route::post('update/password', [CustomerController::class, 'changePassword']);
        Route::get('logout', [CustomerController::class, 'logout']);

        Route::post('set/data', [CustomerDataController::class, 'set_data']);
        Route::get('get/data', [CustomerDataController::class, 'get_data']);
    });
});

Route::get('stocks/data', [StocksController::class, 'stocks_data']);
Route::get('stocks/chart/{symbol}/{interval}', [StocksController::class, 'stocks_chart']);

Route::get('/stocks/assets/{filename}', function ($filename) {
    $path = public_path('stocks/assets/' . $filename);

    if (!File::exists($path)) {
        abort(404); // File not found
    }

    return Response::file($path);
});
