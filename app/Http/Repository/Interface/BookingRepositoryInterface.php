<?php

namespace App\Http\Repository\Interface;

use App\DTO\BookingInDTO;
use App\DTO\BookingOutDTO;
use App\DTO\UserInDTO;

interface BookingRepositoryInterface
{
    public function create(BookingInDTO $bookingDTO,UserInDTO $userInDTO);

}
