<?php

namespace Tests\Feature;

use App\Models\Booking;
use Carbon\CarbonImmutable;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingConcurrencyTest extends TestCase
{
    use RefreshDatabase;

    public function test_database_unique_constraint_prevents_duplicate_hairdresser_slots(): void
    {
        $scheduledAt = CarbonImmutable::parse('2026-07-01 10:00:00');

        Booking::create([
            'name' => 'First Client',
            'email' => 'first@example.com',
            'hairdresser_id' => 1,
            'scheduled_at' => $scheduledAt,
        ]);

        $this->expectException(QueryException::class);

        Booking::create([
            'name' => 'Second Client',
            'email' => 'second@example.com',
            'hairdresser_id' => 1,
            'scheduled_at' => $scheduledAt,
        ]);
    }

    public function test_api_returns_conflict_when_duplicate_slot_is_requested(): void
    {
        $payload = [
            'client_email' => 'first@example.com',
            'hairdresser_id' => 1,
            'date' => '2026-07-01',
            'start_time' => '10:00',
        ];

        $this->postJson('/api/bookings', $payload)
            ->assertCreated();

        $this->postJson('/api/bookings', [
            'client_email' => 'second@example.com',
            'hairdresser_id' => 1,
            'date' => '2026-07-01',
            'start_time' => '10:00',
        ])
            ->assertStatus(409)
            ->assertJson([
                'success' => false,
                'message' => 'The selected time slot is already booked.',
            ])
            ->assertJsonValidationErrors(['start_time']);

        $this->assertSame(1, Booking::count());
    }

    public function test_same_time_slot_is_allowed_for_different_hairdressers(): void
    {
        $this->postJson('/api/bookings', [
            'client_email' => 'first@example.com',
            'hairdresser_id' => 1,
            'date' => '2026-07-01',
            'start_time' => '10:00',
        ])
            ->assertCreated();

        $this->postJson('/api/bookings', [
            'client_email' => 'second@example.com',
            'hairdresser_id' => 2,
            'date' => '2026-07-01',
            'start_time' => '10:00',
        ])
            ->assertCreated();

        $this->assertSame(2, Booking::count());
    }
}
