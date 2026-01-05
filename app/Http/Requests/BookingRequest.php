<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\User;
use Illuminate\Validation\Rule;

class BookingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return !auth()->check() || !auth()->user()->isHairdresser();
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
