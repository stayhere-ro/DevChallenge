<?php

namespace Tests\Feature\Api;

use App\DTO\CreateBookingData;
use App\Exceptions\BookingConflictException;
use App\Models\Hairdresser;
use App\Services\Booking\BookingService;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Tests\TestCase;

class BookingConcurrencyTest extends TestCase
{
    public function test_only_one_booking_survives_repeated_writes_for_same_slot(): void
    {
        $hairdresser = Hairdresser::factory()->create();
        $scheduledAt = Carbon::parse('next monday 10:00');
        $service = app(BookingService::class);

        $successes = 0;
        $conflicts = 0;

        for ($attempt = 0; $attempt < 5; $attempt++) {
            try {
                $service->create(new CreateBookingData(
                    hairdresserId: $hairdresser->id,
                    clientName: "Client {$attempt}",
                    clientEmail: "client{$attempt}@example.com",
                    scheduledAt: $scheduledAt,
                ));
                $successes++;
            } catch (BookingConflictException) {
                $conflicts++;
            }
        }

        $this->assertSame(1, $successes);
        $this->assertSame(4, $conflicts);
        $this->assertDatabaseCount('bookings', 1);
    }

    public function test_unique_index_rejects_duplicate_row_at_database_level(): void
    {
        $hairdresser = Hairdresser::factory()->create();
        $scheduledAt = Carbon::parse('next monday 11:00');

        $service = app(BookingService::class);
        $service->create(new CreateBookingData(
            hairdresserId: $hairdresser->id,
            clientName: 'First',
            clientEmail: 'first@example.com',
            scheduledAt: $scheduledAt,
        ));

        $this->expectException(QueryException::class);

        \App\Models\Booking::query()->create([
            'hairdresser_id' => $hairdresser->id,
            'name' => 'Second',
            'email' => 'second@example.com',
            'scheduled_at' => $scheduledAt,
        ]);
    }

    public function test_parallel_api_posts_return_one_created_and_one_conflict(): void
    {
        $hairdresser = Hairdresser::factory()->create();
        $date = Carbon::parse('next monday')->toDateString();
        $payload = [
            'email' => 'race@example.com',
            'hairdresser_id' => $hairdresser->id,
            'date' => $date,
            'start_time' => '14:00',
        ];

        $first = $this->postJson('/api/bookings', $payload);
        $second = $this->postJson('/api/bookings', $payload);

        $statuses = collect([$first->status(), $second->status()])->sort()->values()->all();

        $this->assertSame([201, 409], $statuses);
        $this->assertDatabaseCount('bookings', 1);
        $second->assertJsonPath('error', 'booking_conflict');
    }
}
