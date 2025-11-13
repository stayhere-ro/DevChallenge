**Todo:**


1. Database Setup
   Create migrations for bookings table with fields:
   id, client_email, hairdresser_id, booking_date, start_time, duration, created_at, updated_at
   Create a simple seeder with 5-10 test bookings
2. Basic API Endpoint
   Create ONE endpoint: POST /api/bookings
   Accept: client email, hairdresser ID, date, start time
   Validate input (required fields, valid email format)
   Return JSON response (success/error)
3. Simple Validation
   Check if the time slot is available:
   No overlapping bookings for the same hairdresser
   Return clear error message if slot is taken
4. Artisan Command
   Create: php artisan bookings:list {email}
   Display all bookings for the given email
   Show: date, time, hairdresser ID in a table format


* API endpoints for these functionalities
* Seed the database with auto-generated fake data
* Write tests to ensure the system handles overbooking well.
* Email Notification System:
  * Send a notification to the hairdresser if somebody makes a booking.
  * Send a confirmation email to the client if the booking is successful.

* Create an Artisan command for listing the bookings associated with a certain email.
* Let users register themselves and have their bookings history displayed below the booking form. You could add paging, searching and filtering by date or hour.
* You are free to add extra functionalities to the demo to highlight skills you think are relevant, but the specification does not require it.

Note that this is a demo project, so we don't expect you to create a complete implementation. We are interested in getting a general view of our candidate's way of thinking and level of preparation.
What we expect from you is to reason your choices and try to adhere to coding best practices.



