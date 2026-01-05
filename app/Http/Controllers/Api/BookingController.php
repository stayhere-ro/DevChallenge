<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreBookingRequest;
use App\Models\Booking;
use App\Services\BookingNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\DTO\CreateBookingData;
use App\Exceptions\BookingValidationException;
use App\Services\CreateBookingService;

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
                'success' => false,
                'message' => 'Validation failed.',
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
    public function store(
        StoreBookingRequest $request,
        CreateBookingService $service,
        BookingNotificationService $notifier
    ) {
        $timezone = config('app.timezone');
        $dto = CreateBookingData::fromApi($request->validated(), $timezone);

        try {
            $booking = $service->create($dto, $timezone);
        } catch (BookingValidationException $error) {
            return response()->json([
                'success' => false,
                'message' => $error->getMessage(),
                'errors' => $error->errors,
            ], 422);
        }

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
