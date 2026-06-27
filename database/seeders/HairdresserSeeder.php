<?php

namespace Database\Seeders;

use App\Models\Hairdresser;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class HairdresserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'hairdresser@example.com'],
            [
                'name' => 'Hairdresser Admin',
                'password' => Hash::make('password'),
            ]
        );

        $defaults = [
            ['name' => 'Alex Morgan', 'email' => 'hairdresser@example.com', 'location' => 'Main Salon'],
            ['name' => 'Maria Popescu', 'email' => 'north@example.com', 'location' => 'Studio North'],
            ['name' => 'David Ionescu', 'email' => 'south@example.com', 'location' => 'Studio South'],
        ];

        foreach ($defaults as $hairdresser) {
            Hairdresser::updateOrCreate(
                ['email' => $hairdresser['email']],
                [
                    'name' => $hairdresser['name'],
                    'location' => $hairdresser['location'],
                    'is_active' => true,
                ]
            );
        }

        $this->command?->info('Hairdressers and admin user seeded.');
        $this->command?->info('Admin login: hairdresser@example.com / password');
    }
}
