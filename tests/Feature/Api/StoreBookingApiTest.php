<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use Carbon\Carbon;

class StoreBookingApiTest extends TestCase
{
    /**
     * A basic feature test example.
     */
   public function test_validation_email_fails_for_api_booking_route(): void
    {

        $payload = [
            'hairdresser_id' => 1,
            'email' => 'johnexamexample.test',
            'date' => '2024-07-01',
            'hour' => '09:00',
        ];

        $this->postJson('/api/bookings', $payload)
        ->assertStatus(422)
        ->assertJsonValidationErrors([
            'email',
        ]);
    }

    public function test_validation_hairdresser_id_fails_for_api_booking_route(): void
    {

        $payload = [
            'hairdresser_id' => 4000,
            'email' => 'johnexample@example.test',
            'date' => '2024-07-01',
            'hour' => '09:00',
        ];

        $this->postJson('/api/bookings', $payload)
        ->assertStatus(422)
        ->assertJsonValidationErrors([
            'hairdresser_id',
        ]);
    }

    public function test_validation_date_and_hour_fails_for_api_booking_route(): void
    {
        $payload = [
            'hairdresser_id' => 1,
            'email' => 'johnexample@example.test',
            'date' => 'test',
        ];

        $this->postJson('/api/bookings', $payload);

        $this->postJson('/api/bookings', $payload)
        ->assertStatus(422)
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
            'date' => Carbon::now()->addDays(1)->format('Y-m-d'),
            'hour' => Carbon::now()->setTime(rand(9, 17), rand(0, 1) ? 0 : 30)->format('H:i'),
        ];

        $this->post('/api/bookings', $payload);

        $this->postJson('/api/bookings', $payload)
            ->assertStatus(422)
            ->assertJsonValidationErrors(['hour']);
    }

    public function test_booking_success_route(): void
    {
        $payload = [
            'hairdresser_id' => 1,
            'email' => 'johnexample@example.test',
            'date' => Carbon::now()->addDays(1)->format('Y-m-d'),
            'hour' => Carbon::now()->setTime(rand(9, 17), rand(0, 1) ? 0 : 30)->format('H:i'),
        ];

        $this->postJson('/api/bookings', $payload)
        ->assertStatus(200)
        ->assertJson([
             "message" => "Booking created successfully.",
        ]);
    }
}
