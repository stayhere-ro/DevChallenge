<?php

namespace App\Http\Controllers\Booking;

use App\DTOs\BookingData;
use App\Http\Controllers\Controller;
use App\Http\Requests\BookingRequest;
use App\Models\Booking;
use App\Models\User;
use App\Services\BookingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiBookingController extends Controller
{
    public function __construct(
        protected BookingService $bookingService,
    )
    {}

    /**
     * @OA\Get(
     *     path="/api/bookings",
     *     summary="List bookings by email",
     *     tags={"Bookings"},
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         description="Filter bookings by customer email",
     *         required=true,
     *         @OA\Schema(type="string", format="email", example="testuser@example.com")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 example={
     *                     "id": 6,
     *                     "hairdresser_id": 5,
     *                     "user_id": 1,
     *                     "name": "Test User",
     *                     "email": "testuser@example.com",
     *                     "scheduled_at": "2026-01-12T08:00:00.000000Z",
     *                     "created_at": "2026-01-04T19:28:45.000000Z",
     *                     "updated_at": "2026-01-04T19:28:45.000000Z"
     *                 }
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No bookings found",
     *    )
     * )
 */
    public function index(Request $request): JsonResponse
    {
        $email = $request->query('email');
        if (!$email) {
            return response()->json(['error' => 'Email parameter is required'], 400);
        }

        $bookings = $this->bookingService->getBookingByEmail($email);

        if ($bookings === null) {
            return response()->json(['error' => 'User not found'], 404);
        }

        return response()->json($bookings);
    }

    /**
     * @OA\Post(
     *     path="/api/bookings",
     *     summary="Create a new booking",
     *     tags={"Bookings"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"hairdresser_id","date","hour","name","email"},
     *             @OA\Property(property="hairdresser_id", type="integer", example=1),
     *             @OA\Property(property="date", type="string", format="date", example="2026-03-10"),
     *             @OA\Property(property="hour", type="string", example="13:00"),
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="johndoe@email.com"),
     *         )
     *     ),
     *
     *    @OA\Response(
     *        response=201,
     *        description="Booking Created",
     *        @OA\JsonContent(
     *            @OA\Property(property="message", type="string", example="Booking created successfully")
     *        )
     *  ),
     *
     *    @OA\Response(
     *          response=422,
     *          description="Validation Error",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="The given data was invalid."),
     *              @OA\Property(property="errors",
     *                           type="object",
     *                           example={
     *                                   "date": {"The date cannot be on weekends."},
     *                                   "hour": {"The selected time slot is already taken."}
     *                           }
     *              )
     *          )
     *      )
     * )
     */
    public function store(BookingRequest $request): JsonResponse
    {
        $data = BookingData::fromRequest($request->validated());

        $booking = $this->bookingService->createBooking($data);

        return response()->json([
            'message' => 'Booking confirmed! We look forward to seeing you.',
            'booking' => $booking
        ], 201);
    }
}
