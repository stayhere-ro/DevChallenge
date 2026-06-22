<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Console\Command;

class ListBookingsByEmailCommand extends Command
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
    protected $description = 'List all bookings for a given user email in a table format showing date, time, and hairdresser ID';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');

        $user = User::where('email', $email)->first();
        if (!$user) {
            $this->error("No user found with email: {$email}");
            return 1;
        }

        $this->info("Bookings for customer: {$user->name}");

        // fetch all bookings by user
        $bookings = Booking::where('user_id', $user->id)->orderBy('scheduled_at')->get();

        // create table
        $headers = ['Date', 'Time', 'Hairdresser ID'];
        $data = $bookings->map(function ($booking) {
            return [
                $booking->scheduled_at->format('Y-m-d'),
                $booking->scheduled_at->format('H:i'),
                $booking->hairdresser_id,
            ];
        });

        $this->table($headers, $data);

        return 0;
    }
}
