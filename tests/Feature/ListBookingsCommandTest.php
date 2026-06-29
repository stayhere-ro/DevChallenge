<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Hairdresser;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ListBookingsCommandTest extends TestCase
{
    use RefreshDatabase;

    private const CLIENT_EMAIL = 'gergo@gmail.com';

    public function test_it_lists_bookings_for_the_given_email(): void
    {
        $hairdresser = Hairdresser::factory()->create();
        $scheduledAt = Carbon::parse('2026-07-06 10:00:00');

        Booking::create([
            'name' => 'Gergo',
            'email' => self::CLIENT_EMAIL,
            'hairdresser_id' => $hairdresser->id,
            'scheduled_at' => $scheduledAt,
        ]);

        $this->artisan('bookings:list', ['email' => self::CLIENT_EMAIL])
            ->expectsTable(
                ['Name', 'Date', 'Time', 'Hairdresser ID'],
                [
                    ['Gergo', '2026-07-06', '10:00', $hairdresser->id],
                ]
            )
            ->assertSuccessful();
    }

    public function test_it_shows_a_message_when_no_bookings_exist_for_the_email(): void
    {
        $this->artisan('bookings:list', ['email' => self::CLIENT_EMAIL])
            ->expectsOutput('No bookings found for this email.')
            ->assertSuccessful();
    }
}
