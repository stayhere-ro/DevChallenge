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

/**
 * @OA\Tag(
 *     name="Bookings",
 *     description="API Endpoints for bookings"
 * )
 */
class ApiBookingController extends Controller
{
    /**
     * Display the booking form.
     *
     * @OA\Get(
     *     path="/api/bookings",
     *     summary="List bookings",
     *     tags={"Bookings"},
     *     @OA\Response(response=200, description="List of bookings")
     * )
     */
    public function index()
    {
        $bookings = Booking::with('hairdresser')->get();
        return BookingResource::collection($bookings);


    }

    /**
     * Store a new booking.
     *
     * @OA\Post(
     *     path="/api/bookings",
     *     tags={"Bookings"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(type="object",
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="email", type="string"),
     *             @OA\Property(property="date", type="string", example="2026-01-08"),
     *             @OA\Property(property="hour", type="string", example="14:00"),
     *             @OA\Property(property="hairdresser_id", type="integer")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Booking created successfully"),
     *     @OA\Response(response=422, description="Validation error")
     * )
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

    /**
     * Get bookings for a user by id.
     *
     * @OA\Get(
     *     path="/api/bookings/{id}",
     *     tags={"Bookings"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="User bookings"),
     *     @OA\Response(response=404, description="User not found")
     * )
     */
    public function show($id)
    {
        try{
             return response()->json(User::findOrFail($id)->bookings,200);

        }catch(ModelNotFoundException $e){
            return response()->json(['message' => 'User not found!'],404);
        }

    }
}
