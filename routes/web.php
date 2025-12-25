<?php


use App\Http\Controllers\admin\AdminDashboardController;

use App\Http\Controllers\admin\OwnerBankController;
use App\Http\Controllers\admin\OwnerController;
use App\Http\Controllers\admin\OwnerFlatController;
use App\Http\Controllers\admin\SiteSettingController;
use App\Http\Controllers\admin\SliderController;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
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

Route::get('/', function () {
    return view('auth.login');
});


Route::middleware('auth')->group(callback: function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/unauthorized-action', [AdminDashboardController::class, 'unauthorized'])->name('unauthorized.action');

    // Slider Section
    Route::get('/slider-section', [SliderController::class, 'index'])->name('slider.section');
    Route::post('/slider-store', [SliderController::class, 'store'])->name('slider.store');
    Route::put('/slider-update/{id}', [SliderController::class, 'update'])->name('slider.update');
    Route::get('/slider-delete/{id}', [SliderController::class, 'destroy'])->name('slider.destroy');


    // Owner Section
    Route::get('/owner-section', [OwnerController::class, 'index'])->name('owner.section');
    Route::post('/owner-store', [OwnerController::class, 'store'])->name('owner.store');
    Route::put('/owner-update/{id}', [OwnerController::class, 'update'])->name('owner.update');
    Route::get('/owner-delete/{id}', [OwnerController::class, 'destroy'])->name('owner.destroy');


    // Owner Bank Section
    Route::get('/owner-bank-section', [OwnerBankController::class, 'index'])->name('owner.bank.section');
    Route::post('/owner-bank-store', [OwnerBankController::class, 'store'])->name('owner.bank.store');
    Route::put('/owner-bank-update/{id}', [OwnerBankController::class, 'update'])->name('owner.bank.update');
    Route::get('/owner-bank-delete/{id}', [OwnerBankController::class, 'destroy'])->name('owner.bank.destroy');

    // Owner Flat Section
    Route::get('/owner-flat-section', [OwnerFlatController::class, 'index'])->name('owner.flat.section');
    Route::post('/owner-flat-store', [OwnerFlatController::class, 'store'])->name('owner.flat.store');
    Route::put('/owner-flat-update/{id}', [OwnerFlatController::class, 'update'])->name('owner.flat.update');
    Route::get('/owner-flat-delete/{id}', [OwnerFlatController::class, 'destroy'])->name('owner.flat.destroy');

    // Role and User Section
    Route::resource('roles', RoleController::class);
    Route::resource('users', UserController::class);

    // Site Setting
    Route::get('/site-setting', [SiteSettingController::class, 'index'])->name('site.setting');
    Route::post('/site-settings-store-update/{id?}', [SiteSettingController::class, 'createOrUpdate'])->name('site-settings.createOrUpdate');



});

require __DIR__.'/auth.php';
