<?php

use App\Http\Controllers\Booking\BookingController;
use App\Http\Controllers\Booking\UserBookingController;
use Illuminate\Support\Facades\Auth;
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

// Public booking routes
Route::get('/', [BookingController::class, 'create'])->name('bookings.index');
Route::get('/bookings', [BookingController::class, 'index']);
Route::post('/bookings', [BookingController::class, 'store'])
    ->middleware('throttle:bookings')
    ->name('bookings.store');


// Logged-in user booking routes
Route::get('/my-bookings', [UserBookingController::class, 'index'])->middleware('auth');
Route::get('/new-booking', [UserBookingController::class, 'create'])->middleware('auth');
Route::post('/new-booking', [UserBookingController::class, 'store'])->middleware('auth');

// Admin routes (protected by auth middleware)
//Route::prefix('admin')->middleware('auth')->group(function () {
//    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
//});

// Authentication routes
Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
