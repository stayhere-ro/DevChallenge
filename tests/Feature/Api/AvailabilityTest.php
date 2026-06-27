<?php

namespace Tests\Feature\Api;

use App\Models\Booking;
use App\Models\Hairdresser;
use Carbon\Carbon;
use Tests\TestCase;

class AvailabilityTest extends TestCase
{
    public function test_returns_available_slots_for_week(): void
    {
        $hairdresser = Hairdresser::factory()->create();
        $monday = Carbon::parse('next monday')->startOfDay();

        Booking::factory()->create([
            'hairdresser_id' => $hairdresser->id,
            'scheduled_at' => $monday->copy()->setTime(10, 0),
        ]);

        $response = $this->getJson('/api/availability?'.http_build_query([
            'hairdresser_id' => $hairdresser->id,
            'week_start' => $monday->toDateString(),
            'week_end' => $monday->copy()->addDays(6)->toDateString(),
        ]));

        $response->assertOk()
            ->assertJsonPath("slots.{$monday->toDateString()}.0.hour", '08:00');

        $slots = collect($response->json("slots.{$monday->toDateString()}"))->pluck('hour');
        $this->assertFalse($slots->contains('10:00'));
    }
}
