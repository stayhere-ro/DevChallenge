<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Http\Requests\BookingRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    /**
     * Display the booking form.
     */
    public function index(Request $request)
    {
        $email = $request->query('email');
        if (!$email) {
            return response()->json(['error' => 'Email parameter is required'], 400);
        }

        $user = User::where('email', $email)->first();
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $bookings = Booking::where('user_id', $user->id)
            ->orderBy('scheduled_at')
            ->get();

        return response()->json($bookings);
    }

    /**
     * Store a new booking.
     */
    public function store(BookingRequest $request)
    {
        $data = $request->validated();

        $scheduledAt = Carbon::parse($data['date'] . ' ' . $data['hour'] . ':00');

        Booking::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'scheduled_at' => $scheduledAt,
        ]);

        return redirect()->route('bookings.index')
            ->with('success', 'Booking confirmed! We look forward to seeing you.');
    }

    public function create()
    {
        return view('bookings.index');
    }
}
