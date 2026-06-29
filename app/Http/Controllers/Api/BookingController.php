<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\BookingRequest;
use App\Models\Booking;
use App\Services\BookingNotificationService;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;

class BookingController extends Controller
{
     /**
     * Store a new booking from the public API.
     */
    public function store(BookingRequest $request, BookingNotificationService $notifications): JsonResponse
    {
        $data = $request->validated();

        try {
            $booking = Booking::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'hairdresser_id' => $data['hairdresser_id'],
                'scheduled_at' => $request->scheduledAt(),
            ]);

            $notifications->sendForBooking($booking);
        } catch (QueryException $exception) {
            return response()->json([
                'message' => 'The booking could not be created because this time slot may already be booked for the selected hairdresser.',
                'errors' => [
                    'hour' => [
                        'Please choose another available time slot.',
                    ],
                ],
            ], 409);
        }

        return response()->json([
            'message' => 'Booking created successfully.',
            'data' => [
                'id' => $booking->id,
                'name' => $booking->name,
                'email' => $booking->email,
                'hairdresser_id' => $booking->hairdresser_id,
                'scheduled_at' => $booking->scheduled_at->format('Y-m-d H:i:s'),
            ],
        ], 201);
    }
}
