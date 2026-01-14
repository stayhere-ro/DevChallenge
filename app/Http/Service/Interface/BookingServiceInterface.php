<?php

namespace App\Http\Service\Interface;

use App\DTO\BookingInDTO;
use App\DTO\UserInDTO;

interface BookingServiceInterface
{
    public function create(BookingInDTO $bookingDTO,UserInDTO $userInDTO);

}
