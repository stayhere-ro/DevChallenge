<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Hairdresser;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class BookingDemoSeeder extends Seeder
{
    public function run(): void
    {
        if (Booking::query()->exists()) {
            return;
        }

        $hairdressers = Hairdresser::active()->get();

        if ($hairdressers->isEmpty()) {
            $this->call(HairdresserSeeder::class);
            $hairdressers = Hairdresser::active()->get();
        }

        foreach ($hairdressers as $hairdresser) {
            $day = now()->addWeek()->startOfWeek(Carbon::MONDAY);

            for ($hour = 8; $hour < 13; $hour++) {
                Booking::factory()->forHairdresser($hairdresser)->create([
                    'scheduled_at' => $day->copy()->setTime($hour, 0),
                ]);
            }
        }
    }
}
