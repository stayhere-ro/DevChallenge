<?php

namespace App\Http\Controllers\Api\V1;

use App\DTO\HairdresserInDTO;
use App\Http\Controllers\Controller;
use App\Http\Service\Interface\HairdresserServiceInterface;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Tag(
 *     name="Hairdressers",
 *     description="API Endpoints for hairdressers (DTO-based)"
 * )
 */
class HairdresserControllerWithDTO extends Controller
{
    private HairdresserServiceInterface $hairdresserService;
    public function __construct(HairdresserServiceInterface $hairdresserService)
    {
        $this->hairdresserService = $hairdresserService;
    }
    /**
     * Create a new hairdresser (DTO)
     *
     * @OA\Post(
     *     path="/api/v1/hairdressers",
     *     tags={"Hairdressers"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(type="object",
     *             @OA\Property(property="name", type="string", example="Jane Stylist"),
     *             @OA\Property(property="email", type="string", example="jane@example.com")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Hairdresser created successfully"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function store(HairdresserInDTO $hairdresserInDTO):JsonResponse{
         $this->hairdresserService->create($hairdresserInDTO);
         return response()->json([
             'message' => 'Hairdresser created successfully']);

    }


}
