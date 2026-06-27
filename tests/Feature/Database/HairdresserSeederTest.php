<?php

namespace Tests\Feature\Database;

use App\Models\Hairdresser;
use Database\Seeders\HairdresserSeeder;
use Tests\TestCase;

class HairdresserSeederTest extends TestCase
{
    public function test_seeds_three_active_stylists(): void
    {
        $this->seed(HairdresserSeeder::class);

        $this->assertDatabaseCount('hairdressers', 3);

        $this->assertDatabaseHas('hairdressers', [
            'email' => 'hairdresser@example.com',
            'name' => 'Alex Morgan',
            'location' => 'Main Salon',
            'is_active' => true,
        ]);

        $this->assertEquals(3, Hairdresser::active()->count());
    }
}
