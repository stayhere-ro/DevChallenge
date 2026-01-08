<?php

use App\Http\Controllers\Api\V1\ApiBookingController;
use App\Http\Controllers\Api\V1\BookingControllerWithDTO;
use App\Http\Controllers\Api\V1\HairdresserControllerWithDTO;
use App\Http\Controllers\Api\V1\UserControllerWithDTO;
use App\Http\Controllers\BookingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route ::apiResource('bookings', ApiBookingController::class);

Route::prefix('v1')->group(function(){
    Route::post('/bookings',[BookingControllerWithDTO::class,'store']);
    Route::post('/users',[UserControllerWithDTO::class,'store']);
    Route::post('/hairdressers',[HairdresserControllerWithDTO::class,'store']);

});



