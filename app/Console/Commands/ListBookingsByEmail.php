<?php

namespace App\Console\Commands;

use App\Models\Booking;
use Illuminate\Console\Command;

class ListBookingsByEmail extends Command
{
    protected $signature = 'bookings:list {email : Client email address}';

    protected $description = 'List bookings for a client email (date, time, hairdresser)';

    public function handle(): int
    {
        $email = (string) $this->argument('email');

        $bookings = Booking::query()
            ->with('hairdresser:id,name')
            ->where('email', $email)
            ->orderBy('scheduled_at')
            ->get();

        if ($bookings->isEmpty()) {
            $this->warn("No bookings found for {$email}.");

            return self::SUCCESS;
        }

        $this->table(
            ['Date', 'Time', 'Hairdresser ID', 'Hairdresser'],
            $bookings->map(fn (Booking $booking) => [
                $booking->scheduled_at->toDateString(),
                $booking->scheduled_at->format('H:i'),
                $booking->hairdresser_id,
                $booking->hairdresser?->name ?? '—',
            ])->all()
        );

        return self::SUCCESS;
    }
}
