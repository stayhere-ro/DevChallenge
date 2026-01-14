<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Hairdresser;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BookingHairdresserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $bookings = Booking::all();
        $hairdressers = Hairdresser::all();
        $bookings->each(function ($booking) use ($hairdressers) {
            $booking->hairdresser()->attach(
                $hairdressers->random()->id,
                [ 'created_at' => now(),
                  'updated_at' => now()]
            );
        });


    }
}
