<x-mail::message>
# New booking received

Hi {{ $hairdresser->name ?? 'there' }},

A new booking has been made.

@php
    $scheduledAt = $booking->scheduled_at;
    $date = $scheduledAt?->format('Y-m-d') ?? '—';
    $time = $scheduledAt?->format('H:i') ?? '—';
    $clientName = $booking->name ?: '—';
    $clientEmail = $booking->email ?: '—';
@endphp

<x-mail::panel>
**When:** {{ $date }} at {{ $time }}

**Client:** {{ $clientName }} ({{ $clientEmail }})

**Booking ID:** {{ $booking->id }}
</x-mail::panel>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
