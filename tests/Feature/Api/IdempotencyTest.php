<?php

namespace Tests\Feature\Api;

use App\Models\Hairdresser;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class IdempotencyTest extends TestCase
{
    public function test_replays_response_for_same_idempotency_key(): void
    {
        Cache::flush();
        $hairdresser = Hairdresser::factory()->create();
        $date = Carbon::parse('next monday')->toDateString();
        $payload = [
            'email' => 'client@example.com',
            'hairdresser_id' => $hairdresser->id,
            'date' => $date,
            'start_time' => '12:00',
        ];

        $first = $this->postJson('/api/bookings', $payload, ['Idempotency-Key' => 'abc-123']);
        $second = $this->postJson('/api/bookings', $payload, ['Idempotency-Key' => 'abc-123']);

        $first->assertCreated();
        $second->assertStatus(201);
        $this->assertSame($first->json('data.id'), $second->json('data.id'));
        $this->assertDatabaseCount('bookings', 1);
    }
}
