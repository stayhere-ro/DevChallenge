<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(): View
    {
        $bookings = Booking::query()->with('hairdresser')->orderByDesc('scheduled_at')->paginate(15);

        return view('admin.dashboard', compact('bookings'));
    }
}
