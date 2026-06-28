<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class BookingTests extends TestCase
{
    /**
     * A basic feature test example.
     */
   public function test_validation_email_fails_for_api_booking_route(): void
    {

        $payload = [
            'hairdresser_id' => 1,
            'email' => 'johnexample@example.test',
            'date' => '2024-07-01',
            'hour' => '09:00',
        ];

        $response = $this->post('/api/bookings', $payload);

        $response->assertStatus(422)
        ->assertJsonValidationErrors([
            'email',
        ]);
    }

    public function test_validation_hairdresser_id_fails_for_api_booking_route(): void
    {

        $payload = [
            'hairdresser_id' => 4,
            'email' => 'johnexample@example.test',
            'date' => '2024-07-01',
            'hour' => '09:00',
        ];

        $response = $this->post('/api/bookings', $payload);

        $response->assertStatus(422)
        ->assertJsonValidationErrors([
            'hairdresser_id',
        ]);
    }

    public function test_validation_date_and_hour_fails_for_api_booking_route(): void
    {
        $payload = [
            'hairdresser_id' => 4,
            'email' => 'johnexample@example.test',
            'date' => 'test',
        ];

        $response = $this->post('/api/bookings', $payload);

        $response->assertStatus(422)
        ->assertJsonValidationErrors([
            'date',
            'hour',
        ]);
    }

    public function test_validation_double_booking_fails_for_api_booking_route(): void
    {
        $payload = [
            'hairdresser_id' => 1,
            'email' => 'johnexample@example.test',
            'date' => '2024-07-01',
            'hour' => '09:00',
        ];

        $response = $this->post('/api/bookings', $payload);

        $response = $this->post('/api/bookings', $payload);

        $response->assertStatus(422)
        ->assertJsonValidationErrors([
            'hour',
        ]);
    }
}
