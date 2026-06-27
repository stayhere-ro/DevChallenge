<?php

namespace Tests\Feature\Admin;

use App\Models\Booking;
use App\Models\Hairdresser;
use App\Models\User;
use Carbon\Carbon;
use Tests\TestCase;

class DashboardBookingsFilterTest extends TestCase
{
    public function test_search_filters_by_client_name(): void
    {
        $user = User::factory()->create();
        $hairdresser = Hairdresser::factory()->create();

        Booking::factory()->forHairdresser($hairdresser)->create([
            'name' => 'Unique Client Alpha',
            'email' => 'alpha@example.com',
            'scheduled_at' => Carbon::parse('next monday 10:00'),
        ]);
        Booking::factory()->forHairdresser($hairdresser)->create([
            'name' => 'Someone Else',
            'email' => 'other@example.com',
            'scheduled_at' => Carbon::parse('next monday 11:00'),
        ]);

        $this->actingAs($user)
            ->get(route('admin.dashboard', ['search' => 'Unique Client']))
            ->assertOk()
            ->assertSee('Unique Client Alpha')
            ->assertDontSee('Someone Else');
    }

    public function test_sorts_by_scheduled_at_descending(): void
    {
        $user = User::factory()->create();
        $hairdresser = Hairdresser::factory()->create();

        Booking::factory()->forHairdresser($hairdresser)->create([
            'name' => 'Earlier',
            'scheduled_at' => Carbon::parse('next monday 09:00'),
        ]);
        Booking::factory()->forHairdresser($hairdresser)->create([
            'name' => 'Later',
            'scheduled_at' => Carbon::parse('next monday 15:00'),
        ]);

        $response = $this->actingAs($user)
            ->get(route('admin.dashboard', ['sort' => 'scheduled_at', 'direction' => 'desc']))
            ->assertOk()
            ->assertSeeInOrder(['Later', 'Earlier']);
    }
}
