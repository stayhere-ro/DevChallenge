<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Carbon\Carbon;

class CheckBusinessHours implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
     public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $time = Carbon::parse($value);

        if ($time->hour < 8 || $time->hour >= 17) {
            $fail('Bookings are only available between 8:00 AM and 5:00 PM.');
        }
    }
}
