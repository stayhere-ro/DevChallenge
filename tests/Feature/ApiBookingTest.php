<?php

namespace Tests\Feature;

use App\Models\Booking;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiBookingTest extends TestCase
{
    use RefreshDatabase;

    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'client_email' => 'client@example.com',
            'hairdresser_id' => 1,
            'date' => CarbonImmutable::parse('2026-07-01')->toDateString(),
            'start_time' => '10:00',
        ], $overrides);
    }

    public function test_it_creates_a_booking_through_the_api(): void
    {
        $response = $this->postJson('/api/bookings', $this->validPayload());

        $response->assertCreated()
            ->assertJson([
                'success' => true,
                'message' => 'Booking created successfully.',
            ])
            ->assertJsonPath('data.client_email', 'client@example.com')
            ->assertJsonPath('data.hairdresser_id', 1)
            ->assertJsonPath('data.date', '2026-07-01')
            ->assertJsonPath('data.start_time', '10:00');

        $this->assertDatabaseHas('bookings', [
            'email' => 'client@example.com',
            'hairdresser_id' => 1,
        ]);

        $this->assertSame(1, Booking::count());
    }

    public function test_it_rejects_duplicate_booking_for_same_hairdresser_and_slot(): void
    {
        $payload = $this->validPayload();

        $this->postJson('/api/bookings', $payload)->assertCreated();

        $response = $this->postJson('/api/bookings', array_merge($payload, [
            'client_email' => 'second-client@example.com',
        ]));

        $response->assertStatus(409)
            ->assertJson([
                'success' => false,
                'message' => 'The selected time slot is already booked.',
            ])
            ->assertJsonValidationErrors(['start_time']);

        $this->assertSame(1, Booking::count());
    }

    public function test_it_allows_same_slot_for_different_hairdressers(): void
    {
        $payload = $this->validPayload();

        $this->postJson('/api/bookings', $payload)->assertCreated();

        $response = $this->postJson('/api/bookings', array_merge($payload, [
            'client_email' => 'second-client@example.com',
            'hairdresser_id' => 2,
        ]));

        $response->assertCreated()
            ->assertJson([
                'success' => true,
                'message' => 'Booking created successfully.',
            ]);

        $this->assertSame(2, Booking::count());
    }

    public function test_it_returns_consistent_validation_error_for_invalid_email(): void
    {
        $response = $this->postJson('/api/bookings', [
            'client_email' => 'invalid-email',
            'hairdresser_id' => 1,
            'date' => '2026-07-01',
            'start_time' => '10:00',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Validation failed.',
            ])
            ->assertJsonValidationErrors(['client_email']);
    }

    public function test_it_returns_consistent_validation_error_for_weekend_booking(): void
    {
        $response = $this->postJson('/api/bookings', [
            'client_email' => 'client@example.com',
            'hairdresser_id' => 1,
            'date' => '2026-07-04',
            'start_time' => '10:00',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Validation failed.',
            ])
            ->assertJsonValidationErrors(['date']);
    }

    public function test_it_returns_consistent_validation_error_for_non_full_hour_slot(): void
    {
        $response = $this->postJson('/api/bookings', [
            'client_email' => 'client@example.com',
            'hairdresser_id' => 1,
            'date' => '2026-07-01',
            'start_time' => '10:30',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Validation failed.',
            ])
            ->assertJsonValidationErrors(['start_time']);
    }
}
