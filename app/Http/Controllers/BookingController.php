<?php

namespace App\Http\Controllers;

use App\DTO\CreateBookingData;
use App\Exceptions\BookingConflictException;
use App\Http\Requests\BookingRequest;
use App\Models\Hairdresser;
use App\Services\Booking\BookingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function __construct(
        private readonly BookingService $bookingService,
    ) {}

    public function index(): View
    {
        return view('bookings.index', [
            'hairdressers' => Hairdresser::active()->orderBy('name')->get(),
        ]);
    }

    public function store(BookingRequest $request): RedirectResponse
    {
        $defaultHairdresserId = Hairdresser::active()->orderBy('id')->value('id');

        try {
            $this->bookingService->create(
                CreateBookingData::fromWebPayload($request->validated(), (int) $defaultHairdresserId)
            );
        } catch (ValidationException $e) {
            return back()->withInput()->withErrors($e->errors());
        } catch (BookingConflictException) {
            return back()
                ->withInput()
                ->withErrors(['hour' => 'This time slot is already booked. Please choose another time.']);
        }

        return redirect()
            ->route('bookings.index')
            ->with('success', 'Booking confirmed! We look forward to seeing you.');
    }
}
