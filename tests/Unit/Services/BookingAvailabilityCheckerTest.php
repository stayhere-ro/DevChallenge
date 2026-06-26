<?php

namespace Tests\Unit\Services;

use App\Models\Hairdresser;
use App\Services\Booking\BookingAvailabilityChecker;
use Carbon\Carbon;
use Tests\TestCase;

class BookingAvailabilityCheckerTest extends TestCase
{
    private BookingAvailabilityChecker $checker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->checker = app(BookingAvailabilityChecker::class);
    }

    public function test_rejects_weekend_slots(): void
    {
        $hairdresser = Hairdresser::factory()->create();
        $saturday = Carbon::parse('next saturday')->setTime(10, 0);

        $this->expectException(\App\Exceptions\BookingUnavailableException::class);
        $this->checker->assertBookable($hairdresser->id, $saturday);
    }

    public function test_rejects_outside_business_hours(): void
    {
        $hairdresser = Hairdresser::factory()->create();
        $slot = Carbon::parse('next monday')->setTime(7, 0);

        $this->expectException(\App\Exceptions\BookingUnavailableException::class);
        $this->checker->assertBookable($hairdresser->id, $slot);
    }

    public function test_accepts_valid_weekday_slot(): void
    {
        $hairdresser = Hairdresser::factory()->create();
        $slot = Carbon::parse('next monday')->setTime(10, 0);

        $this->checker->assertBookable($hairdresser->id, $slot);
        $this->assertTrue(true);
    }
}
