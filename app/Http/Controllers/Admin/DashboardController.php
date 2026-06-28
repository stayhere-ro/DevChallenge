<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    private const EXPORT_HEADERS = [
        'Booking ID',
        'Client Name',
        'Client Email',
        'Appointment Date',
        'Appointment Time',
        'Booked At',
    ];

    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display the dashboard with all bookings.
     */
    public function index()
    {
        $bookings = Booking::orderBy('scheduled_at')->where('hairdresser_id', auth()->user()->id)
            ->paginate(15);

        return view('admin.dashboard', compact('bookings'));
    }

    /**
     * Export bookings for the authenticated hairdresser in a date range.
     */
    public function export(Request $request)
    {
        [$from, $to] = $this->validatedExportRange($request);
        $filename = sprintf('bookings-%s-to-%s.csv', $from->toDateString(), $to->toDateString());

        $bookings = $this->bookingsForExport($from, $to);

        return response()->streamDownload(function () use ($bookings) {
            $output = fopen('php://output', 'w');

            if ($output === false) {
                return;
            }

            fputcsv($output, self::EXPORT_HEADERS);

            foreach ($bookings as $booking) {
                fputcsv($output, $this->bookingExportRow($booking));
            }

            fclose($output);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * @return array{0: Carbon, 1: Carbon}
     */
    private function validatedExportRange(Request $request): array
    {
        $data = $request->validate([
            'from' => ['required', 'date'],
            'to' => ['required', 'date', 'after_or_equal:from'],
        ]);

        return [
            Carbon::parse($data['from'])->startOfDay(),
            Carbon::parse($data['to'])->endOfDay(),
        ];
    }

    /**
     * @return Collection<int, Booking>
     */
    private function bookingsForExport(Carbon $from, Carbon $to): Collection
    {
        return Booking::query()
            ->where('hairdresser_id', auth()->id())
            ->whereBetween('scheduled_at', [$from, $to])
            ->orderBy('scheduled_at')
            ->get();
    }

    /**
     * @return array<int, string|int>
     */
    private function bookingExportRow(Booking $booking): array
    {
        return [
            $booking->id,
            $booking->name,
            $booking->email,
            $booking->scheduled_at->format('Y-m-d'),
            $booking->scheduled_at->format('H:i'),
            $booking->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
