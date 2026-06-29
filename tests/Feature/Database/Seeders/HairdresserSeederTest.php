<?php

namespace Tests\Feature\Database\Seeders;

use App\Models\Booking;
use App\Models\User;
use Database\Seeders\HairdresserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class HairdresserSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_seeds_hairdressers_and_bookings(): void
    {
        $this->seed(HairdresserSeeder::class);

        $this->assertDatabaseHas('users', [
            'name' => 'Hairdresser Admin',
            'email' => 'hairdresser@example.com',
        ]);
        $this->assertDatabaseHas('users', [
            'name' => 'Levente Simon',
            'email' => 'levente.simon@example.com',
        ]);
        $this->assertDatabaseHas('users', [
            'name' => 'László Bedő',
            'email' => 'laszlo.bedo@example.com',
        ]);

        $this->assertSame(3, User::count());
        $this->assertSame(24, Booking::count());

        User::all()->each(function (User $hairdresser): void {
            $this->assertTrue(Hash::check('password', $hairdresser->password));
            $this->assertSame(8, Booking::where('hairdresser_id', $hairdresser->id)->count());
        });
    }

    public function test_it_is_idempotent(): void
    {
        $this->seed(HairdresserSeeder::class);
        $this->seed(HairdresserSeeder::class);

        $this->assertSame(3, User::count());
        $this->assertSame(24, Booking::count());
    }
}
