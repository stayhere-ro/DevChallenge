<?php

namespace App\Http\Requests;

use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

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
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'date' => 'required|date|after_or_equal:today',
            'hour' => 'required|date_format:H:i',
            'hairdresser_id' => 'required|exists:hairdressers,id',
        ];
    }
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {

            $date = $this->input('date');
            $hour = $this->input('hour');
            $hairdresserId = $this->input('hairdresser_id');

            if (!$date || !$hour || !$hairdresserId) {
                return;
            }
            if (Carbon::parse($date)->isWeekend()) {
                $validator->errors()->add(
                    'date',
                    'Bookings are not available on weekends.'
                );
            }
            $time = Carbon::createFromFormat('H:i', $hour);

            if ($time->hour < 8 || $time->hour >= 17) {
                $validator->errors()->add(
                    'hour',
                    'Bookings are only available between 08:00 and 17:00.'
                );
            }
            $exists = Booking::where('date', $date)
                ->where('time', $hour)
                ->whereHas('hairdresser', function ($q) use ($hairdresserId) {
                    $q->where('hairdresser_id', $hairdresserId);
                })
                ->exists();
            if ($exists) {
                $validator->errors()->add(
                    'hour',
                    'This time slot is already booked for this hairdresser.'
                );
            }
        });
    }

}
