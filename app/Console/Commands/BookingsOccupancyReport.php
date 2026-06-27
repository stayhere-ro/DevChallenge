<?php

namespace App\Console\Commands;

use App\Services\Booking\OccupancyReportService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class BookingsOccupancyReport extends Command
{
    protected $signature = 'bookings:occupancy {from} {to}';

    protected $description = 'Print daily occupancy per hairdresser for a date range (YYYY-MM-DD)';

    public function handle(OccupancyReportService $reports): int
    {
        $from = Carbon::parse($this->argument('from'))->startOfDay();
        $to = Carbon::parse($this->argument('to'))->startOfDay();

        $rows = $reports->daily($from, $to);

        if ($rows->isEmpty()) {
            $this->info('No occupancy data for the selected period.');

            return self::SUCCESS;
        }

        $this->table(
            ['Hairdresser', 'Date', 'Booked', 'Capacity', 'Rate'],
            $rows->map(fn (array $row) => [
                $row['hairdresser_name'],
                $row['date'],
                $row['booked_slots'],
                $row['capacity_slots'],
                number_format($row['occupancy_rate'] * 100, 1).'%',
            ])->all()
        );

        return self::SUCCESS;
    }
}
