<?php

namespace Tests\Feature\Console\Commands;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingsListCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_lists_bookings_for_an_email_ordered_by_scheduled_at(): void
    {
        $hairdresser = User::factory()->create();

        Booking::create([
            'hairdresser_id' => $hairdresser->id,
            'name' => 'Alice Client',
            'email' => 'alice@example.com',
            'scheduled_at' => '2026-07-02 12:00:00',
        ]);
        Booking::create([
            'hairdresser_id' => $hairdresser->id,
            'name' => 'Alice Client',
            'email' => 'alice@example.com',
            'scheduled_at' => '2026-07-01 09:00:00',
        ]);
        Booking::create([
            'hairdresser_id' => $hairdresser->id,
            'name' => 'Other Client',
            'email' => 'other@example.com',
            'scheduled_at' => '2026-07-01 10:00:00',
        ]);

        $this->artisan('bookings:list alice@example.com')
            ->expectsTable(
                ['Date', 'Time', 'Hairdresser ID'],
                [
                    ['2026-07-01', '09:00', $hairdresser->id],
                    ['2026-07-02', '12:00', $hairdresser->id],
                ]
            )
            ->assertExitCode(0);
    }

    public function test_it_shows_message_when_email_has_no_bookings(): void
    {
        $this->artisan('bookings:list missing@example.com')
            ->expectsOutput('No bookings found for missing@example.com')
            ->assertExitCode(0);
    }
}
