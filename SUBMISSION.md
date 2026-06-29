# Submission

## Selected Tasks

### 1. Create a basic booking endpoint

Create a basic booking endpoint
POST /api/bookings
Accept: client email, hairdresser ID, date, start time
Validate: required fields, email format, business rules (no weekends, business hours, one per hour)
Return JSON: success/error with clear messages

I read the task and I noticed that there are some incosistent things compared to the database(hairdresserId). So the first thing was to
ask if i get can more information to solve the task.

I didn't get any information so I made the decision to add a `HairDressers` table and link to the bookings through `bookings.hairdresser_id`

## Why I decided like this?

Usually in a Hairdresser Shop multiple haidressers are working. This way our website will stay flexible if we want to have multipe Hairdressers, make profiles for hairdressers or show personal calendar for them.

Added the `POST /api/bookings` endpoint

I modified the frontend to be consistent with the new logic, so now when you are making a reservation you need to choose a hairdresser

I added a new HairDresserFactory which creats a hairdresser(I needed to write the tests more easily) and also created the seeder which can seed 2 hairdressers into the database.

I checked if the logic works correctly with Postman

I wrote tests to check if the functinality was implemented correctly - api booking can be created

- same hairdresser cannot be double booked for same slot
- different hairdresser can be booked for same slot
- outside business hours booking is rejected

While doing this task the second part of it was impelented to the: Prevent overbooking

Validation level
When i am creating a new booking I am checking if already exists a booking with that hairdresser at that time.
Database level
I added a constraint into the database so two rows are not allowed with the same hairdresser_id and same time.

To test it you need a datbase then you run the code. you can eather test it fro mthe frontend or with postman

### 2. Artisan command

I choose this task because with other backend technologies I didn't use this type of things and I was curious hhow can i implement it.

I started the task with reading a few websites about Artisan commands. i realized that it is similar to node where you
have commands in the package-lock.json but i didn't used it for getting data from the database.

I used the `php artisan make:command ListBookings` command and added the logic to get the data and make the table.

I tested the command in the terminal, then i wrote 2 tests to test the command. The 2 tests check if the command works correctly if there are bookings,
or there are no bookings in the database.

run the database and use the command

### 3. Email notifications.

A few weeks ago I solved the same task using spring boot and I am curious how can i implement it using php.
When i was solving this task I checked the possible ways to send emails. I was using MailHog but now I saw that MailPit is in the .env
so I will use that
If you want real mails change the data in the .env.
When I did it I used a free gamil account. Which allows 500 mails/day. You have to create a new mail account enable 2 factor auth and
then you can get the 16 digit password.

`docker run --rm -p 1025:1025 -p 8025:8025 axllent/mailpit`
I use this command to run the MailPit in docker

a `.env beallitas`
MAIL_MAILER=smtp
MAIL_HOST=127.0.0.1
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="freshHaircut@example.com"
MAIL_FROM_NAME="${APP_NAME}"

I used this to create the files

`php artisan make:mail ClientBookingConfirmation`
`php artisan make:mail HairdresserNewBookingNotification`

I implemented the task and I noticed that right now I will have the same code in the Api BookingController and in the
other BookingController. So this is why i need to look for a solution because i don't want to have code duplication.

To solve this I created a service `BookingNotificationService` and then i call it from both controllers.

i tested manually and i noticed that if the mail service is not available i get an error so i will solve this too.

Run the docker mailpit and create a booking

### 4. CI- Github Actions.

Continuous Integration with Github Actions

The first step was that i check if locally everything works.

I runned the tests with `php artisan test`
Then i runned the `php vendor/bin/pint --test` to check if there are formatting error.
i solved the errors using the ` ./vendor/bin/pint.bat` command
I installed the larastan with this command `composer require --dev larastan/larastan`
Created the phpstan.neo file-t and setted the level to 5 which is like medium strict.
Then i used this command `./vendor/bin/phpstan.bat analyse --memory-limit=1G` i had to add the --memory-limit because
the defaul 128Mb was not enough.
I created the github CI file which will set up the php enviroment in the cloud it will run a database and it will run 3 tests.
The tests written by me, pint tests and the larastan tests.

at push you can see in the github actions tab the tests

### 5. Concurrency-safe bookings

I already added the unique index to the database unique(hairdresserId + scheduled_at) so I will use this solution and not with locks
When 2 requests will arrive in the same time the database will not let them to create the booking just only one. the second one will fail with
error code 500. This is why i will need to catch that error and return a 409 conflict error. So I started to see that I will need
most part of this task.
Service layer and validation hardening
Extract booking creation logic into a dedicated service (with a simple DTO)
Use Form Requests for validation; return consistent error schema
Add unit/feature tests for the service and validation

So i will create the service and the dto. I will need the transactions because when an inser will fail and if
there are going to be other operations in the transaction not just the insert it will cause database incosistency.

I will create the dto after that I create the Exception.
I will create a new BookingService which will add the transaction and the 2 controllers will call this service
to do the data insertion.

In my solution I rely on the unique constraint to avoid double bookings. I couldn't wirte real paralel, I serached it
and tried to find a solution. With php tests i couldn't resolve but I found a k6 a command line tool which allows me to run javascript like code
i installed it and sent 2 request at the same time and i o got 201 and then i got 409. So it works correctly.
