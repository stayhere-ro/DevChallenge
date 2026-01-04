<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreBookingRequest;
use App\Models\Booking;
use App\Services\BookingNotificationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BookingController extends Controller
{
    /**
     * List all bookings for a given email.
     */
    public function index(Request $request)
    {
        $validator = Validator::make($request->query(), [
            'email' => ['required', 'email'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $email = $validator->validated()['email'];

        $bookings = Booking::query()
            ->where('email', $email)
            ->orderBy('scheduled_at')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $bookings->map(fn ($booking) => [
                'id' => $booking->id,
                'hairdresser_id' => $booking->hairdresser_id,
                'date' => $booking->scheduled_at->toDateString(),
                'time' => $booking->scheduled_at->format('H:i'),
            ]),
        ]);
    }

    /**
     * Store a new booking.
     */
    public function store(StoreBookingRequest $request, BookingNotificationService $notifier)
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

        $notifier->sendForNewBooking($booking);

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
