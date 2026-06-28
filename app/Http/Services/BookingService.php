<?php

namespace App\Http\Services;

use App\Models\Booking;
use App\Http\Interfaces\BookingServiceInterface;

class BookingService implements BookingServiceInterface
{
    public function insert(array $reservation)
    {
        Booking::create($reservation);
    }
}
