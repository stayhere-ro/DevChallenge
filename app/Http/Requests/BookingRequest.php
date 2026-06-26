<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'date' => ['required', 'date', 'after_or_equal:today'],
            'hour' => ['required', 'date_format:H:i'],
            'hairdresser_id' => ['sometimes', 'integer', 'exists:hairdressers,id'],
        ];
    }

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
