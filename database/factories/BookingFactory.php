<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Hairdresser;

/**
 * @extends \Illuminate\Database\Eloquent\Factories.Factory<\App\Models\Booking>
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
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'scheduled_at' => $this->faker->dateTimeBetween('+1 days', '+1 month'),
            //TODO -> WHY it's not making relationship with the hairdresser_id without the Hairdresser::factory() call? Maybe because the hairdresser_id is nullable...
            'hairdresser_id' => Hairdresser::factory(),
        ];
    }
}
