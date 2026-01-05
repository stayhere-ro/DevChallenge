<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use App\Models\Booking;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Validation\Rule;

class BookingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                function ($attribute, $value, $fail) {
                    $isHairdresser = User::query()
                        ->where('email', $value)
                        ->where('role', 'hairdresser')
                        ->exists();

                    if ($isHairdresser) $fail('This email belongs to a hairdresser account. Please use a client email.');
                },
            ],
            'date' => 'required|date|after_or_equal:today',
            'hour' => 'required|date_format:H:i',
            'hairdresser_id' => [
                'required',
                'integer',
                Rule::exists('users', 'id')->where('role', 'hairdresser'),
            ],
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $date = $this->input('date');
            $hour = $this->input('hour');

            if ($date && $hour) {
                // Check if weekend
                $carbonDate = Carbon::parse($date);
                if ($carbonDate->isWeekend()) {
                    $validator->errors()->add('date', 'Bookings are not available on weekends.');
                }

                // Check business hours (8:00 AM - 5:00 PM)
                $hourTime = Carbon::createFromFormat('H:i', $hour);
                if ((int) $hourTime->format('i') !== 0) {
                    $validator->errors()->add('hour', 'Bookings must start exactly on the hour (e.g., 10:00).');
                }

                if ($hourTime->hour < 8 || $hourTime->hour >= 17) {
                    $validator->errors()->add('hour', 'Bookings are only available between 8:00 AM and 5:00 PM.');
                }

                // Combine into scheduled_at and check if
                $scheduledAt = Carbon::parse($date . ' ' . $hour . ':00');
                // the time slot is in the past for a valid date
                if ($scheduledAt->lte(now())) {
                    $validator->errors()->add('hour', 'Please choose a time slot in the future.');
                    return;
                }

                // the time slot is already booked
                $hairdresserId = (int) $this->input('hairdresser_id');

                $exists = Booking::where('hairdresser_id', $hairdresserId)
                    ->where('scheduled_at', $scheduledAt)
                    ->exists();

                if ($exists) {
                    $validator->errors()->add('hour', 'This time slot is already booked. Please choose another time.');
                }
            }
        });
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Please enter your name.',
            'email.required' => 'Please enter your email address.',
            'email.email' => 'Please enter a valid email address.',
            'date.required' => 'Please select a date.',
            'date.after_or_equal' => 'Please select a date from today onwards.',
            'hour.required' => 'Please select a time.',
            'hour.date_format' => 'Please select a valid time in HH:MM format.',
            'hairdresser_id.required' => 'Please select a hairdresser.',
            'hairdresser_id.exists' => 'Please select a valid hairdresser.',
        ];
    }
}
