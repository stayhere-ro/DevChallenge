<?php

namespace Tests\Feature;

use App\Mail\ClientBookingConfirmation;
use App\Mail\HairdresserNewBookingNotification;
use App\Models\Booking;
use App\Models\Hairdresser;
use App\Services\BookingNotificationService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class BookingNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_sends_booking_emails_to_client_and_hairdresser(): void
    {
        Mail::fake();

        $hairdresser = Hairdresser::factory()->create();
        $booking = Booking::create([
            'name' => 'Gergo',
            'email' => 'gergo@gmail.com',
            'hairdresser_id' => $hairdresser->id,
            'scheduled_at' => Carbon::parse('2026-07-06 10:00:00'),
        ]);

        app(BookingNotificationService::class)->sendForBooking($booking);

        Mail::assertSent(ClientBookingConfirmation::class, function (ClientBookingConfirmation $mail): bool {
            return $mail->hasTo('gergo@gmail.com');
        });

        Mail::assertSent(HairdresserNewBookingNotification::class, function (HairdresserNewBookingNotification $mail) use ($hairdresser): bool {
            return $mail->hasTo($hairdresser->user->email);
        });
    }
}
