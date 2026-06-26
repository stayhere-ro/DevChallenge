<?php

namespace App\Services\Booking;

use App\Exceptions\BookingUnavailableException;
use App\Models\Hairdresser;
use Carbon\Carbon;

class BookingAvailabilityChecker
{
    public function assertBookable(
        int $hairdresserId,
        Carbon $scheduledAt,
        bool $slotAlreadyBooked = false,
    ): void {
        $this->assertHairdresserActive($hairdresserId);
        $this->assertNotInPast($scheduledAt);
        $this->assertWeekday($scheduledAt);
        $this->assertBusinessHours($scheduledAt);
        $this->assertWholeHourSlot($scheduledAt);

        if ($slotAlreadyBooked) {
            throw new BookingUnavailableException('This time slot is already booked. Please choose another time.');
        }
    }

    public function assertHairdresserActive(int $hairdresserId): void
    {
        $exists = Hairdresser::active()->whereKey($hairdresserId)->exists();

        if (! $exists) {
            throw new BookingUnavailableException('The selected hairdresser is not available.');
        }
    }

    public function assertNotInPast(Carbon $scheduledAt): void
    {
        if ($scheduledAt->isPast()) {
            throw new BookingUnavailableException('Bookings must be scheduled for a future time.');
        }
    }

    public function assertWeekday(Carbon $scheduledAt): void
    {
        if ($scheduledAt->isWeekend()) {
            throw new BookingUnavailableException('Bookings are not available on weekends.');
        }
    }

    public function assertBusinessHours(Carbon $scheduledAt): void
    {
        $start = (int) config('booking.business_hours.start');
        $end = (int) config('booking.business_hours.end');
        $hour = (int) $scheduledAt->format('H');

        if ($hour < $start || $hour >= $end) {
            throw new BookingUnavailableException('Bookings are only available between 8:00 AM and 5:00 PM.');
        }
    }

    public function assertWholeHourSlot(Carbon $scheduledAt): void
    {
        if ($scheduledAt->minute !== 0 || $scheduledAt->second !== 0) {
            throw new BookingUnavailableException('Bookings must start on the hour.');
        }
    }
}
