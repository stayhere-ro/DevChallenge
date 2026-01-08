<?php

namespace App\DTO;

use Spatie\LaravelData\Data;

class HairdresserOutDTO extends Data
{
    public function __construct(
        public int $id,
        public string $name,
        public string $email,
        public string $password

    ) {}

}
