<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class HairdresserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a hairdresser user for admin access
        User::updateOrCreate(
            ['email' => 'hairdresser@example.com'],
            [
                'name' => 'Hairdresser Admin',
                'password' => Hash::make('password'),
                'role' => 'hairdresser',
            ]
        );

        for ($i = 1; $i <= 5; $i++) {
            User::updateOrCreate(
                ['email' => "hairdresser{$i}@example.com"],
                [
                    'name' => fake()->name(),
                    'password' => Hash::make('password'),
                    'role' => 'hairdresser',
                ]
            );
        }

        $this->command->info('Hairdressers seeded.');
        $this->command->info('Example Login: hairdresser@example.com / password');
    }
}
