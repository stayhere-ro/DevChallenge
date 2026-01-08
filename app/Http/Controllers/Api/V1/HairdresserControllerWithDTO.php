<?php

namespace App\Http\Controllers\Api\V1;

use App\DTO\HairdresserInDTO;
use App\Http\Controllers\Controller;
use App\Http\Service\Interface\HairdresserServiceInterface;
use Illuminate\Http\JsonResponse;

class HairdresserControllerWithDTO extends Controller
{
    private HairdresserServiceInterface $hairdresserService;
    public function __construct(HairdresserServiceInterface $hairdresserService)
    {
        $this->hairdresserService = $hairdresserService;
    }
    public function store(HairdresserInDTO $hairdresserInDTO):JsonResponse{
         $this->hairdresserService->create($hairdresserInDTO);
         return response()->json([
             'message' => 'Hairdresser created successfully']);

    }


}
