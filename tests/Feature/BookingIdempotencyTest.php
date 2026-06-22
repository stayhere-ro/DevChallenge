<?php

namespace Tests\Feature;

use App\Models\Hairdresser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class BookingIdempotencyTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_creates_only_one_booking_for_the_same_idempotency_key(){

        $hairdresser = Hairdresser::factory()->create();

        $data = [
            'hairdresser_id' => $hairdresser->id,
            'date' => '2026-03-24',
            'hour' => '10:00',
            'name' => 'John Doe',
            'email' => 'johndoe@email.com'
        ];

        $headers = ['Idempotency-Key' => 'test-key-1234'];

        // First request
        $response1 = $this->postJson('/api/bookings', $data, $headers);
        $response1->assertStatus(201);

        // Check if DB has the given entry
        $this->assertDatabaseHas('bookings', [
            'hairdresser_id' => $hairdresser->id,
            'email' => 'johndoe@email.com'
        ]);

        // Second request
        $response2 = $this->postJson('/api/bookings', $data, $headers);
        $response2->assertStatus(201);

        // Check if the DB has only one entry
        $this->assertDatabaseCount('bookings', 1);

    }

    /** @test */
    public function it_does_not_cache_failed_responses()
    {
        $headers = ['Idempotency-Key' => 'error-key'];

        // Invalid request data
        $this->postJson('/api/bookings', [], $headers)->assertStatus(422);

        // Verify the request is not cached
        $this->assertFalse(Cache::has('idempotency_key:error-key'));
    }


}
