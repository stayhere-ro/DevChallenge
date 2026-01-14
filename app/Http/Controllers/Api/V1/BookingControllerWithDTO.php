<?php

namespace App\Http\Controllers\Api\V1;

use App\DTO\BookingInDTO;
use App\DTO\UserInDTO;
use App\Http\Controllers\Controller;
use App\Http\Service\Interface\BookingServiceInterface;
use Illuminate\Http\JsonResponse;


/**
 * @OA\Tag(
 *     name="Bookings",
 *     description="API Endpoints for bookings (DTO-based)"
 * )
 */
class BookingControllerWithDTO extends Controller
{
    private BookingServiceInterface $bookingService;
    public function __construct(BookingServiceInterface $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    /**
     * Create a new booking (DTO)
     *
     * @OA\Post(
     *     path="/api/v1/bookings",
     *     operationId="createBooking",
     *     tags={"Bookings"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="date", type="string", example="2026-01-08"),
     *             @OA\Property(property="hour", type="string", example="14:00"),
     *             @OA\Property(property="hairdresser_id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", example="john@example.com")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Booking created successfully"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */


    public function store(BookingInDTO $bookingInDTO,UserInDTO $userInDTO):JsonResponse
    {

        $this->bookingService->create($bookingInDTO,$userInDTO);
        return response()->json([
            'message' => 'Booking created successfully'],status:201);

    }

}
