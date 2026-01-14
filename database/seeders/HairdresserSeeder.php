<?php

namespace Database\Seeders;

use App\Models\Hairdresser;
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
        // Create a hairdresser  for admin access
       /* Hairdresser::updateOrCreate(
            ['email' => 'hairdresser@example.com'],
            [
                'name' => 'Hairdresser Admin',
                'password' => Hash::make('password'),
            ]
        );*/
        Hairdresser::factory(3)
            ->create();

        $this->command->info('Hairdresser  created successfully!');
       // $this->command->info('Email: hairdresser@example.com');
        $this->command->info('Password: password');
    }
}

