<?php

namespace Tests\Feature;

use App\Exceptions\BookingSlotNotAvailableException;
use App\Models\Dtos\CreateBookingDto;
use App\Models\Hairdresser;
use App\Services\BookingService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_throws_an_exception_when_the_slot_is_already_booked(): void
    {
        $hairdresser = Hairdresser::factory()->create();
        $scheduledAt = Carbon::parse('2026-07-06 10:00:00');
        $bookingService = app(BookingService::class);
        $bookingData = new CreateBookingDto(
            name: 'Gergo',
            email: 'gergo@gmail.com',
            hairdresserId: $hairdresser->id,
            scheduledAt: $scheduledAt,
        );

        $bookingService->create($bookingData);

        $this->expectException(BookingSlotNotAvailableException::class);

        $bookingService->create($bookingData);
    }
}
