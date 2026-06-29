<?php

namespace App\Http\Controllers;

use App\Exceptions\BookingSlotNotAvailableException;
use App\Http\Requests\BookingRequest;
use App\Models\Dtos\CreateBookingDto;
use App\Models\Hairdresser;
use App\Services\BookingNotificationService;
use App\Services\BookingService;

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
    public function store(
        BookingRequest $request,
        BookingService $bookingService,
        BookingNotificationService $notifications
    ) {
        $data = $request->validated();
        $bookingData = CreateBookingDto::fromArray($data, $request->scheduledAt());

        try {
            $booking = $bookingService->create($bookingData);
        } catch (BookingSlotNotAvailableException $exception) {
            return back()
                ->withInput()
                ->withErrors([
                    'hour' => 'This time slot is already booked. Please choose another time.',
                ]);
        }

        $notifications->sendForBooking($booking);

        return redirect()->route('bookings.index')
            ->with('success', 'Booking confirmed! We look forward to seeing you.');
    }
}
