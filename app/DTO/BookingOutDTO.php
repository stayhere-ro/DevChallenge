<?php
namespace App\DTO;
use Spatie\LaravelData\Data;

class BookingOutDTO extends Data{
    public function __construct(
        public int $id,
        public int $client_id,
        public string $date,
        public string $time,
    ){}

}
