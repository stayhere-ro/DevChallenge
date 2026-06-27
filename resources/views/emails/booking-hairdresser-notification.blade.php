@component('mail::message')
# New booking

A new appointment was booked.

- **Client:** {{ $booking->name }} ({{ $booking->email }})
- **When:** {{ $booking->scheduled_at->format('Y-m-d H:i') }}
- **Hairdresser:** {{ $booking->hairdresser?->name ?? 'N/A' }}

Thanks,<br>
{{ config('app.name') }}
@endcomponent
