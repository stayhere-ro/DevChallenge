<?php

namespace App\Services\Booking;

use App\Models\Booking;
use App\Models\Hairdresser;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class BookingAvailabilityService
{
    public function __construct(
        private readonly BookingAvailabilityChecker $checker,
    ) {}

    /**
     * @return array<string, list<array{hour: string}>>
     */
    public function slotsForWeek(int $hairdresserId, Carbon $weekStart, Carbon $weekEnd): array
    {
        $cacheKey = sprintf(
            'availability:%d:%s:%s',
            $hairdresserId,
            $weekStart->toDateString(),
            $weekEnd->toDateString()
        );

        $ttl = config('booking.availability_cache_ttl_seconds', 45);

        return Cache::remember($cacheKey, $ttl, function () use ($hairdresserId, $weekStart, $weekEnd) {
            return $this->buildSlotsForWeek($hairdresserId, $weekStart, $weekEnd);
        });
    }

    public function forgetWeekCache(int $hairdresserId, Carbon $scheduledAt): void
    {
        $weekStart = $scheduledAt->copy()->startOfWeek(Carbon::MONDAY);
        $weekEnd = $weekStart->copy()->addDays(6);

        Cache::forget(sprintf(
            'availability:%d:%s:%s',
            $hairdresserId,
            $weekStart->toDateString(),
            $weekEnd->toDateString()
        ));
    }

    /**
     * @return array<string, list<array{hour: string}>>
     */
    private function buildSlotsForWeek(int $hairdresserId, Carbon $weekStart, Carbon $weekEnd): array
    {
        $hairdresser = Hairdresser::active()->find($hairdresserId);

        if (! $hairdresser) {
            return [];
        }

        $period = CarbonPeriod::create($weekStart->copy()->startOfDay(), '1 day', $weekEnd->copy()->startOfDay());

        $bookedTimes = Booking::query()
            ->where('hairdresser_id', $hairdresserId)
            ->whereBetween('scheduled_at', [
                $weekStart->copy()->startOfDay(),
                $weekEnd->copy()->endOfDay(),
            ])
            ->pluck('scheduled_at')
            ->map(fn ($dt) => Carbon::parse($dt)->format('Y-m-d H:i'))
            ->flip();

        $slots = [];

        foreach ($period as $day) {
            $dateKey = $day->toDateString();
            $slots[$dateKey] = [];

            if ($day->isWeekend()) {
                continue;
            }

            for ($hour = config('booking.business_hours.start'); $hour < config('booking.business_hours.end'); $hour++) {
                $slot = $day->copy()->setTime($hour, 0);

                if ($slot->isPast()) {
                    continue;
                }

                try {
                    $this->checker->assertBookable($hairdresserId, Carbon::instance($slot), $bookedTimes->has($slot->format('Y-m-d H:i')));
                    $slots[$dateKey][] = ['hour' => $slot->format('H:i')];
                } catch (\App\Exceptions\BookingUnavailableException) {
                    continue;
                }
            }
        }

        return $slots;
    }

    /**
     * @return Collection<int, Carbon>
     */
    public function occupiedSlotsForDate(int $hairdresserId, Carbon $date): Collection
    {
        return Booking::query()
            ->where('hairdresser_id', $hairdresserId)
            ->whereDate('scheduled_at', $date->toDateString())
            ->orderBy('scheduled_at')
            ->pluck('scheduled_at')
            ->map(fn ($value) => Carbon::parse($value));
    }
}
