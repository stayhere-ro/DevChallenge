<?php

namespace App\Http\Controllers\Api;

use App\Http\DTO\BookingDTO;
use App\Http\Requests\BookingApiRequest;
use App\Http\Interfaces\BookingServiceInterface;
use App\Http\Controllers\Controller;

class BookingApiController extends Controller
{
    public function store (BookingApiRequest $request, BookingServiceInterface $bookingService)
    {
        $reservation = BookingDTO::self($request->validated())->toArray();
        try {
            $bookingService->insert($reservation);
        } catch (\Exception $e) {
            dd($e->getMessage());
            return response()->json([
                'message' => 'Failed to create booking. Please try again later.',
            ], 500);
        }

        return response()->json([
            'message' => 'Booking created successfully.',
        ], 200);
    }
}
