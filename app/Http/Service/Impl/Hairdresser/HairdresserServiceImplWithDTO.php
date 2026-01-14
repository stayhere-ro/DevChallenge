<?php

namespace App\Http\Service\Impl\Hairdresser;

use App\DTO\HairdresserInDTO;

use App\Http\Repository\Interface\HairdresserRepositoryInterface;
use App\DTO\HairdresserOutDTO;
use App\Http\Service\Interface\HairdresserServiceInterface;

class HairdresserServiceImplWithDTO implements HairdresserServiceInterface
{
    private HairdresserRepositoryInterface $hairdresserRepository;
    public function __construct(HairdresserRepositoryInterface $hairdresserRepository)
    {
        $this->hairdresserRepository = $hairdresserRepository;
    }


    public function create(HairdresserInDTO $hairdresserDTO): HairdresserOutDTO
    {
        return $this->hairdresserRepository->create($hairdresserDTO);
    }
}
