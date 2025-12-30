<?php

namespace Tests\Feature;

use App\Models\Hairdresser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class BookingTest extends TestCase
{
    use RefreshDatabase;

    /** @test  */
    public function can_create_a_booking_successfully()
    {
        $hairdresser = Hairdresser::factory()->create();
        $date = now()->nextWeekday()->format('Y-m-d');

        $response = $this->postJson('/api/bookings', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'hairdresser_id' => $hairdresser->id,
            'date' => $date,
            'hour' => '10:00',
        ]);

        $response->assertStatus(201)->assertJsonPath('message', 'Booking confirmed! We look forward to seeing you.');
        $this->assertDatabaseHas('bookings', ['email' => 'john@example.com']);
    }


    /** @test */
    public function fails_when_booking_on_weekend()
    {
        $hairdresser = Hairdresser::factory()->create();

        // date is on weekend
        $date = now()->nextWeekendDay()->format('Y-m-d');

        $response = $this->postJson('/api/bookings', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'hairdresser_id' => $hairdresser->id,
            'date' => $date,
            'hour' => '10:00',
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors('date');
    }

    /** @test */
    public function fails_when_booking_after_working_hours()
    {
        $hairdresser = Hairdresser::factory()->create();

        $date = now()->nextWeekday()->format('Y-m-d');

        $response = $this->postJson('/api/bookings', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'hairdresser_id' => $hairdresser->id,
            'date' => $date,
            'hour' => '18:00',
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors('hour');
    }

    /** @test */
    public function fails_when_bookings_overlap()
    {
        $hairdresser = Hairdresser::factory()->create();
        $date = now()->nextWeekday()->format('Y-m-d');

        $this->postJson('/api/bookings', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'hairdresser_id' => $hairdresser->id,
            'date' => $date,
            'hour' => '10:00',
        ])->assertStatus(201);

        $response = $this->postJson('/api/bookings', [
            'name' => 'James Doe',
            'email' => 'james@example.com',
            'hairdresser_id' => $hairdresser->id,
            'date' => $date,
            'hour' => '10:00',
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors('hour');
    }
}
