<?php

namespace App\Console\Commands;

use App\Models\Booking;
use Illuminate\Console\Command;

class BookingsList extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookings:list {email : Client email address}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List bookings for a given client email';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $email = (string) $this->argument('email');

        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('Please provide a valid email address.');

            return self::FAILURE;
        }

        $bookings = Booking::query()
            ->where('email', $email)
            ->orderBy('scheduled_at')
            ->get(['scheduled_at', 'hairdresser_id']);

        if ($bookings->isEmpty()) {
            $this->info("No bookings found for {$email}.");

            return self::SUCCESS;
        }

        $rows = $bookings->map(function (Booking $booking) {
            return [
                'date' => $booking->scheduled_at->toDateString(),
                'time' => $booking->scheduled_at->format('H:i'),
                'hairdresser_id' => $booking->hairdresser_id,
            ];
        })->all();

        $this->table(['Date', 'Time', 'Hairdresser ID'], $rows);

        return self::SUCCESS;
    }
}
