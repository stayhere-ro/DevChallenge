<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ListBookingsRequest;
use App\Models\Booking;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(ListBookingsRequest $request): View
    {
        $sort = $request->sortColumn();
        $direction = $request->sortDirection();

        $bookings = Booking::query()
            ->with('hairdresser')
            ->search($request->search())
            ->when($sort === 'stylist', fn (Builder $query) => $query
                ->leftJoin('hairdressers', 'bookings.hairdresser_id', '=', 'hairdressers.id')
                ->select('bookings.*')
                ->orderBy('hairdressers.name', $direction))
            ->when($sort !== 'stylist', fn (Builder $query) => $query->orderBy(
                match ($sort) {
                    'id' => 'bookings.id',
                    'client' => 'bookings.name',
                    'email' => 'bookings.email',
                    'created_at' => 'bookings.created_at',
                    default => 'bookings.scheduled_at',
                },
                $direction
            ))
            ->paginate(15)
            ->withQueryString();

        return view('admin.dashboard', [
            'bookings' => $bookings,
            'search' => $request->input('search', ''),
            'sort' => $sort,
            'direction' => $direction,
        ]);
    }
}
