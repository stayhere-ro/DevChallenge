<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ApiBookingTest extends TestCase
{
    public function test_can_create_booking_successfully(): void {
        $user = \App\Models\User::factory()->create();

        $payload = [
            'client_email' => 'test@example.com',
            'hairdresser_id' => $user->id,
            'date' => now()->addDay()->toDateString(),
            'start_time' => '14:00',
        ];

        $response = $this->postJson('/api/bookings', $payload);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'success',
                    'message'
                ]);

        $this->assertDatabaseHas('bookings', [
            'email' => 'test@example.com',
            'hairdresser_id' => $user->id,
        ]);
    }

    public function test_booking_fails_when_required_fields_missing(): void {
        $response = $this->postJson('/api/bookings', []);

        $response->assertStatus(422)
                ->assertJson([
                    'success' => false,
                ])
                ->assertJsonStructure([
                    'errors'
                ]);
    }

    public function test_booking_fails_outside_business_hours(): void {
        $user = \App\Models\User::factory()->create();

        $payload = [
            'client_email' => 'test@example.com',
            'hairdresser_id' => $user->id,
            'date' => now()->addDay()->toDateString(),
            'start_time' => '22:00',
        ];

        $response = $this->postJson('/api/bookings', $payload);

        $response->assertStatus(422)
                ->assertJson([
                    'success' => false
                ]);
    }

    public function test_cannot_book_same_hairdresser_same_time_twice(): void {
        $user = \App\Models\User::factory()->create();

        $payload = [
            'client_email' => 'test@example.com',
            'hairdresser_id' => $user->id,
            'date' => now()->addDay()->toDateString(),
            'start_time' => '14:00',
        ];

        // first booking succeeds
        $this->postJson('/api/bookings', $payload)
            ->assertStatus(201);

        // second booking should fail
        $this->postJson('/api/bookings', $payload)
            ->assertStatus(422);
    }

    public function test_can_book_same_time_for_different_hairdressers(): void {
        $hairdresser1 = \App\Models\User::factory()->create();
        $hairdresser2 = \App\Models\User::factory()->create();

        $payload1 = [
            'client_email' => 'client1@example.com',
            'hairdresser_id' => $hairdresser1->id,
            'date' => now()->addDay()->toDateString(),
            'start_time' => '14:00',
        ];

        $payload2 = [
            'client_email' => 'client2@example.com',
            'hairdresser_id' => $hairdresser2->id,
            'date' => now()->addDay()->toDateString(),
            'start_time' => '14:00',
        ];

        // First booking
        $this->postJson('/api/bookings', $payload1)
            ->assertStatus(201);

        // Second booking at same time but different hairdresser
        $this->postJson('/api/bookings', $payload2)
            ->assertStatus(201);

        // Both must exist in DB
        $this->assertDatabaseHas('bookings', [
            'email' => 'client1@example.com',
            'hairdresser_id' => $hairdresser1->id,
        ]);

        $this->assertDatabaseHas('bookings', [
            'email' => 'client2@example.com',
            'hairdresser_id' => $hairdresser2->id,
        ]);
    }
}
