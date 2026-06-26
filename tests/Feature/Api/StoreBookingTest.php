<?php

namespace Tests\Feature\Api;

use App\Models\Hairdresser;
use Carbon\Carbon;
use Tests\TestCase;

class StoreBookingTest extends TestCase
{
    public function test_creates_booking_via_api(): void
    {
        $hairdresser = Hairdresser::factory()->create();
        $date = Carbon::parse('next monday')->toDateString();

        $response = $this->postJson('/api/bookings', [
            'email' => 'client@example.com',
            'name' => 'Client',
            'hairdresser_id' => $hairdresser->id,
            'date' => $date,
            'start_time' => '10:00',
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.email', 'client@example.com')
            ->assertJsonPath('data.hairdresser_id', $hairdresser->id);

        $this->assertDatabaseCount('bookings', 1);
    }

    public function test_returns_422_for_weekend(): void
    {
        $hairdresser = Hairdresser::factory()->create();

        $response = $this->postJson('/api/bookings', [
            'email' => 'client@example.com',
            'hairdresser_id' => $hairdresser->id,
            'date' => Carbon::parse('next saturday')->toDateString(),
            'start_time' => '10:00',
        ]);

        $response->assertStatus(422);
    }
}
