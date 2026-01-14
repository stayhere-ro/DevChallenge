<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Http\Requests\BookingRequest;
use App\Models\Hairdresser;
use Carbon\Carbon;

class BookingController extends Controller
{
    /**
     * Display the booking form.
     */
    public function index()
    {
        $hairdressers = Hairdresser::all();
        return view('bookings.index', compact('hairdressers'));

    }

    /**
     * Store a new booking.
     */
 /*   public function store(BookingRequest $request)
    {
        $data = $request->validated();

        $scheduledAt = Carbon::parse($data['date'] . ' ' . $data['hour'] . ':00');

        Booking::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'scheduled_at' => $scheduledAt,
        ]);

        return redirect()->route('bookings.index')
            ->with('success', 'Booking confirmed! We look forward to seeing you.');
    }*/
}
