<?php

namespace Database\Factories;

use App\Models\Hairdresser;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Hairdresser>
 */
class HairdresserFactory extends Factory
{
    protected $model = Hairdresser::class;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'location' => fake()->city(),
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }
}
