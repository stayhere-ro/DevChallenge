<h1>Booking confirmed</h1>

<p>Szia {{ $booking->name }},</p>

<p>Megkaptuk a foglalasodat.</p>

<ul>
    <li>Date: {{ $booking->scheduled_at->format('Y-m-d') }}</li>
    <li>Time: {{ $booking->scheduled_at->format('H:i') }}</li>
    <li>Hairdresser: {{ $booking->hairdresser->name }}</li>
</ul>

<p>Alig várjuk hogy lássuk. </p>