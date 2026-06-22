<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class BookingSeeder extends Seeder
{
    public function run(): void
    {
        $hairdressers = User::query()->get();

        // Create a small pool of recurring clients
        $clientEmails = collect(range(1, 20))
            ->map(fn () => fake()->unique()->safeEmail())
            ->values();

        foreach ($hairdressers as $hairdresser) {
            $used = [];

            $count = fake()->numberBetween(10, 25);

            for ($i = 0; $i < $count; $i++) {
                $scheduledAt = $this->randomValidSlot();

                // ensure no overlap for THIS hairdresser
                while (isset($used[$scheduledAt->format('Y-m-d H:i')])) {
                    $scheduledAt = $this->randomValidSlot();
                }
                $used[$scheduledAt->format('Y-m-d H:i')] = true;

                Booking::factory()->create([
                    'hairdresser_id' => $hairdresser->id,
                    'email' => $clientEmails->random(),
                    'scheduled_at' => $scheduledAt,
                ]);
            }
        }

        $this->command->info('Bookings seeded.');
    }

    private function randomValidSlot(): Carbon
    {
        $date = Carbon::now(config('app.timezone'))
            ->addDays(fake()->numberBetween(1, 30))
            ->setTime(fake()->numberBetween(8, 16), 0, 0);

        while ($date->isWeekend()) {
            $date->addDay();
        }

        return $date;
    }
}
