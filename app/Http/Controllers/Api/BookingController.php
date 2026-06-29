<?php

namespace App\Http\Controllers\Api;

use App\DTOs\CreateBookingData;
use App\Exceptions\SlotAlreadyBookedException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreBookingRequest;
use App\Services\BookingService;

class BookingController extends Controller
{
    /**
     * Store a new booking through the API.
     */
    public function store(StoreBookingRequest $request, BookingService $bookingService)
    {
        try {
            $booking = $bookingService->create(
                CreateBookingData::fromApiPayload($request->validated())
            );
        } catch (SlotAlreadyBookedException $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
                'errors' => [
                    'start_time' => [
                        $exception->getMessage(),
                    ],
                ],
            ], 409);
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
