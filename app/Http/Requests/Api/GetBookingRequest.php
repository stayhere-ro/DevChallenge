<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\String\TruncateMode;

class GetBookingRequest extends FormRequest
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
            'email' => 'required|email|exists:bookings,email'
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Email is required to retrieve bookings.',
            'email.email' => 'Please enter a valid email address.',
            'email.exists' => 'No bookings found for the provided email address.',
        ];
    }
}
