<?php

namespace Tests\Feature\Http\Controllers\Admin;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExportBookingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_export_bookings(): void
    {
        $response = $this->get(route('admin.bookings.export', [
            'from' => '2026-06-10',
            'to' => '2026-06-10',
        ]));

        $response->assertRedirect(route('login'));
    }

    public function test_hairdresser_can_export_own_bookings_for_selected_date_range(): void
    {
        $hairdresser = User::factory()->create();
        $otherHairdresser = User::factory()->create();

        $includedBooking = $this->createBooking([
            'hairdresser_id' => $hairdresser->id,
            'name' => 'Alice Client',
            'email' => 'alice@example.com',
            'scheduled_at' => '2026-06-10 09:00:00',
            'created_at' => '2026-06-01 08:15:30',
            'updated_at' => '2026-06-01 08:15:30',
        ]);

        $this->createBooking([
            'hairdresser_id' => $hairdresser->id,
            'name' => 'Outside Range',
            'email' => 'outside@example.com',
            'scheduled_at' => '2026-06-11 09:00:00',
            'created_at' => '2026-06-01 08:15:30',
            'updated_at' => '2026-06-01 08:15:30',
        ]);

        $this->createBooking([
            'hairdresser_id' => $otherHairdresser->id,
            'name' => 'Other Hairdresser',
            'email' => 'other@example.com',
            'scheduled_at' => '2026-06-10 10:00:00',
            'created_at' => '2026-06-01 08:15:30',
            'updated_at' => '2026-06-01 08:15:30',
        ]);

        $response = $this->actingAs($hairdresser)->get(route('admin.bookings.export', [
            'from' => '2026-06-10',
            'to' => '2026-06-10',
        ]));

        $response->assertOk();
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString(
            'attachment; filename=bookings-2026-06-10-to-2026-06-10.csv',
            $response->headers->get('content-disposition')
        );

        $rows = $this->csvRows($response->streamedContent());

        $this->assertSame([
            'Booking ID',
            'Client Name',
            'Client Email',
            'Appointment Date',
            'Appointment Time',
            'Booked At',
        ], $rows[0]);

        $this->assertSame([
            (string) $includedBooking->id,
            'Alice Client',
            'alice@example.com',
            '2026-06-10',
            '09:00',
            '2026-06-01 08:15:30',
        ], $rows[1]);

        $this->assertCount(2, $rows);
    }

    public function test_export_requires_valid_date_range(): void
    {
        $hairdresser = User::factory()->create();

        $response = $this->actingAs($hairdresser)->get(route('admin.bookings.export', [
            'from' => '2026-06-11',
            'to' => '2026-06-10',
        ]));

        $response->assertSessionHasErrors('to');
    }

    /**
     * @return array<int, array<int, string>>
     */
    private function csvRows(string $csv): array
    {
        return array_map('str_getcsv', explode("\n", trim($csv)));
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function createBooking(array $attributes): Booking
    {
        return Booking::unguarded(fn () => Booking::create($attributes));
    }
}
