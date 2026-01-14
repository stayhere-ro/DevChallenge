<?php

namespace App\Http\Repository\RepositoryImpl\EloqImpl;

use App\DTO\HairdresserInDTO;
use App\DTO\HairdresserOutDTO;
use App\Http\Repository\Interface\HairdresserRepositoryInterface;
use App\Models\Hairdresser;

class HairdresserRepositoryWithDTO implements HairdresserRepositoryInterface
{

    public function create(HairdresserInDTO $hairdresserDTO): HairdresserOutDTO
    {
        return HairdresserOutDTO::from(Hairdresser::create($hairdresserDTO->toArray()));
    }
}
