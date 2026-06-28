<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Models\Booking;
use Carbon\Carbon;

class CheckHairdresserAndScheduledHours implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
   public function __construct(
        private ?int $hairdresserId = null,
        private ?string $date = null
    ) {}

    public function validate(string $attribute, mixed $hour, Closure $fail): void
    {
        $scheduledAt = Carbon::createFromFormat('Y-m-d H:i', "{$this->date} {$hour}");

        $exists = Booking::query()
            ->when($this->hairdresserId, fn ($q) => $q->where('hairdresser_id', $this->hairdresserId))
            ->where('scheduled_at', $scheduledAt)
            ->exists();

        if ($exists) {
            $fail('This time slot is already booked. Please choose another time.');
        }
    }
}
