<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Hairdresser;
use App\Http\Requests\BookingRequest;
use App\Services\BookingNotificationService;

class BookingController extends Controller
{
    /**
     * Display the booking form.
     */
    public function index()
    {
        $hairdressers = Hairdresser::orderBy('name')->get();

        return view('bookings.index', compact('hairdressers'));
    }

    /**
     * Store a new booking.
     */
    public function store(BookingRequest $request, BookingNotificationService $notifications)
    {
        $data = $request->validated();

        $booking = Booking::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'hairdresser_id' => $data['hairdresser_id'],
            'scheduled_at' => $request->scheduledAt(),
        ]);

        $notifications->sendForBooking($booking);

        return redirect()->route('bookings.index')
            ->with('success', 'Booking confirmed! We look forward to seeing you.');
    }
}
