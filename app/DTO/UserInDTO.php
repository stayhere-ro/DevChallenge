<?php
namespace App\DTO;

use Spatie\LaravelData\Data;

class UserInDTO extends Data
{

    public function __construct(
        public string $name,
        public string $email,
        public string $password
    ){}




}
