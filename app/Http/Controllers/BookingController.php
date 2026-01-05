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
        if (auth()->check() && auth()->user()->isHairdresser()) {
            abort(403);
        }

        $data = $request->validated();

        $scheduledAt = Carbon::parse($data['date'] . ' ' . $data['hour'] . ':00');

        $name = $data['name'];
        $email = $data['email'];

        if (auth()->check() && auth()->user()->isClient()) {
            $name = auth()->user()->name;
            $email = auth()->user()->email;
        }

        $booking = Booking::create([
            'name' => $name,
            'email' => $email,
            'scheduled_at' => $scheduledAt,
            'hairdresser_id' => $data['hairdresser_id'],
        ]);

        $notifier->sendForNewBooking($booking);

        return redirect()->route('bookings.index')
            ->with('success', 'Booking confirmed! We look forward to seeing you.');
    }
}
