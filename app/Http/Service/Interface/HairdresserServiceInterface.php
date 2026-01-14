<?php

namespace App\Http\Service\Interface;

use App\DTO\HairdresserInDTO;
use App\DTO\HairdresserOutDTO;

interface HairdresserServiceInterface
{
    public function create(HairdresserInDTO $hairdresserDTO):HairdresserOutDTO;

}
