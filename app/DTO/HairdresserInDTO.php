<?php

namespace App\DTO;

use Spatie\LaravelData\Data;

class HairdresserInDTO extends Data
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password
    ){}


}
