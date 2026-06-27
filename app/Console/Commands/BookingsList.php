<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;

class BookingsList extends Command
{
    protected $signature = 'bookings:list {email}';

    protected $description = 'List bookings for a given email';

    public function handle()
    {
        $email = $this->argument('email');

        $bookings = Booking::where('email', $email)
            ->select('scheduled_at', 'hairdresser_id')
            ->orderBy('scheduled_at')
            ->get();


        if ($bookings->isEmpty()) {
            $this->info("No bookings found for {$email}");
            return 0;
        }

        $this->table(
            ['Date', 'Time', 'Hairdresser ID'],
            $bookings->map(function ($booking) {
                return [
                    $booking->scheduled_at->toDateString(),
                    $booking->scheduled_at->format('H:i'),
                    $booking->hairdresser_id,
                ];
            })->toArray()
        );

        return 0;
    }
}