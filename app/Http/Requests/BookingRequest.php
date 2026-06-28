<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\CheckWeekend;
use App\Rules\CheckBusinessHours;
use App\Rules\CheckHairdresserAndScheduledHours;

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
            'date' => [
                'required',
                'date',
                'after_or_equal:today',
                new CheckWeekend(),
            ],
            'hour' => [
                'required',
                'date_format:H:i',
                new CheckBusinessHours(),
                new CheckHairdresserAndScheduledHours(date:$this->date),
            ],
        ];
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
        ];
    }
}
