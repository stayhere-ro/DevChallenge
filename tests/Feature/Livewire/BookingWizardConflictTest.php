<?php

namespace Tests\Feature\Livewire;

use App\Livewire\BookingWizard;
use App\Models\Booking;
use App\Models\Hairdresser;
use Carbon\Carbon;
use Livewire\Livewire;
use Tests\TestCase;

class BookingWizardConflictTest extends TestCase
{
    public function test_shows_clear_error_when_slot_was_just_booked(): void
    {
        $hairdresser = Hairdresser::factory()->create();
        $date = Carbon::parse('next monday')->toDateString();

        Booking::factory()->forHairdresser($hairdresser)->create([
            'scheduled_at' => Carbon::parse($date.' 10:00:00'),
        ]);

        Livewire::test(BookingWizard::class)
            ->set('name', 'Jane Doe')
            ->set('email', 'jane@example.com')
            ->set('hairdresserId', $hairdresser->id)
            ->set('step', 3)
            ->set('selectedDate', $date)
            ->set('selectedHour', '10:00')
            ->call('confirmBooking')
            ->assertHasErrors([
                'selectedHour' => 'This time slot has just been booked. Please choose another.',
            ]);
    }
}
