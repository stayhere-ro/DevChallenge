<?php

namespace Tests\Unit\Services;

use App\Models\Booking;
use App\Models\Hairdresser;
use App\Services\Booking\OccupancyReportService;
use Carbon\Carbon;
use Tests\TestCase;

class OccupancyReportServiceTest extends TestCase
{
    public function test_calculates_daily_occupancy_rate(): void
    {
        $hairdresser = Hairdresser::factory()->create();
        $monday = Carbon::parse('next monday');

        Booking::factory()->forHairdresser($hairdresser)->create([
            'scheduled_at' => $monday->copy()->setTime(9, 0),
        ]);
        Booking::factory()->forHairdresser($hairdresser)->create([
            'scheduled_at' => $monday->copy()->setTime(10, 0),
        ]);

        $rows = app(OccupancyReportService::class)->daily($monday, $monday);

        $this->assertCount(1, $rows);
        $this->assertSame(2, $rows->first()['booked_slots']);
        $this->assertSame(9, $rows->first()['capacity_slots']);
    }
}
