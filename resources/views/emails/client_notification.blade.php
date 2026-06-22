<!DOCTYPE html>
<html>
<head>
    <title>New Booking Notification</title>
</head>
<body>
<h1>Hello!</h1>
<p>Your booking has been confirmed!</p>

<ul>
    <li><strong>Hairdresser Name:</strong> {{ $booking->hairdresser->name }}</li>
    <li><strong>Date and Time:</strong> {{ $booking->scheduled_at->format('Y-m-d H:i') }}</li>
</ul>

</body>
</html>
