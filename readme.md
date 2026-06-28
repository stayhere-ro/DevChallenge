# Level 1 — Core API and Validation

>  Create a basic booking endpoint (1)
>  Prevent overbooking (2)

***Problems Identified***
a. The hairdressers table did not exist.
b. The foreign key (hairdresser_id) required by the bookings table was missing.
c. The booking->name can not be null... That means if I don't have all the required information, I can't save the data. 
d. The booking table has unque index and can't save 

**Solution**
I created database migrations:

`2026_06_27_133611_add_column_hairdresser_id_to_bookings_table`
`2026_06_27_141847_create_hairdressers_table`
`2026_06_27_164923_update_unique_index_for_bookings_table`

**Overview**
Since the endpoint is a POST route, I assumed it should provide the same functionality as the existing store method in the original controller.

Based on this, I started by separating the dashboard and API responsibilities into different controllers. I created a dedicated API controller containing the same booking creation logic.

To avoid code duplication and improve maintainability, I extracted the reusable booking creation logic into a shared `BookingService` with its corresponding `Interface`.

Both controllers use the same validation request. The only difference for the API endpoint is that requests must include the Accept: application/json header, ensuring validation errors are returned as JSON instead of redirecting to the original form.

Finally, I implemented the booking creation logic using a DTO to map and prepare the data before inserting it into the Booking model.

**Assumption**
Since the API request does not provide a customer name, I think it's acceptable to save an empty string ('') for the name field in the database.
*I must consider that, it would be better if i requrire the name to. :( *

**Automated Test Coverage**
I created `StoreBookingApiTest`, which extends `TestCase`. It contains six test cases that validate the behavior of this endpoint.

To run the test suite, use:
 `php artisan test`

**Manual Test Coverage**

1. Run `php artisan serve`.
2. Use Postman or another tool to acces `http://127.0.0.1:8000/api/bookings`.
3. Add Header for content type to `Accept: application/json`.
4. As parameters you have to complete with `email` `hairdresser_id` `date` `hour`.
5. Check DB registrations for valid cases.

Example of payload: 
[
	'hairdresser_id' => 1,
	'email' => 'john@example.test',
	'date' => '2024-07-01',
	'hour' => '09:00',
]


# Level 2 — Tooling and UX

> Seeder and factories (3)

I used a factory class and a database seeder with fake data to generate additional records in the database. This is why I initially created the Hairdresser model.

I used only one seeder because Booking::factory() already defines 'hairdresser_id' => Hairdresser::factory(). This approach has both advantages and disadvantages.

On the one hand, it ensures data consistency between related models without introducing errors. On the other hand, it can lead to a heavier database load, depending on the number of seeds.

Also, because the database can be corrupted by modifiers, I always truncate the data.

To run the test suite, use:
 `php artisan db:seed`

>  Basic API listing (5)

This was one of the simplest requests. This API endpoint returns all data associated with the provided email. To ensure data consistency, I used a one-to-one relationship to return the correct data to the user.

I implemented the business logic in the Service layer and added request validation for the email. If the email is not found, the API returns an error.

**Manual Test Coverage**

1. Run `php artisan serve`.
2. Use Postman or another tool to acces `http://127.0.0.1:8000/api/bookings`.
3. Add Header for content type to `Accept: application/json`.
4. As parameters you have to complete with `email`.

# Level 4 — Architecture & Quality
>  Service layer and validation hardening (9)

***Problems Identified***
Duplicate logic for dasboard and API...
The hairdresser_id is nullable but is not saved from interface.

**Overview**
I extracted reusable logic into services for both API and dashboard registration flows.

I removed the `withValidator` method from the `BookingRequest` and replaced it with three (`CheckBusinessHours`, `CheckHairdresserAndScheduledHours`, `CheckWeekend`) separate validation rules containing the same logic. This makes the code cleaner and more reusable for additional scenarios. These three rules are used in both POST routes.

I handled the DTO scenario. In the API, the name is not provided, while the dashboard form provides it but does not include hairdresser_id. Therefore, I updated the DTO to allow nullable values to avoid potential errors.

Regarding service testing: I already moved this logic into the BookingController for the API. To properly test the service layer, I should use mocks, but I have not practiced this enough yet, so I will use feature tests on the API POST route insted.


# Reasons why I Took those challenges

Well, I read all the tasks first and then decided which one was best to start with. I initially started with `Service layer and validation hardening` because I needed to create reusable logic. Then I thought it would be easier to handle `Create a basic booking endpoint` and `Prevent overbooking`, but that gave me some trouble because I wasnt sure which solution would be better. In the end, I went with it. Somehow. 😄

Then I thought about the data and tests… thats why I chose `Seeder and Factories`, since I needed fast data to develop the code for Basic API listing.

It was a good challenge for this weekend. It made me use more things like Unit Tests, Rules, etc., and also helped me go deeper into Email handling, Idempotency-Key, and CSV exports, which I hadn't used much before.
