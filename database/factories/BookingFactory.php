<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Booking>
 */
class BookingFactory extends Factory
{
    protected $model = Booking::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'hairdresser_id' => User::factory(),
            'name' => $this->faker->name(),
            'email' => $this->faker->safeEmail(),
            'scheduled_at' => $this->randomValidSlot(),
        ];
    }

    private function randomValidSlot(): Carbon
    {
        $date = Carbon::now(config('app.timezone'))
            ->addDays($this->faker->numberBetween(1, 30))
            ->setTime($this->faker->numberBetween(8, 16), 0, 0);

        while ($date->isWeekend()) {
            $date->addDay();
        }

        return $date;
    }
}
