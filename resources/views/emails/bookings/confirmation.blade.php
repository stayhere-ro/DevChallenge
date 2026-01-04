<x-mail::message>
# Booking confirmed ✅

Hi {{ $booking->name ?? 'there' }},

Your booking is confirmed.

@php
    $scheduledAt = $booking->scheduled_at;
    $date = $scheduledAt?->format('Y-m-d') ?? '—';
    $time = $scheduledAt?->format('H:i') ?? '—';
@endphp

<x-mail::panel>
**When:** {{ $date }} at {{ $time }}
@if($hairdresser)

**Hairdresser:** {{ $hairdresser->name ?? '—' }}
@endif

**Booking ID:** {{ $booking->id }}
</x-mail::panel>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
