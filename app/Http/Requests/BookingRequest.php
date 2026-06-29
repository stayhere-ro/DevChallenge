<?php

namespace App\Http\Requests;

use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

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
            'email' => 'required|email|max:255',
            'hairdresser_id' => 'required|exists:hairdressers,id',
            'date' => 'required|date|after_or_equal:today',
            'hour' => 'required|date_format:H:i',
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
            $hairdresserId = $this->input('hairdresser_id');

            if ($validator->errors()->has('date') || $validator->errors()->has('hour')) {
                return;
            }

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
                $scheduledAt = Carbon::parse($date.' '.$hour.':00');
                $exists = $hairdresserId && Booking::where('hairdresser_id', $hairdresserId)
                    ->where('scheduled_at', $scheduledAt)
                    ->exists();

                if ($exists) {
                    $validator->errors()->add('hour', 'This time slot is already booked for the selected hairdresser. Please choose another time.');
                }
            }
        });
    }

    public function scheduledAt(): Carbon
    {
        return Carbon::parse($this->input('date').' '.$this->input('hour').':00');
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
            'hairdresser_id.required' => 'Please select a hairdresser.',
            'hairdresser_id.exists' => 'Please select a valid hairdresser.',
            'date.required' => 'Please select a date.',
            'date.after_or_equal' => 'Please select a date from today onwards.',
            'hour.required' => 'Please select a time.',
            'hour.date_format' => 'Please select a valid time in HH:MM format.',
        ];
    }
}
