<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\User;
use App\Http\Requests\BookingRequest;
use Carbon\Carbon;

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
        $data = $request->validated();

        $scheduledAt = Carbon::parse($data['date'] . ' ' . $data['hour'] . ':00');
        $hairdresserId = User::where('email', 'hairdresser@example.com')->value('id');

        Booking::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'scheduled_at' => $scheduledAt,
            'hairdresser_id' => $hairdresserId,
        ]);

        return redirect()->route('bookings.index')
            ->with('success', 'Booking confirmed! We look forward to seeing you.');
    }
}
