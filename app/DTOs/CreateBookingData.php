<?php

namespace App\DTOs;

use Carbon\CarbonImmutable;

class CreateBookingData
{
    public function __construct(
        public readonly string $clientEmail,
        public readonly int $hairdresserId,
        public readonly CarbonImmutable $scheduledAt,
    ) {
    }

    public static function fromApiPayload(array $data): self
    {
        return new self(
            clientEmail: $data['client_email'],
            hairdresserId: (int) $data['hairdresser_id'],
            scheduledAt: CarbonImmutable::parse($data['date'] . ' ' . $data['start_time'] . ':00'),
        );
    }
}
