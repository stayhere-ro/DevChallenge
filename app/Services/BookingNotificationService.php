<?php

namespace App\Services;

use App\Mail\ClientBookingConfirmation;
use App\Mail\HairdresserNewBookingNotification;
use App\Models\Booking;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class BookingNotificationService
{
    public function sendForBooking(Booking $booking): void
    {
        try {
            $booking->load('hairdresser.user');

            Mail::to($booking->email)->send(new ClientBookingConfirmation($booking));

            Mail::to($booking->hairdresser->user->email)
                ->send(new HairdresserNewBookingNotification($booking));
        } catch (Throwable $exception) {
            Log::warning('Booking notification email failed.', [
                'booking_id' => $booking->id,
                'error' => $exception->getMessage(),
            ]);
        }
    }
}
