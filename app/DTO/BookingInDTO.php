<?php

namespace App\DTO;
use Spatie\LaravelData\Data;
class BookingInDTO extends Data{

    public function __construct(
        public string $date,
        public string $hour,
        public int $hairdresser_id

    ){}





}
