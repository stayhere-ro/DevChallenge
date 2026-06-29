<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Hairdresser;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiBookingTest extends TestCase
{
    use RefreshDatabase;

    private const API_ENDPOINT = '/api/bookings';

    private const START_TIME = '10:00';

    private const SUCCESS_MESSAGE = 'Booking created successfully.';

    private const NAME_1 = 'Gergo';

    private const EMAIL_1 = 'gergo@gmail.com';

    private const NAME_2 = 'Lajos';

    private const EMAIL_2 = 'lajos@gmail.com';

    public function test_api_booking_can_be_created(): void
    {
        $hairdresser = Hairdresser::factory()->create();
        $date = $this->nextWeekday();
        $scheduledAt = $this->scheduledAt($date);
        $payload = [
            'name' => self::NAME_1,
            'email' => self::EMAIL_1,
            'hairdresser_id' => $hairdresser->id,
            'date' => $date,
            'hour' => self::START_TIME,
        ];

        $response = $this->postJson(self::API_ENDPOINT, $payload);

        $response->assertCreated()
            ->assertJsonPath('message', self::SUCCESS_MESSAGE)
            ->assertJsonPath('data.email', self::EMAIL_1)
            ->assertJsonPath('data.hairdresser_id', $hairdresser->id)
            ->assertJsonPath('data.scheduled_at', $scheduledAt);

        $this->assertDatabaseHas('bookings', [
            'email' => self::EMAIL_1,
            'hairdresser_id' => $hairdresser->id,
            'scheduled_at' => $scheduledAt,
        ]);
    }

    public function test_same_hairdresser_cannot_be_double_booked_for_same_slot(): void
    {
        $hairdresser = Hairdresser::factory()->create();
        $date = $this->nextWeekday();
        $scheduledAt = $this->scheduledAt($date);
        $payload = [
            'name' => self::NAME_2,
            'email' => self::EMAIL_2,
            'hairdresser_id' => $hairdresser->id,
            'date' => $date,
            'hour' => self::START_TIME,
        ];

        Booking::create([
            'name' => self::NAME_1,
            'email' => self::EMAIL_1,
            'hairdresser_id' => $hairdresser->id,
            'scheduled_at' => Carbon::parse($scheduledAt),
        ]);

        $response = $this->postJson(self::API_ENDPOINT, $payload);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['hour'])
            ->assertJsonPath('errors.hour.0', 'This time slot is already booked for the selected hairdresser. Please choose another time.');

        $this->assertSame(1, Booking::where('hairdresser_id', $hairdresser->id)->count());
    }

    public function test_different_hairdresser_can_be_booked_for_same_slot(): void
    {
        $firstHairdresser = Hairdresser::factory()->create();
        $secondHairdresser = Hairdresser::factory()->create();
        $date = $this->nextWeekday();
        $scheduledAt = $this->scheduledAt($date);
        $payload = [
            'name' => self::NAME_2,
            'email' => self::EMAIL_2,
            'hairdresser_id' => $secondHairdresser->id,
            'date' => $date,
            'hour' => self::START_TIME,
        ];

        Booking::create([
            'name' => self::NAME_1,
            'email' => self::EMAIL_1,
            'hairdresser_id' => $firstHairdresser->id,
            'scheduled_at' => Carbon::parse($scheduledAt),
        ]);

        $response = $this->postJson(self::API_ENDPOINT, $payload);

        $response->assertCreated()
            ->assertJsonPath('message', self::SUCCESS_MESSAGE);

        $this->assertDatabaseHas('bookings', [
            'email' => self::EMAIL_2,
            'hairdresser_id' => $secondHairdresser->id,
            'scheduled_at' => $scheduledAt,
        ]);
    }

    public function test_weekend_booking_is_rejected(): void
    {
        $hairdresser = Hairdresser::factory()->create();
        $weekendDate = Carbon::today()->next(Carbon::SATURDAY)->toDateString();
        $payload = [
            'name' => self::NAME_1,
            'email' => self::EMAIL_1,
            'hairdresser_id' => $hairdresser->id,
            'date' => $weekendDate,
            'hour' => self::START_TIME,
        ];

        $response = $this->postJson(self::API_ENDPOINT, $payload);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['date']);
    }

    public function test_outside_business_hours_booking_is_rejected(): void
    {
        $hairdresser = Hairdresser::factory()->create();
        $payload = [
            'name' => self::NAME_1,
            'email' => self::EMAIL_1,
            'hairdresser_id' => $hairdresser->id,
            'date' => $this->nextWeekday(),
            'hour' => '18:00',
        ];

        $response = $this->postJson(self::API_ENDPOINT, $payload);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['hour']);
    }

    private function nextWeekday(): string
    {
        return Carbon::today()->next(Carbon::MONDAY)->toDateString();
    }

    private function scheduledAt(string $date): string
    {
        return $date.' '.self::START_TIME.':00';
    }
}
