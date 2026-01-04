<?php

namespace App\Http\Controllers\Booking;

use App\DTOs\BookingData;
use App\Http\Controllers\Controller;
use App\Http\Requests\BookingRequest;
use App\Models\Booking;
use App\Models\Hairdresser;
use App\Services\BookingService;
use Carbon\Carbon;

class UserBookingController extends Controller
{
    public function __construct(
        protected BookingService $bookingService,
    )
    {}

    public function index()
    {
        $bookings = $this->bookingService->getBookingsForUser(auth()->user());
        return view('user.index', compact('bookings'));
    }

    public function create(){

        $hairdressers = Hairdresser::all();
        return view('user.create', compact('hairdressers'));
    }

    public function store(BookingRequest $request){

        $data = BookingData::fromRequest($request->validated(), auth()->id());

        $this->bookingService->createBooking($data);

        return redirect('/my-bookings')->with('success', 'Booking created successfully');
    }
}
