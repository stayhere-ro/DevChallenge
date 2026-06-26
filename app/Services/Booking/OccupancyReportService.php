<?php

namespace App\Services\Booking;

use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class OccupancyReportService
{
    /**
     * @return Collection<int, array{hairdresser_id: int, hairdresser_name: string, date: string, booked_slots: int, capacity_slots: int, occupancy_rate: float}>
     */
    public function daily(Carbon $from, Carbon $to): Collection
    {
        $rows = collect();
        $period = $from->copy()->startOfDay();

        while ($period->lte($to->copy()->startOfDay())) {
            if ($period->isWeekend()) {
                $period->addDay();

                continue;
            }

            $capacity = max(0, config('booking.business_hours.end') - config('booking.business_hours.start'));

            $counts = Booking::query()
                ->selectRaw('hairdresser_id, COUNT(*) as total')
                ->whereDate('scheduled_at', $period->toDateString())
                ->groupBy('hairdresser_id')
                ->get();

            $hairdressers = \App\Models\Hairdresser::whereIn('id', $counts->pluck('hairdresser_id'))->get()->keyBy('id');

            foreach ($counts as $count) {
                $hairdresser = $hairdressers->get($count->hairdresser_id);
                $booked = (int) $count->getAttribute('total');
                $rows->push([
                    'hairdresser_id' => (int) $count->hairdresser_id,
                    'hairdresser_name' => $hairdresser?->name ?? 'Unknown',
                    'date' => $period->toDateString(),
                    'booked_slots' => $booked,
                    'capacity_slots' => $capacity,
                    'occupancy_rate' => $capacity > 0 ? round($booked / $capacity, 4) : 0.0,
                ]);
            }

            $period->addDay();
        }

        return $rows;
    }
}
