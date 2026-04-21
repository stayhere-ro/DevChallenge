<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ApiBookingRequest;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ApiBookingController extends Controller
{
    /**
     * Store a new booking.
     */
    public function store(ApiBookingRequest $request)
    {
        $data = $request->validated();

        $scheduledAt = Carbon::parse($data['date'] . ' ' . $data['start_time'] . ':00');

        $booking = Booking::create([
            'name' => explode('@', $data['client_email'])[0], //name can be changed in the table, to be nullable, but i prefered for now this quick solution
            'email' => $data['client_email'],
            'hairdresser_id' => (int) $data['hairdresser_id'],
            'scheduled_at' => $scheduledAt,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Booking confirmed! We look forward to seeing you.'
        ], 201);
    }

    /**
     * Retrieve all bookings for the given email.
     */
    public function bookingsRetriever(Request $request)
    {
        $email = $request->query('email');

        $bookings = Booking::query()
            ->where('email', $email)
            ->orderBy('scheduled_at')
            ->get();

        $formatedBookings = $bookings->map(fn ($booking) => [
                'id' => $booking->id,
                'name' => $booking->name,
                'hairdresser_id' => $booking->hairdresser_id,
                'date' => $booking->scheduled_at->toDateString(), //date
                'scheduled' => $booking->scheduled_at->format('H:i'), //hour
            ]);

        return response()->json([
            'success' => true,
            'data' => $formatedBookings
        ]);
    }
}
