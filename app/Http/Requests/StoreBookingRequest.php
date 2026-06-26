<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'max:255'],
            'hairdresser_id' => ['required', 'integer', 'exists:hairdressers,id'],
            'date' => ['required', 'date', 'after_or_equal:today'],
            'start_time' => ['required', 'date_format:H:i'],
            'name' => ['sometimes', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Client email is required.',
            'hairdresser_id.required' => 'Hairdresser is required.',
            'date.after_or_equal' => 'Date must be today or in the future.',
            'start_time.date_format' => 'Start time must be in HH:MM format.',
        ];
    }
}
