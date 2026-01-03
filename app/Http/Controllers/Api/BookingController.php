<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreBookingRequest;
use App\Models\Booking;
use Carbon\Carbon;

class BookingController extends Controller
{
    /**
     * Store a new booking.
     */
    public function store(StoreBookingRequest $request)
    {
        $data = $request->validated();

        $scheduledAt = Carbon::createFromFormat(
            'Y-m-d H:i',
            "{$data['date']} {$data['start_time']}",
            config('app.timezone')
        );

        $booking = Booking::create([
            'name' => null,
            'email' => $data['client_email'],
            'hairdresser_id' => (int) $data['hairdresser_id'],
            'scheduled_at' => $scheduledAt,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Booking confirmed! We look forward to seeing you.',
            'data' => [
                'id' => $booking->id,
                'client_email' => $booking->email,
                'hairdresser_id' => $booking->hairdresser_id,
                'scheduled_at' => $booking->scheduled_at->toIso8601String(),
            ],
        ], 201);
    }
}
