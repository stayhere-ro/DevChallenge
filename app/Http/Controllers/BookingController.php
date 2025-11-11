<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Http\Requests\BookingRequest;

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
    public function store(BookingRequest $request)
    {
        Booking::create($request->validated());

        return redirect()->route('bookings.index')
            ->with('success', 'Booking confirmed! We look forward to seeing you.');
    }
}
