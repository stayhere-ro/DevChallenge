<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Booking;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class HairdresserSeeder extends Seeder
{
    public function run(): void
    {
        $hairdressers = [
            ['name' => 'Hairdresser Admin', 'email' => 'hairdresser@example.com'],
            ['name' => 'Levente Simon', 'email' => 'levente.simon@example.com'],
            ['name' => 'László Bedő', 'email' => 'laszlo.bedo@example.com'],
        ];

        $faker = Faker::create();

        $baseBookings = [
            '2026-07-01 11:00:00',
            '2026-07-02 12:00:00',
            '2026-07-03 13:00:00',
            '2026-07-04 14:00:00',
        ];

        foreach ($hairdressers as $hairdresser) {

            $user = User::updateOrCreate(
                ['email' => $hairdresser['email']],
                [
                    'name' => $hairdresser['name'],
                    'password' => Hash::make('password'),
                ]
            );

            foreach ($baseBookings as $date) {
                $name = $faker->name();
                $email = $faker->unique()->safeEmail();

                Booking::updateOrCreate(
                    [
                        'scheduled_at' => Carbon::parse($date),
                        'hairdresser_id' => $user->id,
                    ],
                    [
                        'name' => $name,
                        'email' => $email,
                    ]
                );

                Booking::updateOrCreate(
                    [
                        'scheduled_at' => Carbon::parse($date)->addDays(7),
                        'hairdresser_id' => $user->id,
                    ],
                    [
                        'name' => $name,
                        'email' => $email,
                    ]
                );
            }
        }

        $this->command->info(count($hairdressers) . ' hairdressers seeded successfully!');
        $this->command->info('Bookings created for each hairdresser.');
        $this->command->info('Default password: password');
    }
}