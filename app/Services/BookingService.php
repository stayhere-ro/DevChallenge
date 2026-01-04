<?php

namespace App\Services;

use App\Models\Booking;
use App\DTOs\BookingData;
use App\Mail\NewBookingHairdresser;
use App\Mail\BookingConfirmedClient;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class BookingService
{
    public function createBooking(BookingData $data){
        $booking = Booking::create([
            'hairdresser_id' => $data->hairdresser_id,
            'user_id'        => $data->user_id,
            'name'           => $data->name,
            'email'          => $data->email,
            'scheduled_at'   => $data->scheduled_at,
        ]);

        Mail::to($booking->hairdresser->email)->send(new NewBookingHairdresser($booking));
        Mail::to($booking->email)->send(new BookingConfirmedClient($booking));

        return $booking;
    }

    public function getBookingByEmail(string $email){
        $user = User::where('email', $email)->first();

        if(!$user){
            return null;
        }

        return Booking::where('user_id', $user->id)
            ->orderBy('scheduled_at')
            ->get();
    }

    public function getBookingsForUser(User $user){
        return $user->bookings()
            ->with('hairdresser')
            ->orderBy('scheduled_at')
            ->get();

    }
}
