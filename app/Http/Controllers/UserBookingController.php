<?php

namespace App\Http\Controllers;

use App\Mail\BookingConfirmedClient;
use App\Mail\NewBookingHairdresser;
use App\Models\Booking;
use App\Http\Requests\BookingRequest;
use App\Models\Hairdresser;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class UserBookingController extends Controller
{
    public function index()
    {
        $bookings = auth()->user()->bookings()->with('hairdresser')->get();
        return view('user.index', compact('bookings'));
    }

    public function create(){

        $hairdressers = Hairdresser::all();
        return view('user.create', compact('hairdressers'));
    }

    public function store(BookingRequest $request){
        $data = $request->validated();

        $scheduledAt = Carbon::parse($data['date'] . ' ' . $data['hour'] . ':00');

        $booking = Booking::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'user_id' => auth()->user()->id,
            'hairdresser_id' => $data['hairdresser_id'],
            'scheduled_at' => $scheduledAt,
        ]);

        //Mail::to($booking->hairdresser->email)->send(new NewBookingHairdresser($booking));
        //Mail::to($booking->email)->send(new BookingConfirmedClient($booking));

        return redirect('/my-bookings')->with('success', 'Booking created successfully');
    }
}
