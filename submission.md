# Submission

## Overview
I started by reviewing all six levels and their sub-tasks to gather as much information as possible before implementing anything. 
I believe that careful upfront analysis can save a significant amount of unnecessary work later, 
which is why I spent time understanding the requirements, 
levels, and their dependencies in detail.

Since the levels are built on top of each other, from simpler tasks like basic API endpoint creation and database handling (model factories, database seeders)
to more advanced, development-supporting features (such as Swagger documentation and GitHub Actions with PHPStan), 
I decided to approach them in the order in the order in which they were listed. 
This helped ensure a structured progression and allowed each step to build naturally on the previous one.

## Implemented Features

### Domain Modeling & Database Design
- Created a **Hairdresser** model with corresponding migration
- Defined clear **Eloquent relationships** between `User`, `Hairdresser`, and `Booking`
- Enhanced the `bookings` table with a **composite unique index** on `(hairdresser_id, scheduled_at)` to prevent double booking at the database level
- Implemented **model factories** and **seeders** for consistent local development and testing

---

### Booking Functionality
- Updated the **booking creation logic**, **input validation** and **conflict prevention** with `hairdresser_id`
- Added the ability to **select a hairdresser** when creating a booking
- Created a **personal booking history** view for authenticated users
- Alongside the existing guest booking form, I created a new booking form specifically for logged-in users

---

### API & CLI Interfaces
- Implemented a `GET /api/bookings?email=` endpoint to retrieve a client’s bookings in JSON format
- Implemented a `POST /api/bookings` endpoint with validation and overlap checks
- Added a custom Artisan command:  
  `php artisan bookings:list {email}`  
  which outputs a user’s bookings in a formatted table

---

### Architecture Improvements
- Introduced a **Service Layer** to make the business logic cleaner
- Implemented a **BookingData DTO** for clean data transfer
- Added a dedicated **ApiBookingController** to clearly separate responsibilities. The application uses three booking-related controllers: `BookingController` for guest users, `UserBookingController` for authenticated users, and `ApiBookingController` for API interactions
- Used Laravel Debugbar to detect lazy loading and potential N+1 query issues, then disabled lazy loading to prevent them
---

### Email Notifications & Queues
- Implemented email notifications for:
  - Clients (booking confirmation)
  - Hairdressers (new booking notification)
- Dispatched emails via the **queue system**
- Added retries and backoff parameters
- Verified mail delivery using **Mailtrap** and Laravel logs

---

### Reliability & Safety
- Added **Idempotency middleware** to prevent duplicate booking requests
- Implemented **rate limiting** for sensitive endpoints

---

### Testing, Documentation & CI
- Created feature tests covering:
    - Booking conflicts
    - Idempotent requests
    - Rate limiting behavior
- Integrated **OpenAPI / Swagger documentation** for API endpoints
- Configured **GitHub Actions** to automatically:
  - Run test suites
  - Perform PHPStan static analysis

---

## Task Completion Overview

Below is a summary of the tasks I completed from each level of the assignment:

**Level 1 — Core API and Validation**  
Tasks completed: 1, 2

**Level 2 — Tooling and UX**  
Tasks completed: 3, 4, 5

**Level 3 — Enhancements**  
Tasks completed: 6, 7

**Level 4 — Architecture & Quality**  
Tasks completed: 9, 10, 11

**Level 5 — Scalability & Resilience**  
Tasks completed: 12, 13

**Level 6 — Data & Reporting**  
Tasks completed: 17 (partially)


