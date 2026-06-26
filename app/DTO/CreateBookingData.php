<?php

namespace App\DTO;

use Carbon\Carbon;

final class CreateBookingData
{
    public function __construct(
        public readonly int $hairdresserId,
        public readonly string $clientName,
        public readonly string $clientEmail,
        public readonly Carbon $scheduledAt,
    ) {}

    public static function fromApiPayload(array $validated): self
    {
        $scheduledAt = Carbon::parse(
            $validated['date'].' '.$validated['start_time'].':00'
        );

        return new self(
            hairdresserId: (int) $validated['hairdresser_id'],
            clientName: $validated['name'] ?? $validated['client_name'] ?? 'Guest',
            clientEmail: $validated['email'],
            scheduledAt: $scheduledAt,
        );
    }

    public static function fromWebPayload(array $validated, int $defaultHairdresserId): self
    {
        $scheduledAt = Carbon::parse(
            $validated['date'].' '.$validated['hour'].':00'
        );

        return new self(
            hairdresserId: (int) ($validated['hairdresser_id'] ?? $defaultHairdresserId),
            clientName: $validated['name'],
            clientEmail: $validated['email'],
            scheduledAt: $scheduledAt,
        );
    }
}
