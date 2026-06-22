<?php

namespace App\DTO;

use Carbon\Carbon;

final class CreateBookingData
{
    public function __construct(
        public readonly ?string $clientName,
        public readonly string $clientEmail,
        public readonly int $hairdresserId,
        public readonly Carbon $scheduledAt,
    ) {}

    public static function fromWeb(array $validated, string $timezone): self
    {
        $scheduledAt = Carbon::createFromFormat(
            'Y-m-d H:i',
            "{$validated['date']} {$validated['hour']}",
            $timezone
        );

        return new self(
            clientName: $validated['name'],
            clientEmail: $validated['email'],
            hairdresserId: (int) $validated['hairdresser_id'],
            scheduledAt: $scheduledAt,
        );
    }

    public static function fromApi(array $validated, string $timezone): self
    {
        $scheduledAt = Carbon::createFromFormat(
            'Y-m-d H:i',
            "{$validated['date']} {$validated['start_time']}",
            $timezone
        );

        return new self(
            clientName: $validated['client_name'] ?? null,
            clientEmail: $validated['client_email'],
            hairdresserId: (int) $validated['hairdresser_id'],
            scheduledAt: $scheduledAt,
        );
    }
}
