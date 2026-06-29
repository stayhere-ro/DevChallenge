<?php

namespace Tests\Feature\Views\Bookings;

use App\Models\Booking;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IndexBladeTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_it_renders_booking_form_fields_and_time_badges(): void
    {
        Carbon::setTestNow('2026-06-29 10:00:00');

        $hairdresser = User::factory()->create([
            'name' => 'Test Hairdresser',
        ]);

        $response = $this->get(route('bookings.index'));

        $response->assertOk();
        $response->assertSee('Book Your Appointment');
        $response->assertSee('name="name"', false);
        $response->assertSee('name="email"', false);
        $response->assertSee('name="date"', false);
        $response->assertSee('name="hairdresser_id"', false);
        $response->assertSee('name="hour"', false);
        $response->assertSee('Test Hairdresser');
        $response->assertSee('8:00 AM');
        $response->assertSee('4:00 PM');
        $response->assertSee('Weekends are not available');
        $response->assertSee('Please select a time.');
        $response->assertSee('min="2026-06-29"', false);
        $response->assertSee('disabled', false);

        $this->assertNotNull($hairdresser);
    }

    public function test_it_embeds_booked_slot_keys_for_client_side_badge_filtering(): void
    {
        $hairdresser = User::factory()->create();

        Booking::create([
            'hairdresser_id' => $hairdresser->id,
            'name' => 'Alice Client',
            'email' => 'alice@example.com',
            'scheduled_at' => '2026-07-01 09:00:00',
        ]);

        $response = $this->get(route('bookings.index'));

        $response->assertOk();
        $response->assertSee($hairdresser->id.'|2026-07-01 09:00', false);
    }
}
