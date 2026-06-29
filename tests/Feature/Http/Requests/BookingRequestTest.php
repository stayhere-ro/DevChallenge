<?php

namespace Tests\Feature\Http\Requests;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingRequestTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_it_requires_booking_fields(): void
    {
        Carbon::setTestNow('2026-06-29 10:00:00');

        $response = $this->from(route('bookings.index'))->post(route('bookings.store'), []);

        $response->assertRedirect(route('bookings.index'));
        $response->assertSessionHasErrors([
            'name',
            'email',
            'date',
            'hour',
            'hairdresser_id',
        ]);
    }

    public function test_it_rejects_weekend_dates(): void
    {
        Carbon::setTestNow('2026-06-29 10:00:00');

        $hairdresser = User::factory()->create();

        $response = $this->from(route('bookings.index'))->post(route('bookings.store'), [
            'name' => 'Alice Client',
            'email' => 'alice@example.com',
            'date' => '2026-07-04',
            'hour' => '09:00',
            'hairdresser_id' => $hairdresser->id,
        ]);

        $response->assertRedirect(route('bookings.index'));
        $response->assertSessionHasErrors([
            'date' => 'Bookings are not available on weekends.',
        ]);
    }

    public function test_it_rejects_hours_outside_business_hours(): void
    {
        Carbon::setTestNow('2026-06-29 10:00:00');

        $hairdresser = User::factory()->create();

        $response = $this->from(route('bookings.index'))->post(route('bookings.store'), [
            'name' => 'Alice Client',
            'email' => 'alice@example.com',
            'date' => '2026-07-01',
            'hour' => '17:00',
            'hairdresser_id' => $hairdresser->id,
        ]);

        $response->assertRedirect(route('bookings.index'));
        $response->assertSessionHasErrors([
            'hour' => 'Bookings are only available between 8:00 AM and 5:00 PM.',
        ]);
    }

    public function test_it_rejects_unknown_hairdresser(): void
    {
        Carbon::setTestNow('2026-06-29 10:00:00');

        $response = $this->from(route('bookings.index'))->post(route('bookings.store'), [
            'name' => 'Alice Client',
            'email' => 'alice@example.com',
            'date' => '2026-07-01',
            'hour' => '09:00',
            'hairdresser_id' => 999,
        ]);

        $response->assertRedirect(route('bookings.index'));
        $response->assertSessionHasErrors([
            'hairdresser_id' => 'Please select a valid hairdresser.',
        ]);
    }
}
