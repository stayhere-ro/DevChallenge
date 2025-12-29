<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Hairdresser;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BookingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    public function run(): void
    {
        Booking::factory(20)->create();

        $this->command->info('Bookings were created successfully!');
    }
}
