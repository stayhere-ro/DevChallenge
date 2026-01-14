<?php

use App\Http\Controllers\Api\V1\ApiBookingController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\Admin\DashboardController;

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

// Public booking routes
Route::get('/', [BookingController::class, 'index'])->name('bookings.index');
Route::post('/bookings', [ApiBookingController::class, 'store'])
    ->middleware('throttle:bookings')
    ->name('bookings.store');
//Route::post('/bookings',[ApiBookingController::class,'store']);

// Admin routes (protected by auth middleware)
Route::prefix('admin')->middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
});

// Authentication routes
// Disable registration for security; remove duplicate Auth::routes call
Auth::routes(['register' => false]);

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

