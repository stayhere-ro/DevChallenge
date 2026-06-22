<!DOCTYPE html>
<html>
<head>
    <title>New Booking Notification</title>
</head>
<body>
<h1>Hello!</h1>
<p>A new booking has been made through the system.</p>

<ul>
    <li><strong>Client Name:</strong> {{ $booking->name }}</li>
    <li><strong>Client Email:</strong> {{ $booking->email }}</li>
    <li><strong>Date and Time:</strong> {{ $booking->scheduled_at->format('Y-m-d H:i') }}</li>
</ul>

</body>
</html>
