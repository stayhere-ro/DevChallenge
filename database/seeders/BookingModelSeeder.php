<?php

namespace Database\Seeders;

use App\Models\Booking;
use Illuminate\Database\Seeder;

class BookingModelSeeder extends Seeder
{
    public function run(): void
    {
        Booking::truncate();
        Booking::factory()->count(20)->create();
    }
}
