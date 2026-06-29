<h1>New booking received</h1>

<p>Egy uj ugyfel foglalt idot.</p>

<ul>
    <li>Client: {{ $booking->name }}</li>
    <li>Email: {{ $booking->email }}</li>
    <li>Date: {{ $booking->scheduled_at->format('Y-m-d') }}</li>
    <li>Time: {{ $booking->scheduled_at->format('H:i') }}</li>
</ul>