<?php

namespace App\Http\Requests\Api;

use App\Models\User;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

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
            'client_email' => [
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
            'hairdresser_id' => [
                'required',
                'integer',
                Rule::exists('users', 'id')->where('role', 'hairdresser'),
            ],
            'date' => 'required|date_format:Y-m-d|after_or_equal:today',
            'start_time' => 'bail|required|date_format:H:i',
        ];
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

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Validation failed.',
            'errors' => $validator->errors(),
        ], 422));
    }
}
