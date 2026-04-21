<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Models\Booking;
use Carbon\Carbon;

class ApiBookingRequest extends FormRequest
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
            'client_email' => 'required|email|max:255',
            'hairdresser_id' => 'required|integer|exists:users,id',
            'date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $date = $this->input('date');
            $hour = $this->input('start_time');

            if (!$date || !$hour) return;

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


            // Combine into scheduled_at and check if the time slot is already booked for the given hairdresser
            $hairdresser_id = $this->input('hairdresser_id');
            $scheduledAt = Carbon::parse($date . ' ' . $hour . ':00');

            if (!$hairdresser_id) return;

            $exists = Booking::where('scheduled_at', $scheduledAt)
                ->where('hairdresser_id', $hairdresser_id)
                ->exists();

            if ($exists) {
                $validator->errors()->add('start_time', 'This time slot is already booked. Please choose another time.');
            }
        });
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'errors' => $validator->errors()
        ], 422));
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'client_email.required' => 'Please enter your email address.',
            'client_email.email' => 'Please enter a valid email address.',
            'hairdresser_id.required' => 'Please enter your hairdressers ID.',
            'date.required' => 'Please select a date.',
            'date.after_or_equal' => 'Please select a date from today onwards.',
            'start_time.required' => 'Please select a time.',
            'start_time.date_format' => 'Please select a valid time in HH:MM format.',
        ];
    }
}
