<?php

namespace Tests\Feature\Api;

use App\Models\Booking;
use App\Models\Hairdresser;
use Carbon\Carbon;
use Tests\TestCase;

class BookingConflictTest extends TestCase
{
    public function test_returns_409_when_slot_already_booked(): void
    {
        $hairdresser = Hairdresser::factory()->create();
        $date = Carbon::parse('next monday')->toDateString();
        $payload = [
            'email' => 'other@example.com',
            'hairdresser_id' => $hairdresser->id,
            'date' => $date,
            'start_time' => '11:00',
        ];

        Booking::factory()->forHairdresser($hairdresser)->create([
            'scheduled_at' => Carbon::parse($date.' 11:00:00'),
        ]);

        $this->postJson('/api/bookings', $payload)
            ->assertStatus(409)
            ->assertJsonPath('error', 'booking_conflict');
    }

    public function test_allows_same_time_for_different_hairdressers(): void
    {
        $a = Hairdresser::factory()->create();
        $b = Hairdresser::factory()->create();
        $date = Carbon::parse('next monday')->toDateString();

        Booking::factory()->forHairdresser($a)->create([
            'scheduled_at' => Carbon::parse($date.' 11:00:00'),
        ]);

        $this->postJson('/api/bookings', [
            'email' => 'client@example.com',
            'hairdresser_id' => $b->id,
            'date' => $date,
            'start_time' => '11:00',
        ])->assertCreated();
    }
}
