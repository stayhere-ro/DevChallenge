<?php

namespace Database\Factories;

use App\Models\Hairdresser;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Booking>
 */
class BookingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Generate date
        $date = $this->faker->dateTimeBetween('now', '+1 month');
        while (in_array($date->format('N'), [6, 7])) {
            $date = $this->faker->dateTimeBetween('now', '+1 month');
        }
        $date->setTime($this->faker->numberBetween(8, 16), 0, 0);

        $randomUser = User::all()->random();
        $randomHairdresser = Hairdresser::all()->random();

        return [
            'hairdresser_id' => $randomHairdresser->id,
            'user_id' => $randomUser->id,
            'name' => $randomUser->name,
            'email' => $randomUser->email,
            'scheduled_at' => $date,
        ];
    }
}
