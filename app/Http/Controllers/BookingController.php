<?php

namespace App\Http\Controllers;

use App\Services\BookingNotificationService;
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
    public function store(BookingRequest $request, BookingNotificationService $notifier)
    {
        $data = $request->validated();

        $scheduledAt = Carbon::parse($data['date'] . ' ' . $data['hour'] . ':00');
        $hairdresserId = User::where('email', 'hairdresser@example.com')->firstOrFail()->id;

        $booking = Booking::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'scheduled_at' => $scheduledAt,
            'hairdresser_id' => $hairdresserId,
        ]);

        $notifier->sendForNewBooking($booking);

        return redirect()->route('bookings.index')
            ->with('success', 'Booking confirmed! We look forward to seeing you.');
    }
}
