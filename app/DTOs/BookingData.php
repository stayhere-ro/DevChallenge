<?php

namespace App\DTOs;

use Illuminate\Support\Carbon;

class BookingData
{
    public function __construct(
        public readonly int $hairdresser_id,
        public readonly Carbon $scheduled_at,
        public readonly string $name,
        public readonly string $email,
        public readonly ?int $user_id = null,
    ) {}


    public static function fromRequest(array $validated, ?int $userId = null): self
    {
        return new self(
            hairdresser_id: $validated['hairdresser_id'],
            scheduled_at: Carbon::parse($validated['date'] . ' ' . $validated['hour']),
            name: $validated['name'],
            email: $validated['email'],
            user_id: $userId
        );
    }
}
