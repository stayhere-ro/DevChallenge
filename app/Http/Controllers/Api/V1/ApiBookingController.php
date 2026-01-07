<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApiBookingRequest;
use App\Http\Resources\BookingResource;
use App\Models\Booking;
use App\Http\Requests\BookingRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Hash;

class ApiBookingController extends Controller
{
    /**
     * Display the booking form.
     */
    public function index()
    {
        $bookings = Booking::with('hairdresser')->get();
        return BookingResource::collection($bookings);


    }

    /**
     * Store a new booking.
     */
    public function store(ApiBookingRequest $request)
    {
        $data = $request->validated();
        $user = User::firstOrCreate([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make('password'),
        ]);
        $booking = Booking::create([
            'user_id' => $user->id,
            'date' => Carbon::parse($data['date'])->format('Y-m-d'),
            'time' => $data['hour'],

        ]);
        $booking->hairdresser()->attach($data['hairdresser_id']);
        return response()->json(
            ['message' => 'Booking created successfully!'],
            201);
    }

    public function show($id)
    {
        try{
             return response()->json(User::findOrFail($id)->bookings,200);

        }catch(ModelNotFoundException $e){
            return response()->json(['message' => 'User not found!'],404);
        }

    }
}
