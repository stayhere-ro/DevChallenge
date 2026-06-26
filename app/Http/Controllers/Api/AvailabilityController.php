<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Booking\BookingAvailabilityService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AvailabilityController extends Controller
{
    public function __construct(
        private readonly BookingAvailabilityService $availability,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'hairdresser_id' => ['required', 'integer', 'exists:hairdressers,id'],
            'week_start' => ['required', 'date'],
            'week_end' => ['required', 'date', 'after_or_equal:week_start'],
        ]);

        $weekStart = Carbon::parse($validated['week_start'])->startOfDay();
        $weekEnd = Carbon::parse($validated['week_end'])->startOfDay();

        return response()->json([
            'hairdresser_id' => (int) $validated['hairdresser_id'],
            'slots' => $this->availability->slotsForWeek(
                (int) $validated['hairdresser_id'],
                $weekStart,
                $weekEnd,
            ),
        ]);
    }
}
