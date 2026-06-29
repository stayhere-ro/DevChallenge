<?php

namespace App\Console\Commands;

use App\Models\Booking;
use Illuminate\Console\Command;

class ListBookings extends Command
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
    protected $description = 'List bookings for a client email address';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $email = $this->argument('email');

        $bookings = Booking::where('email', $email)
            ->orderBy('scheduled_at')
            ->get();

        if ($bookings->isEmpty()) {
            $this->info('No bookings found for this email.');

            return self::SUCCESS;
        }

        $this->table(
            ['Name', 'Date', 'Time', 'Hairdresser ID'],
            $bookings->map(fn (Booking $booking): array => [
                $booking->name,
                $booking->scheduled_at->format('Y-m-d'),
                $booking->scheduled_at->format('H:i'),
                $booking->hairdresser_id,
            ])->all()
        );

        return self::SUCCESS;
    }
}
