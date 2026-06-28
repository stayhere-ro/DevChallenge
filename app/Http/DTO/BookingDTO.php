<?php

namespace App\Http\DTO;

use Carbon\Carbon;

class BookingDTO
{
    public function __construct(
        public readonly int $hairdresser_id,
        public readonly string $name,
        public readonly string $email,
        public readonly string $date,
        public readonly string $hour
    ) {}

    public static function self(array $data): self
    {
        return new self(
            hairdresser_id: $data['hairdresser_id'],
            name: $data['name'] ?? '',
            email: $data['email'],
            date: $data['date'],
            hour: $data['hour'],
        );
    }

    public function toArray(): array
    {
        return [
            'hairdresser_id' => $this->hairdresser_id,
            'name' => $this->name,
            'email' => $this->email,
            'scheduled_at' => Carbon::parse($this->date . ' ' . $this->hour . ':00')->format('Y-m-d H:i:s'),
        ];
    }
}
