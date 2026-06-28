<?php

namespace App\Http\Traits;

use Carbon\Carbon;
use App\Models\Booking;

trait BookingValidation
{
    public function validateBookingBusinessRules($validator): void
    {
        $validator->after(function ($validator) {
            // TODO -> Should i consider the FormRequest with $this call it's not the best approach but for now it's ok... Could be better..
            $date = $this->date;
            $hour = $this->hour;

            if ($date && $hour) {
                // Check if weekend
                $carbonDate = Carbon::parse($date);
                if ($carbonDate->isWeekend()) {
                    $validator->errors()->add('date', 'Bookings are not available on weekends.');
                }

                // Check business hours (8:00 AM - 5:00 PM)
                $hourTime = Carbon::createFromFormat('H:i', $hour);
                if ($hourTime->hour < 8 || $hourTime->hour >= 17) {
                    $validator->errors()->add('hour', 'Bookings are only available between 8:00 AM and 5:00 PM.');
                }

                // Combine into scheduled_at and check if the time slot is already booked
                $scheduledAt = Carbon::parse($date . ' ' . $hour . ':00');
                $exists = Booking::where('scheduled_at', $scheduledAt)
                ->where('hairdresser_id', $this->hairdresser_id)
                ->exists();

                if ($exists) {
                    $validator->errors()->add('hour', 'This time slot is already booked. Please choose another time.');
                }
            }
        });
    }
}
