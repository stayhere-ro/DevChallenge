<?php

namespace App\Services;

use App\Exceptions\BookingSlotNotAvailableException;
use App\Models\Booking;
use App\Models\Dtos\CreateBookingDto;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class BookingService
{
    public function create(CreateBookingDto $data): Booking
    {
        try {
            return DB::transaction(function () use ($data): Booking {
                return Booking::create([
                    'name' => $data->name,
                    'email' => $data->email,
                    'hairdresser_id' => $data->hairdresserId,
                    'scheduled_at' => $data->scheduledAt,
                ]);
            });
        } catch (QueryException $exception) {
            throw new BookingSlotNotAvailableException;
        }
    }
}
