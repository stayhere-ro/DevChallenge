<?php

namespace App\Services;

use App\DTOs\CreateBookingData;
use App\Exceptions\SlotAlreadyBookedException;
use App\Models\Booking;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class BookingService
{
    public function create(CreateBookingData $data): Booking
    {
        try {
            return DB::transaction(function () use ($data) {
                $this->ensureSlotIsAvailable($data);

                return Booking::create([
                    'name' => $data->clientEmail,
                    'email' => $data->clientEmail,
                    'hairdresser_id' => $data->hairdresserId,
                    'scheduled_at' => $data->scheduledAt,
                ]);
            });
        } catch (QueryException $exception) {
            if ($this->isUniqueConstraintViolation($exception)) {
                throw new SlotAlreadyBookedException();
            }

            throw $exception;
        }
    }

    public function ensureSlotIsAvailable(CreateBookingData $data): void
    {
        $exists = Booking::where('hairdresser_id', $data->hairdresserId)
            ->where('scheduled_at', $data->scheduledAt)
            ->exists();

        if ($exists) {
            throw new SlotAlreadyBookedException();
        }
    }

    private function isUniqueConstraintViolation(QueryException $exception): bool
    {
        return $exception->getCode() === '23000';
    }
}
