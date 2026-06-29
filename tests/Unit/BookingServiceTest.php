<?php

namespace Tests\Unit;

use App\DTOs\CreateBookingData;
use App\Exceptions\SlotAlreadyBookedException;
use App\Models\Booking;
use App\Services\BookingService;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_a_booking(): void
    {
        $service = app(BookingService::class);

        $booking = $service->create(new CreateBookingData(
            clientEmail: 'client@example.com',
            hairdresserId: 1,
            scheduledAt: CarbonImmutable::parse('2026-07-01 10:00:00'),
        ));

        $this->assertInstanceOf(Booking::class, $booking);
        $this->assertSame('client@example.com', $booking->email);
        $this->assertSame(1, $booking->hairdresser_id);
        $this->assertSame('2026-07-01 10:00:00', $booking->scheduled_at->format('Y-m-d H:i:s'));
    }

    public function test_it_rejects_duplicate_booking_slots(): void
    {
        $service = app(BookingService::class);

        $firstBooking = new CreateBookingData(
            clientEmail: 'first@example.com',
            hairdresserId: 1,
            scheduledAt: CarbonImmutable::parse('2026-07-01 10:00:00'),
        );

        $secondBooking = new CreateBookingData(
            clientEmail: 'second@example.com',
            hairdresserId: 1,
            scheduledAt: CarbonImmutable::parse('2026-07-01 10:00:00'),
        );

        $service->create($firstBooking);

        $this->expectException(SlotAlreadyBookedException::class);

        $service->create($secondBooking);
    }

    public function test_it_allows_same_slot_for_different_hairdressers(): void
    {
        $service = app(BookingService::class);

        $service->create(new CreateBookingData(
            clientEmail: 'first@example.com',
            hairdresserId: 1,
            scheduledAt: CarbonImmutable::parse('2026-07-01 10:00:00'),
        ));

        $service->create(new CreateBookingData(
            clientEmail: 'second@example.com',
            hairdresserId: 2,
            scheduledAt: CarbonImmutable::parse('2026-07-01 10:00:00'),
        ));

        $this->assertSame(2, Booking::count());
    }
}
