<?php

namespace Tests\Feature;

use App\Models\Hairdresser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class BookingRateLimiterTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_throttles_booking_requests_after_exceeding_limit()
    {
        $hairdresser = Hairdresser::factory()->create();

        $hours = [
            '08:00', '08:30', '09:00', '09:30', '10:00',
            '10:30', '11:00', '11:30', '12:00', '12:30'
        ];

        // Create 10 valid bookings
        foreach ($hours as $hour) {
            $this->postJson('/api/bookings',[
                'hairdresser_id' => $hairdresser->id,
                'date' => '2026-03-24',
                'hour' => $hour,
                'name' => 'John Doe',
                'email' => 'johndoe@email.com'
            ])->assertStatus(201);
        }

        // The 11. booking should trigger the Rate Limiter
        $response = $this->postJson('/api/bookings', [
            'hairdresser_id' => $hairdresser->id,
            'date' => '2026-03-24',
            'hour' => '13:00',
            'name' => 'John Doe',
            'email' => 'johndoe@email.com'
        ]);

        // Verify if the status code and error message
        $response->assertStatus(429);
        $response->assertJson([
            'message' => 'Too many booking attempts.'
        ]);
    }
}
