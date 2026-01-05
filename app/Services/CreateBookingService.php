<?php

namespace App\Services;

use App\DTO\CreateBookingData;
use App\Exceptions\BookingValidationException;
use App\Models\Booking;
use Carbon\Carbon;

class CreateBookingService
{
    public function create(CreateBookingData $data, string $timezone): Booking
    {
        $scheduledAt = $data->scheduledAt;
        $now = Carbon::now($timezone);

        // Weekend check
        if ($scheduledAt->isWeekend()) {
            throw BookingValidationException::forField('date', 'Bookings are not available on weekends.');
        }

        // On the hour check
        if ($scheduledAt->minute !== 0) {
            throw BookingValidationException::forField('start_time', 'Bookings must start exactly on the hour (e.g., 10:00).');
        }

        // Business hours check
        if ($scheduledAt->hour < 8 || $scheduledAt->hour >= 17) {
            throw BookingValidationException::forField('start_time', 'Bookings are only available between 08:00 AM and 5:00 PM.');
        }

        // Future slot check
        if ($scheduledAt->lte($now)) {
            throw BookingValidationException::forField('start_time', 'Please choose a time slot in the future.');
        }

        // Collision check
        $exists = Booking::query()
            ->where('hairdresser_id', $data->hairdresserId)
            ->where('scheduled_at', $scheduledAt)
            ->exists();

        if ($exists) {
            throw BookingValidationException::forField('start_time', 'This time slot is already booked. Please choose another time.');
        }

        return Booking::create([
            'name' => $data->clientName,
            'email' => $data->clientEmail,
            'hairdresser_id' => $data->hairdresserId,
            'scheduled_at' => $scheduledAt,
        ]);
    }
}
