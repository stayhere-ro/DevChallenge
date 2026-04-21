<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;

class BookingsList extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookings:list {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List bookings for a given email';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');

        $bookings = Booking::where('email', $email)
            ->get(['scheduled_at', 'hairdresser_id']);

        if ($bookings->isEmpty()) {
            $this->info("No bookings found for {$email}");
            return;
        }

        //format the table elements
        $tableElements =  $bookings->map(function ($booking) {
                return [
                    $booking->scheduled_at->toDateString(), //date
                    $booking->scheduled_at->format('H:i'), //hour
                    $booking->hairdresser_id,
                ];
            });

        $this->table(
            ['Date', 'Time', 'Hairdresser ID'],
            $tableElements
        );
    }
}
