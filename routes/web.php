<?php

use App\Http\Controllers\BusinessSettingController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\RegionController;
use App\Http\Controllers\SymbolController;
use App\Http\Controllers\TitleManagement;
use App\Http\Controllers\TreeController;
use App\Http\Controllers\TreeInspectionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect()->route('login');
});

Auth::routes(['register' => false]);

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
// Route::get('/marketstacks', [App\Http\Controllers\MarketStackController::class, 'index'])->name('marketstack');

// Profile Routes
Route::prefix('profiles')->name('profile.')->middleware('auth')->group(function(){
    Route::get('/', [HomeController::class, 'getProfile'])->name('detail');
    Route::post('/update', [HomeController::class, 'updateProfile'])->name('update');
    Route::post('/change-password', [HomeController::class, 'changePassword'])->name('change-password');
});


Route::resource('roles', App\Http\Controllers\RolesController::class);

Route::resource('permissions', App\Http\Controllers\PermissionsController::class);


Route::middleware('auth')->prefix('admins')->name('users.')->group(function(){
    Route::get('/', [UserController::class, 'index'])->name('index');
    Route::get('/create', [UserController::class, 'create'])->name('create');
    Route::post('/store', [UserController::class, 'store'])->name('store');
    Route::get('/edit/{user}', [UserController::class, 'edit'])->name('edit');
    Route::put('/update/{user}', [UserController::class, 'update'])->name('update');
    Route::delete('/delete/{user}', [UserController::class, 'delete'])->name('destroy');
    Route::get('/update/status/{user_id}/{status}', [UserController::class, 'updateStatus'])->name('status');
    Route::get('/import-admin', [UserController::class, 'importUsers'])->name('import');
    Route::post('/upload-admin', [UserController::class, 'uploadUsers'])->name('upload');
    Route::get('export/', [UserController::class, 'export'])->name('export');

});

Route::middleware('auth')->prefix('customers')->name('customer.')->group(function(){
    Route::get('/list', [CustomerController::class, 'index'])->name('index');
    Route::delete('/delete', [CustomerController::class, 'delete'])->name('destroy');
    Route::get('/update/status/{customer_id}/{status}', [CustomerController::class, 'updateStatus'])->name('status');
});

Route::middleware('auth')->prefix('symbols')->name('symbol.')->group(function(){
    Route::get('/list', [SymbolController::class, 'index'])->name('index');
    Route::get('/create', [SymbolController::class, 'create'])->name('create');
    Route::post('/store', [SymbolController::class, 'store'])->name('store');
    Route::delete('/destroy', [SymbolController::class, 'destroy'])->name('destroy');
    Route::get('/api/data', [SymbolController::class, 'search_symbol'])->name('search');
});

Route::middleware('auth')->prefix('business_settings')->name('settings.')->group(function(){
    Route::get('/terms_condtions', [BusinessSettingController::class, 'terms_contions_view'])->name('terms');
    Route::post('/terms_condtions/update', [BusinessSettingController::class, 'terms_conditions_store'])->name('terms.store');

    Route::get('/privacy_policy', [BusinessSettingController::class, 'privacy_policy_view'])->name('privacy');
    Route::post('/privacy_policy/update', [BusinessSettingController::class, 'privacy_policy_store'])->name('privacy.store');
});


Route::get('/', function(){
    return redirect()->route('login');
});

