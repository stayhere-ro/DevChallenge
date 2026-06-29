<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Booking;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_index_displays_booking_form_with_hairdressers_and_bookings(): void
    {
        $hairdresser = User::factory()->create([
            'name' => 'Test Hairdresser',
        ]);

        $booking = Booking::create([
            'hairdresser_id' => $hairdresser->id,
            'name' => 'Alice Client',
            'email' => 'alice@example.com',
            'scheduled_at' => '2026-07-01 09:00:00',
        ]);

        $response = $this->get(route('bookings.index'));

        $response->assertOk();
        $response->assertViewIs('bookings.index');
        $response->assertViewHas('users', fn ($users) => $users->contains($hairdresser));
        $response->assertViewHas('bookings', fn ($bookings) => $bookings->contains($booking));
        $response->assertSee('Test Hairdresser');
        $response->assertSee('9:00 AM');
    }

    public function test_store_creates_booking_and_redirects_to_booking_form(): void
    {
        Carbon::setTestNow('2026-06-29 10:00:00');

        $hairdresser = User::factory()->create();

        $response = $this->post(route('bookings.store'), [
            'name' => 'Alice Client',
            'email' => 'alice@example.com',
            'date' => '2026-07-01',
            'hour' => '09:00',
            'hairdresser_id' => $hairdresser->id,
        ]);

        $response->assertRedirect(route('bookings.index'));
        $response->assertSessionHas('success', 'Booking confirmed! We look forward to seeing you.');

        $this->assertDatabaseHas('bookings', [
            'name' => 'Alice Client',
            'email' => 'alice@example.com',
            'scheduled_at' => '2026-07-01 09:00:00',
            'hairdresser_id' => $hairdresser->id,
        ]);
    }
}
