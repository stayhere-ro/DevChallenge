<?php

namespace App\Listeners;

use App\Events\BookingCreated;
use App\Mail\BookingConfirmedMail;
use App\Mail\HairdresserBookingNotificationMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class SendBookingNotifications implements ShouldQueue
{
    public int $tries = 3;

    public function handle(BookingCreated $event): void
    {
        $booking = $event->booking->loadMissing('hairdresser');

        Mail::to($booking->email)->send(new BookingConfirmedMail($booking));

        if ($booking->hairdresser?->email) {
            Mail::to($booking->hairdresser->email)->send(new HairdresserBookingNotificationMail($booking));
        }
    }
}
