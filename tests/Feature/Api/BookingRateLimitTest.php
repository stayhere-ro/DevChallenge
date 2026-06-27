<?php

namespace Tests\Feature\Api;

use App\Models\Hairdresser;
use Carbon\Carbon;
use Tests\TestCase;

class BookingRateLimitTest extends TestCase
{
    public function test_returns_429_when_booking_rate_limit_exceeded(): void
    {
        $hairdresser = Hairdresser::factory()->create();
        $date = Carbon::parse('next monday')->toDateString();

        for ($i = 0; $i < 20; $i++) {
            $hour = 8 + intdiv($i, 2);
            $minute = ($i % 2) * 30;

            $this->postJson('/api/bookings', [
                'email' => "client{$i}@example.com",
                'name' => 'Client',
                'hairdresser_id' => $hairdresser->id,
                'date' => $date,
                'start_time' => sprintf('%02d:%02d', $hour, $minute),
            ]);
        }

        $response = $this->postJson('/api/bookings', [
            'email' => 'overflow@example.com',
            'name' => 'Client',
            'hairdresser_id' => $hairdresser->id,
            'date' => $date,
            'start_time' => '17:00',
        ]);

        $response->assertStatus(429)
            ->assertJsonPath('error', 'rate_limit_exceeded')
            ->assertHeader('Retry-After', '60');
    }
}
