<?php

namespace App\Http\Controllers\Api;

use App\DTO\CreateBookingData;
use App\Exceptions\BookingConflictException;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookingRequest;
use App\Http\Resources\BookingResource;
use App\Models\Booking;
use App\Services\Booking\BookingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class BookingController extends Controller
{
    public function __construct(
        private readonly BookingService $bookingService,
    ) {}

    public function store(StoreBookingRequest $request): JsonResponse
    {
        try {
            $booking = $this->bookingService->create(
                CreateBookingData::fromApiPayload($request->validated())
            );
        } catch (BookingConflictException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'error' => 'booking_conflict',
            ], 409);
        }

        return (new BookingResource($booking))
            ->response()
            ->setStatusCode(201);
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        $bookings = Booking::query()
            ->with('hairdresser')
            ->where('email', $request->user()->email)
            ->orderByDesc('scheduled_at')
            ->paginate(15);

        return BookingResource::collection($bookings);
    }
}
