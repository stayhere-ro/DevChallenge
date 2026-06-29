<?php

namespace App\Http\Requests\Api;

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
     * Validation rules for POST /api/bookings.
     */
    public function rules(): array
    {
        return [
            'client_email' => 'required|email|max:255',
            'hairdresser_id' => 'required|integer|min:1',
            'date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
        ];
    }

    /**
     * Business rule validation.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $date = $this->input('date');
            $startTime = $this->input('start_time');

            if (!$date || !$startTime) {
                return;
            }

            $bookingDate = Carbon::parse($date);

            if ($bookingDate->isWeekend()) {
                $validator->errors()->add(
                    'date',
                    'Bookings are not available on weekends.'
                );
            }

            $bookingTime = Carbon::createFromFormat('H:i', $startTime);

            if ($bookingTime->hour < 8 || $bookingTime->hour >= 17) {
                $validator->errors()->add(
                    'start_time',
                    'Bookings are only available between 8:00 AM and 5:00 PM.'
                );
            }

            if ((int) $bookingTime->minute !== 0) {
                $validator->errors()->add(
                    'start_time',
                    'Bookings are only available on full-hour time slots.'
                );
            }
        });
    }

    /**
     * Custom validation messages.
     */
    public function messages(): array
    {
        return [
            'client_email.required' => 'Please provide the client email.',
            'client_email.email' => 'Please provide a valid client email.',
            'client_email.max' => 'The client email may not be greater than 255 characters.',

            'hairdresser_id.required' => 'Please provide the hairdresser ID.',
            'hairdresser_id.integer' => 'The hairdresser ID must be a valid integer.',
            'hairdresser_id.min' => 'The hairdresser ID must be greater than zero.',

            'date.required' => 'Please provide a booking date.',
            'date.date' => 'Please provide a valid booking date.',
            'date.after_or_equal' => 'The booking date must be today or a future date.',

            'start_time.required' => 'Please provide a booking start time.',
            'start_time.date_format' => 'The start time must use the HH:MM format.',
        ];
    }

    /**
     * Return consistent JSON validation errors.
     */
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Validation failed.',
            'errors' => $validator->errors(),
        ], 422));
    }
}
