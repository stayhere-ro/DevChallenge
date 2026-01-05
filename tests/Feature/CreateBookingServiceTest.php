<?php

namespace Tests\Unit;

use App\DTO\CreateBookingData;
use App\Exceptions\BookingValidationException;
use App\Models\Booking;
use App\Models\User;
use App\Services\CreateBookingService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateBookingServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow(Carbon::create(2026, 1, 5, 9, 0, 0, config('app.timezone')));
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    /**
     * Test a successful booking via service.
     */
    public function test_it_creates_a_booking_for_a_valid_slot(): void
    {
        $hairdresser = User::factory()
            ->create(['role' => 'hairdresser']);

        $dto = new CreateBookingData(
            clientName: 'Client Name',
            clientEmail: 'client@example.com',
            hairdresserId: $hairdresser->id,
            scheduledAt: Carbon::create(2026, 1, 5, 10, 0, 0, config('app.timezone'))
        );

        $service = new CreateBookingService();
        $booking = $service->create($dto, config('app.timezone'));

        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'email' => 'client@example.com',
            'hairdresser_id' => $hairdresser->id,
            'scheduled_at' => '2026-01-05 10:00:00',
        ]);
    }

    /**
     * Test a failed booking via service.
     */
    public function test_it_rejects_a_conflicting_booking_for_the_same_hairdresser(): void
    {
        $hairdresser = User::factory()
            ->create(['role' => 'hairdresser']);

        Booking::create([
            'name' => null,
            'email' => 'existing@example.com',
            'hairdresser_id' => $hairdresser->id,
            'scheduled_at' => Carbon::create(2026, 1, 5, 10, 0, 0, config('app.timezone')),
        ]);

        $dto = new CreateBookingData(
            clientName: 'Client Name',
            clientEmail: 'client@example.com',
            hairdresserId: $hairdresser->id,
            scheduledAt: Carbon::create(2026, 1, 5, 10, 0, 0, config('app.timezone'))
        );
        $service = new CreateBookingService();

        try {
            $service->create($dto, config('app.timezone'));
            $this->fail('Expected BookingValidationException');
        } catch (BookingValidationException $error) {
            $this->assertArrayHasKey('start_time', $error->errors);
            $this->assertEquals('This time slot is already booked. Please choose another time.', $error->errors['start_time'][0]);
        }
    }
}
