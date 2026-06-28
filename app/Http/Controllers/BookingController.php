<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookingRequest;
use App\Http\DTO\BookingDTO;
use App\Http\Interfaces\BookingServiceInterface;
use App\Helpers\HttpResponse;

class BookingController extends Controller
{
    /**
     * Display the booking form.
     */
    public function index()
    {
        return view('bookings.index');
    }

    /**
     * Store a new booking.
     */
    public function store(BookingRequest $request, BookingServiceInterface $bookingService)
    {
        $reservation = BookingDTO::self($request->validated())->toArray();
        try {
            $bookingService->insert($reservation);

            return redirect()->route('bookings.index')
            ->with('success', 'Booking confirmed! We look forward to seeing you.');
        } catch (\Exception $e) {
            return HttpResponse::simpleResponse(500, 'Failed to create booking. Please try again later.');
        }
    }
}
