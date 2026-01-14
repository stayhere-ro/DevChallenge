<?php

namespace App\Http\Repository\Interface;

use App\DTO\HairdresserInDTO;
use App\DTO\HairdresserOutDTO;
use App\DTO\UserInDTO;
use App\DTO\UserOutDTO;
use Illuminate\Support\Collection;

interface HairdresserRepositoryInterface
{
   public function create(HairdresserInDTO $hairdresserDTO):HairdresserOutDTO;


}
