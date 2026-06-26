<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Hairdresser;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Booking>
 */
class BookingFactory extends Factory
{
    protected $model = Booking::class;

    public function definition(): array
    {
        $scheduledAt = $this->nextWeekdaySlot();

        return [
            'hairdresser_id' => Hairdresser::factory(),
            'name' => fake()->name(),
            'email' => fake()->safeEmail(),
            'scheduled_at' => $scheduledAt,
        ];
    }

    public function forHairdresser(Hairdresser $hairdresser): static
    {
        return $this->state(fn () => ['hairdresser_id' => $hairdresser->id]);
    }

    public function at(Carbon $scheduledAt): static
    {
        return $this->state(fn () => ['scheduled_at' => $scheduledAt]);
    }

    private function nextWeekdaySlot(): Carbon
    {
        $date = now()->addDays(fake()->numberBetween(1, 14))->startOfDay();

        while ($date->isWeekend()) {
            $date->addDay();
        }

        return $date->setTime(fake()->numberBetween(8, 16), 0);
    }
}
