<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Booking;
use App\Models\Hairdresser;
use App\Models\User;
use Database\Factories\BookingFactory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create a hairdresser user for admin access
        Hairdresser::factory()->create([
            'name' => 'Hairdresser Admin',
            'email' => 'hairdresser@example.com',
            'password' => Hash::make('password'),
        ]);

        Hairdresser::factory(5)->create();

        User::factory(15)->create();

        Booking::factory(12)->create();
    }


}
