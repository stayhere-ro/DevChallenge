<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreBookingRequest;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Database\QueryException;

class BookingController extends Controller
{
    /**
     * Store a new booking through the API.
     */
    public function store(StoreBookingRequest $request)
    {
        $data = $request->validated();

        $scheduledAt = Carbon::parse(
            $data['date'] . ' ' . $data['start_time'] . ':00'
        );

        try {
            $booking = Booking::create([
                'name' => $data['client_email'],
                'email' => $data['client_email'],
                'hairdresser_id' => $data['hairdresser_id'],
                'scheduled_at' => $scheduledAt,
            ]);
        } catch (QueryException $exception) {
            if ($exception->getCode() === '23000') {
                return response()->json([
                    'success' => false,
                    'message' => 'The selected time slot is already booked.',
                    'errors' => [
                        'start_time' => [
                            'The selected time slot is already booked.',
                        ],
                    ],
                ], 409);
            }

            throw $exception;
        }

        return response()->json([
            'success' => true,
            'message' => 'Booking created successfully.',
            'data' => [
                'id' => $booking->id,
                'client_email' => $booking->email,
                'hairdresser_id' => $booking->hairdresser_id,
                'date' => $booking->scheduled_at->toDateString(),
                'start_time' => $booking->scheduled_at->format('H:i'),
            ],
        ], 201);
    }
}
