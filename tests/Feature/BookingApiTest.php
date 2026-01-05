<?php

namespace Tests\Feature;

use App\Mail\BookingConfirmationMail;
use App\Mail\NewBookingMail;
use App\Models\Booking;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class BookingApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $timezone = config('app.timezone');
        Carbon::setTestNow(Carbon::create(2026, 1, 5, 9, 0, 0, $timezone));
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    private function makeHairdresser(array $overrides = []): User
    {
        return User::factory()->create(array_merge([
            'role' => 'hairdresser',
        ], $overrides));
    }

    /**
     * Test a successful booking.
     */
    public function test_it_creates_a_booking_successfully(): void
    {
        $hairdresser = $this->makeHairdresser();
        $payload = [
            'client_email' => 'client@example.com',
            'hairdresser_id' => $hairdresser->id,
            'date' => '2026-01-05',
            'start_time' => '10:00',
        ];
        $response = $this->postJson('/api/bookings', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.client_email', $payload['client_email'])
            ->assertJsonPath('data.hairdresser_id', $hairdresser->id)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id', 'client_email', 'hairdresser_id', 'scheduled_at'],
            ]);

        $this->assertDatabaseHas('bookings', [
            'email' => 'client@example.com',
            'hairdresser_id' => $hairdresser->id,
            'scheduled_at' => Carbon::createFromFormat(
                'Y-m-d H:i:s',
                '2026-01-05 10:00:00', config('app.timezone') ?? 'UTC')->format('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Test a successful booking for a different hairdresser at the same time.
     */
    public function test_it_allows_booking_the_same_time_slot_for_different_hairdressers(): void
    {
        $hairdresserA = $this->makeHairdresser();
        $hairdresserB = $this->makeHairdresser();

        // Create an existing booking for hairdresser A at 10:00
        Booking::create([
            'name' => null,
            'email' => 'existing@example.com',
            'hairdresser_id' => $hairdresserA->id,
            'scheduled_at' => Carbon::createFromFormat(
                'Y-m-d H:i:s',
                '2026-01-05 10:00:00',
                config('app.timezone')
            ),
        ]);

        // Try booking the same slot for hairdresser B -> should succeed
        $payload = [
            'client_email' => 'client@example.com',
            'hairdresser_id' => $hairdresserB->id,
            'date' => '2026-01-05',
            'start_time' => '10:00',
        ];

        $response = $this->postJson('/api/bookings', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.hairdresser_id', $hairdresserB->id);

        $this->assertDatabaseHas('bookings', [
            'email' => 'client@example.com',
            'hairdresser_id' => $hairdresserB->id,
            'scheduled_at' => Carbon::createFromFormat(
                'Y-m-d H:i:s',
                '2026-01-05 10:00:00', config('app.timezone') ?? 'UTC')->format('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Test a failed booking due to a conflicting slot.
     */
    public function test_it_rejects_a_conflicting_booking_for_same_hairdresser_and_time_slot(): void
    {
        $hairdresser = $this->makeHairdresser();

        Booking::create([
            'name' => null,
            'email' => 'existing@example.com',
            'hairdresser_id' => $hairdresser->id,
            'scheduled_at' => Carbon::createFromFormat(
                'Y-m-d H:i:s',
                '2026-01-05 10:00:00',
                config('app.timezone')
            ),
        ]);

        $payload = [
            'client_email' => 'client@example.com',
            'hairdresser_id' => $hairdresser->id,
            'date' => '2026-01-05',
            'start_time' => '10:00',
        ];
        $response = $this->postJson('/api/bookings', $payload);

        $response->assertStatus(422)
            ->assertJsonPath('success', false)
            ->assertJsonPath('message', 'Validation failed.')
            ->assertJsonStructure(['success', 'message', 'errors'])
            ->assertJsonPath('errors.start_time.0', 'This time slot is already booked. Please choose another time.');
    }

    /**
     * Test booking not available during the weekend.
     */
    public function test_it_rejects_a_booking_during_the_weekend(): void
    {
        $hairdresser = $this->makeHairdresser();
        $payload = [
            'client_email' => 'client@example.com',
            'hairdresser_id' => $hairdresser->id,
            'date' => '2026-01-10',
            'start_time' => '10:00',
        ];
        $response = $this->postJson('/api/bookings', $payload);

        $response->assertStatus(422)
            ->assertJsonPath('success', false)
            ->assertJsonPath('message', 'Validation failed.')
            ->assertJsonStructure(['success', 'message', 'errors'])
            ->assertJsonPath('errors.date.0', 'Bookings are not available on weekends.');
    }

    /**
     * Test booking outside business hours.
     */
    public function test_it_rejects_a_booking_outside_business_hours(): void
    {
        $hairdresser = $this->makeHairdresser();
        $payload = [
            'client_email' => 'client@example.com',
            'hairdresser_id' => $hairdresser->id,
            'date' => '2026-01-05',
            'start_time' => '07:00',
        ];
        $response = $this->postJson('/api/bookings', $payload);

        $response->assertStatus(422)
            ->assertJsonPath('success', false)
            ->assertJsonPath('message', 'Validation failed.')
            ->assertJsonStructure(['success', 'message', 'errors'])
            ->assertJsonPath('errors.start_time.0', 'Bookings are only available between 08:00 AM and 5:00 PM.');
    }

    /**
     * Test listing bookings by email.
     */
    public function test_it_lists_bookings_by_email(): void
    {
        $email = 'client@example.com';
        $hairdresser = $this->makeHairdresser(['email' => 'hairdresser@example.com']);

        $slots = [
            '2026-01-05 10:00:00',
            '2026-01-05 11:00:00',
            '2026-01-05 12:00:00',
        ];

        foreach ($slots as $slot) {
            Booking::factory()->create([
                'email' => $email,
                'hairdresser_id' => $hairdresser->id,
                'scheduled_at' => $slot,
            ]);
        }

        $otherSlots = [
            '2026-01-06 10:00:00',
            '2026-01-06 11:00:00',
        ];

        foreach ($otherSlots as $slot) {
            Booking::factory()->create([
                'email' => 'other@example.com',
                'hairdresser_id' => $hairdresser->id,
                'scheduled_at' => $slot,
            ]);
        }

        $response = $this->getJson('/api/bookings?email='.$email);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonCount(3, 'data');
    }

    /**
     * Test listing with invalid email.
     */
    public function test_it_returns_422_json_for_invalid_email_query_param(): void
    {
        $response = $this->getJson('/api/bookings?email=not-an-email');

        $response->assertStatus(422)
            ->assertJsonPath('success', false)
            ->assertJsonPath('message', 'Validation failed.')
            ->assertJsonStructure([
                'success',
                'message',
                'errors' => ['email'],
            ]);
    }

    /**
     * Test listing with empty email.
     */
    public function test_it_returns_422_json_when_email_query_param_is_missing(): void
    {
        $response = $this->getJson('/api/bookings?email=');

        $response->assertStatus(422)
            ->assertJsonPath('success', false)
            ->assertJsonPath('message', 'Validation failed.')
            ->assertJsonStructure([
                'success',
                'message',
                'errors' => ['email'],
            ]);
    }

    /**
     * Test emails notifications are sent from api.
     */
    public function test_api_store_sends_hairdresser_and_client_emails(): void
    {
        Mail::fake();

        $hairdresser = User::factory()->create([
            'email' => 'hairdresser@example.com',
            'name' => 'Hair Dresser',
            'role' => 'hairdresser',
        ]);

        $payload = [
            'client_email' => 'client@example.com',
            'hairdresser_id' => $hairdresser->id,
            'date' => now()->addDay()->toDateString(),
            'start_time' => '10:00',
        ];

        $this->postJson('/api/bookings', $payload)->assertCreated();

        Mail::assertSent(NewBookingMail::class, fn ($m) => $m->hasTo('hairdresser@example.com'));
        Mail::assertSent(BookingConfirmationMail::class, fn ($m) => $m->hasTo('client@example.com'));
    }

    /**
     * Test emails notifications are sent from web.
     */
    public function test_web_store_sends_hairdresser_and_client_emails(): void
    {
        Mail::fake();

        $hairdresser = User::factory()->create([
            'email' => 'hairdresser@example.com',
            'name' => 'Hair Dresser',
            'role' => 'hairdresser',
        ]);

        $timezone = config('app.timezone');
        $date = Carbon::parse('next monday', $timezone)->toDateString();

        $payload = [
            'name' => 'Client Name',
            'email' => 'client@example.com',
            'date' => $date,
            'hour' => '10:00',
            'hairdresser_id' => $hairdresser->id,
        ];

        $this->post('/bookings', $payload)
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        Mail::assertSent(NewBookingMail::class, fn ($m) => $m->hasTo('hairdresser@example.com'));
        Mail::assertSent(BookingConfirmationMail::class, fn ($m) => $m->hasTo('client@example.com'));
    }
}
