<?php

namespace App\Services;

use App\Mail\BookingConfirmationMail;
use App\Mail\NewBookingMail;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class BookingNotificationService
{
    public function sendForNewBooking(Booking $booking): void
    {
        $hairdresser = User::find($booking->hairdresser_id);

        if ($hairdresser?->email) {
            Mail::to($hairdresser->email)->send(new NewBookingMail($booking, $hairdresser));
        }

        if ($booking->email) {
            Mail::to($booking->email)->send(new BookingConfirmationMail($booking, $hairdresser));
        }
    }
}
