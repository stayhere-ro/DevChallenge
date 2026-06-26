<?php

namespace Tests\Feature\Console;

use App\Models\Booking;
use App\Models\Hairdresser;
use Carbon\Carbon;
use Tests\TestCase;

class ListBookingsCommandTest extends TestCase
{
    public function test_lists_bookings_for_email(): void
    {
        $hairdresser = Hairdresser::factory()->create(['name' => 'Jane']);
        $scheduledAt = Carbon::parse('next monday 10:00');

        Booking::factory()->forHairdresser($hairdresser)->create([
            'email' => 'client@example.com',
            'scheduled_at' => $scheduledAt,
        ]);

        $this->artisan('bookings:list', ['email' => 'client@example.com'])
            ->assertSuccessful()
            ->expectsTable(
                ['Date', 'Time', 'Hairdresser ID', 'Hairdresser'],
                [[
                    $scheduledAt->toDateString(),
                    '10:00',
                    $hairdresser->id,
                    'Jane',
                ]]
            );
    }

    public function test_warns_when_no_bookings_found(): void
    {
        $this->artisan('bookings:list', ['email' => 'none@example.com'])
            ->assertSuccessful()
            ->expectsOutputToContain('No bookings found for none@example.com.');
    }
}
