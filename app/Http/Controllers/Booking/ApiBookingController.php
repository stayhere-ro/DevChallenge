<?php

namespace App\Http\Controllers\Booking;

use App\DTOs\BookingData;
use App\Http\Controllers\Controller;
use App\Http\Requests\BookingRequest;
use App\Models\Booking;
use App\Models\User;
use App\Services\BookingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiBookingController extends Controller
{
    public function __construct(
        protected BookingService $bookingService,
    )
    {}

    public function index(Request $request): JsonResponse
    {
        $email = $request->query('email');
        if (!$email) {
            return response()->json(['error' => 'Email parameter is required'], 400);
        }

        $bookings = $this->bookingService->getBookingByEmail($email);

        if ($bookings === null) {
            return response()->json(['error' => 'User not found'], 404);
        }

        return response()->json($bookings);
    }

    public function store(BookingRequest $request): JsonResponse
    {
        $data = BookingData::fromRequest($request->validated());

        $booking = $this->bookingService->createBooking($data);

        return response()->json([
            'message' => 'Booking confirmed! We look forward to seeing you.',
            'booking' => $booking
        ], 201);
    }
}
