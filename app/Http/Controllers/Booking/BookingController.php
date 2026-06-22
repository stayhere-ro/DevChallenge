<?php

namespace App\Http\Controllers\Booking;

use App\DTOs\BookingData;
use App\Http\Controllers\Controller;
use App\Http\Requests\BookingRequest;
use App\Models\Booking;
use App\Models\Hairdresser;
use App\Models\User;
use App\Services\BookingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function __construct(
        protected BookingService $bookingService,
    )
    {}
    /**
     * Store a new booking.
     */
    public function store(BookingRequest $request)
    {
        $data = BookingData::fromRequest($request->validated());

        $this->bookingService->createBooking($data);

        return redirect()->back()->with('success', 'Booking confirmed! We look forward to seeing you.');
    }

    public function create()
    {
        $hairdressers = Hairdresser::all();

        return view('bookings.index', compact('hairdressers'));
    }
}
