<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
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
            ]
        );

        $this->command->info('Hairdresser user created successfully!');
        $this->command->info('Email: hairdresser@example.com');
        $this->command->info('Password: password');
    }
}

