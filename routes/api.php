<?php

use App\Http\Controllers\Api\AvailabilityController;
use App\Http\Controllers\Api\BookingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/availability', AvailabilityController::class)
    ->middleware('throttle:api-availability');

Route::post('/bookings', [BookingController::class, 'store'])
    ->middleware(['throttle:api-bookings', 'idempotency']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/bookings', [BookingController::class, 'index']);
});
