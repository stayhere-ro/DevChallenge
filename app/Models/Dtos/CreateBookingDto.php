<?php

namespace App\Models\Dtos;

use Carbon\Carbon;

class CreateBookingDto
{
    public function __construct(
        public string $name,
        public string $email,
        public int $hairdresserId,
        public Carbon $scheduledAt,
    ) {}

    public static function fromArray(array $data, Carbon $scheduledAt): self
    {
        return new self(
            name: $data['name'],
            email: $data['email'],
            hairdresserId: (int) $data['hairdresser_id'],
            scheduledAt: $scheduledAt,
        );
    }
}
