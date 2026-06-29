<?php

namespace Database\Seeders;

use App\Models\Hairdresser;
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
        $user = User::updateOrCreate(
            ['email' => 'hairdresser@example.com'],
            [
                'name' => 'Hairdresser Admin',
                'password' => Hash::make('password'),
            ]
        );

        Hairdresser::updateOrCreate(
            ['user_id' => $user->id],
            ['name' => 'Hairdresser Admin']
        );

        $annaUser = User::updateOrCreate(
            ['email' => 'anna@example.com'],
            [
                'name' => 'Anna Stylist',
                'password' => Hash::make('password'),
            ]
        );

        Hairdresser::updateOrCreate(
            ['user_id' => $annaUser->id],
            ['name' => 'Anna Stylist']
        );

        $this->command->info('Hairdresser user created successfully!');
        $this->command->info('Email: hairdresser@example.com');
        $this->command->info('Password: password');
    }
}
