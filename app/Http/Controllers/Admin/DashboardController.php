<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;

class DashboardController extends Controller
{
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
        $bookings = Booking::with('hairdresser')
            ->orderBy('scheduled_at')
            ->paginate(15);

        return view('admin.dashboard', compact('bookings'));
    }
}
