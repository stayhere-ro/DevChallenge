<?php

namespace App\Http\Requests\Api;

use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreBookingRequest extends FormRequest
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
            'date' => 'required|date_format:Y-m-d|after_or_equal:today',
            'start_time' => 'bail|required|date_format:H:i',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $date = $this->input('date');
            $time = $this->input('start_time');

            if (!$date || !$time) {
                return;
            }

            $hasBlockingErrors = fn () =>
                $validator->errors()->hasAny(['start_time', 'date']);
            //  Don't run business rules if date/time format/required failed
            if ($hasBlockingErrors()) return;

            // No weekends
            $carbonDate = Carbon::createFromFormat('Y-m-d', $date, config('app.timezone'));
            if ($carbonDate->isWeekend()) {
                $validator->errors()->add('date', 'Bookings are not available on weekends.');
            }

            $hourTime = Carbon::createFromFormat('H:i', $time);
            // Exactly on the hour (must be exactly HH:00)
            if ((int) $hourTime->format('i') !== 0) {
                $validator->errors()->add('start_time', 'Bookings must start exactly on the hour (e.g., 10:00).');
            }

            // Business hours (08:00–17:00, last start < 17:00)
            if ($hourTime->hour < 8 || $hourTime->hour >= 17) {
                $validator->errors()->add('start_time', 'Bookings are only available between 08:00 and 17:00 (last start before 17:00).');
            }
            // If any business rule failed, don't continue with future-slot or DB checks.
            if ($hasBlockingErrors()) return;

            $scheduledAt = Carbon::createFromFormat('Y-m-d H:i:s', "{$date} {$time}:00", config('app.timezone'));
            // Check if the time slot is in the past for a valid date
            if ($scheduledAt->lte(now())) {
                $validator->errors()->add('start_time', 'Please choose a time slot in the future.');
            }

            // Avoid DB query unless date/time are fully valid.
            if ($hasBlockingErrors()) return;

            // Check if the time slot is already booked
            $exists = Booking::where('hairdresser_id', $this->input('hairdresser_id'))
                ->where('scheduled_at', $scheduledAt)
                ->exists();

            if ($exists) {
                $validator->errors()->add('start_time', 'This time slot is already booked. Please choose another time.');
            }
        });
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Validation failed.',
            'errors' => $validator->errors(),
        ], 422));
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'client_email.required' => 'Please provide an email address.',
            'client_email.email' => 'Please provide a valid email address.',
            'hairdresser_id.required' => 'Please select a hairdresser.',
            'hairdresser_id.exists' => 'The provided hairdresser does not exist.',
            'date.required' => 'Please provide a date.',
            'date.date_format' => 'Please provide a valid date in YYYY-MM-DD format.',
            'date.after_or_equal' => 'Please provide a date from today onwards.',
            'start_time.required' => 'Please provide a start time.',
            'start_time.date_format' => 'Please provide a valid time in HH:MM format.',
        ];
    }
}
