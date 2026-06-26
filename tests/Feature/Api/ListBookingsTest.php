<?php

namespace Tests\Feature\Api;

use App\Models\Booking;
use App\Models\Hairdresser;
use App\Models\User;
use Carbon\Carbon;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ListBookingsTest extends TestCase
{
    public function test_lists_only_authenticated_user_bookings(): void
    {
        $user = User::factory()->create(['email' => 'client@example.com']);
        $hairdresser = Hairdresser::factory()->create();

        Booking::factory()->forHairdresser($hairdresser)->create([
            'email' => 'client@example.com',
            'scheduled_at' => Carbon::parse('next monday 10:00'),
        ]);
        Booking::factory()->forHairdresser($hairdresser)->create([
            'email' => 'other@example.com',
            'scheduled_at' => Carbon::parse('next monday 11:00'),
        ]);

        Sanctum::actingAs($user);

        $this->getJson('/api/bookings')
            ->assertOk()
            ->assertJsonCount(1, 'data');
    }
}
