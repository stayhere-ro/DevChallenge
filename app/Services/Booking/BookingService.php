<?php

namespace App\Services\Booking;

use App\DTO\CreateBookingData;
use App\Exceptions\BookingConflictException;
use App\Exceptions\BookingUnavailableException;
use App\Models\Booking;
use App\Models\Hairdresser;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BookingService
{
    public function __construct(
        private readonly BookingAvailabilityChecker $checker,
        private readonly BookingAvailabilityService $availability,
    ) {}

    public function create(CreateBookingData $data): Booking
    {
        $this->checker->assertHairdresserActive($data->hairdresserId);

        if ($this->slotIsTaken($data->hairdresserId, $data->scheduledAt)) {
            throw new BookingConflictException;
        }

        try {
            $this->checker->assertBookable($data->hairdresserId, $data->scheduledAt);
        } catch (BookingUnavailableException $e) {
            throw ValidationException::withMessages([
                'scheduled_at' => [$e->getMessage()],
            ]);
        }

        try {
            $booking = DB::transaction(function () use ($data) {
                Hairdresser::active()->whereKey($data->hairdresserId)->lockForUpdate()->first();

                if ($this->slotIsTaken($data->hairdresserId, $data->scheduledAt)) {
                    throw new BookingConflictException;
                }

                return Booking::create([
                    'hairdresser_id' => $data->hairdresserId,
                    'name' => $data->clientName,
                    'email' => $data->clientEmail,
                    'scheduled_at' => $data->scheduledAt,
                ]);
            });
        } catch (QueryException $e) {
            if ($this->isUniqueConstraintViolation($e)) {
                throw new BookingConflictException;
            }

            throw $e;
        }

        $this->availability->forgetWeekCache($data->hairdresserId, $data->scheduledAt);

        event(new \App\Events\BookingCreated($booking));

        return $booking->load('hairdresser');
    }

    private function slotIsTaken(int $hairdresserId, \Carbon\Carbon $scheduledAt): bool
    {
        return Booking::query()
            ->where('hairdresser_id', $hairdresserId)
            ->where('scheduled_at', $scheduledAt)
            ->exists();
    }

    private function isUniqueConstraintViolation(QueryException $e): bool
    {
        $sqlState = $e->errorInfo[0] ?? null;

        return in_array($sqlState, ['23000', '23505'], true);
    }
}
