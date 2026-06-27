<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BookingExportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function __invoke(Request $request): StreamedResponse
    {
        $validated = $request->validate([
            'from' => ['required', 'date'],
            'to' => ['required', 'date', 'after_or_equal:from'],
        ]);

        $from = Carbon::parse($validated['from'])->startOfDay();
        $to = Carbon::parse($validated['to'])->endOfDay();

        $filename = sprintf('bookings-%s_%s.csv', $from->toDateString(), $to->toDateString());

        return response()->streamDownload(function () use ($from, $to) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['id', 'hairdresser', 'client_name', 'email', 'date', 'time', 'created_at']);

            Booking::query()
                ->with('hairdresser')
                ->whereBetween('scheduled_at', [$from, $to])
                ->orderBy('scheduled_at')
                ->chunk(200, function ($bookings) use ($handle) {
                    foreach ($bookings as $booking) {
                        fputcsv($handle, [
                            $booking->id,
                            $booking->hairdresser?->name,
                            $booking->name,
                            $booking->email,
                            $booking->scheduled_at->toDateString(),
                            $booking->scheduled_at->format('H:i'),
                            $booking->created_at?->toDateTimeString(),
                        ]);
                    }
                });

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }
}
