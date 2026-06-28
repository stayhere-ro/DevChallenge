<?php

namespace App\Http\Controllers\Api;

use App\Http\DTO\BookingDTO;
use App\Http\Requests\Api\StoreBookingRequest;
use App\Http\Interfaces\BookingServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\GetBookingRequest;
use App\Helpers\HttpResponse;

class BookingApiController extends Controller
{
    public function store(StoreBookingRequest $request, BookingServiceInterface $bookingService)
    {
        $reservation = BookingDTO::self($request->validated())->toArray();

        try {
            $bookingService->insert($reservation);
        } catch (\Exception $e) {
            return HttpResponse::simpleResponse(500, 'Failed to create booking. Please try again later.');
        }

        return HttpResponse::simpleResponse(200, 'Booking created successfully.');
    }

    public function getBookings(GetBookingRequest $request, BookingServiceInterface $bookingService)
    {
        try {
            $bookings = $bookingService->getListBy($request->email);
            return HttpResponse::dataResponse(200, $bookings, 'Bookings retrieved successfully.');
        }
        catch (\Exception $e) {
            return HttpResponse::simpleResponse(500, 'Failed to retrieve bookings. Please try again later.');
        }
    }
}
