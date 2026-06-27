@component('mail::message')
# Booking confirmed

Hello {{ $booking->name }},

Your appointment is scheduled for **{{ $booking->scheduled_at->format('l, M j Y') }}** at **{{ $booking->scheduled_at->format('H:i') }}**.

@if($booking->hairdresser)
**Hairdresser:** {{ $booking->hairdresser->name }}
@endif

Thanks,<br>
{{ config('app.name') }}
@endcomponent
