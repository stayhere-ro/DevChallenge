<?php

namespace App\Http\Controllers;

use App\Services\BookingNotificationService;
use App\Models\Booking;
use App\Models\User;
use App\Http\Requests\BookingRequest;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Validation\Rule;

class BookingController extends Controller
{
    /**
     * Display the booking form.
     */
    public function index(Request $request)
    {
        if (auth()->check() && auth()->user()->isHairdresser()) {
            return redirect()->route('admin.dashboard');
        }

        $myBookings = null;
        $hairdressers = User::query()
            ->where('role', 'hairdresser')
            ->orderBy('name')
            ->get(['id', 'name']);

        if (auth()->check() && auth()->user()->isClient()) {
            $query = Booking::query()
                ->where('email', auth()->user()->email)
                ->orderByDesc('scheduled_at');

            if ($request->filled('from')) {
                $query->whereDate('scheduled_at', '>=', $request->query('from'));
            }

            if ($request->filled('to')) {
                $query->whereDate('scheduled_at', '<=', $request->query('to'));
            }

            if ($request->filled('filter_hour')) {
                $query->whereTime('scheduled_at', '=', $request->query('filter_hour'));
            }

            $myBookings = $query->paginate(perPage: 15);
        }

        return view('bookings.index', compact('myBookings', 'hairdressers'));
    }

    /**
     * Get available hours for a selected hairdresser.
     */
    public function availability(Request $request)
    {
        $data = $request->validate([
            'hairdresser_id' => [
                'required',
                'integer',
                Rule::exists('users', 'id'),
            ],
            'date' => ['required', 'date_format:Y-m-d'],
        ]);

        $takenHours = Booking::query()
            ->where('hairdresser_id', $data['hairdresser_id'])
            ->whereDate('scheduled_at', $data['date'])
            ->orderBy('scheduled_at')
            ->get(['scheduled_at'])
            ->map(fn ($booking) => $booking->scheduled_at->format('H:i'))
            ->values();

        return response()->json([
            'taken_hours' => $takenHours,
        ]);
    }

    /**
     * Store a new booking.
     */
    public function store(BookingRequest $request, BookingNotificationService $notifier)
    {
        if (auth()->check() && auth()->user()->isHairdresser()) {
            abort(403);
        }

        $data = $request->validated();

        $scheduledAt = Carbon::parse($data['date'] . ' ' . $data['hour'] . ':00');

        $name = $data['name'];
        $email = $data['email'];

        if (auth()->check() && auth()->user()->isClient()) {
            $name = auth()->user()->name;
            $email = auth()->user()->email;
        }

        $booking = Booking::create([
            'name' => $name,
            'email' => $email,
            'scheduled_at' => $scheduledAt,
            'hairdresser_id' => $data['hairdresser_id'],
        ]);

        $notifier->sendForNewBooking($booking);

        return redirect()->route('bookings.index')
            ->with('success', 'Booking confirmed! We look forward to seeing you.');
    }
}
