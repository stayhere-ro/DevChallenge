<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

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
        do {
            $date = $this->faker->dateTimeBetween('now', '+1 month');
        } while (in_array((int)$date->format('N'), [6, 7]));
        $hour = $this->faker->numberBetween(8, 18);
        $minute = $this->faker->numberBetween(0, 59);
        $time = Carbon::createFromTime($hour, $minute)->format('H:i');


        return [
            'date'=>$date->format('Y-m-d'),
            'time'=>$time,
            'user_id'=>User::inRandomOrder()->value('id')
        ];
    }
}
