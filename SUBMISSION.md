# Submission

## What I did

This section maps directly to the homework's required submission notes:
which tasks were selected and why, the assumptions and trade-offs made,
and how to run/test/verify the work. See the sections below for each.

## Tasks selected

I picked four tasks across different levels, implemented in the order
below.

- **#1 — Create a basic booking endpoint** (Level 1)
- **#2 — Prevent overbooking** (Level 1)
- **#9 — Service layer and validation hardening** (Level 4)
- **#14 — Concurrency-safe bookings** (Level 5)

### Why these four, and why in this order

**Task #1** The project only had a web form (`POST /bookings`, redirect
response). I added a separate JSON surface: `StoreBookingRequest` for
input validation, `Api\BookingController::store()`, and the
`POST /api/bookings` route — accepting `client_email`, `hairdresser_id`,
`date`, and `start_time`, returning a structured JSON success or
validation-error response. This also required extending the data model
with `hairdresser_id` (see below), since the task explicitly scopes
bookings to "a hairdresser ID."

**Task #2** Once the basic endpoint existed, the obvious next gap was that
nothing stopped two bookings from landing on the same hairdresser and slot.
I added an existence check before creation, plus a database-level composite
unique index (`hairdresser_id`, `scheduled_at`) as the real guarantee, with
feature tests proving a duplicate slot is rejected and that two different
hairdressers can share the same time slot.

**Task #9** With the endpoint and the overbooking rule both working
inside the controller, the controller had started accumulating logic that
isn't an HTTP concern: parsing the payload into a scheduled-at value,
checking availability, handling the duplicate-key database error. I
extracted that into `CreateBookingData` (a DTO, decoupling the HTTP
payload shape from the domain) and `BookingService::create()` (the single
place that owns booking-creation rules), then refactored the controller
down to: build the DTO, call the service, translate the result into JSON.
I did this *after* #1/#2 rather than before because the refactor was much
easier to get right once I already had working code and passing tests
that defined the correct behavior — the service layer formalizes logic
that had already been proven to work, rather than being designed
speculatively up front.

**Task #14**, as the verification step. By this point the concurrency
safeguard (unique index + caught `QueryException` → `SlotAlreadyBookedException`)
already existed structurally as a side effect of #2 and #9. What #14
actually added was dedicated test coverage confirming that the database
constraint — not just the application-level check — is what makes the
guarantee hold, plus confirming the API surfaces that as a clean `409`
rather than a raw `500`.

## Data model decision: `hairdresser_id`

The original schema (`bookings`: `name`, `email`, `scheduled_at`) assumed a
single hairdresser, with a unique index on `scheduled_at` alone. Task #1
scopes a booking to a specific hairdresser ID, and task #14 explicitly
asks to prevent "overlapping bookings for the same hairdresser and time
slot" — both imply multiple hairdressers, a feature this demo didn't have
yet.

I added `hairdresser_id` (default `1`) rather than skip that part of the
requirement. This keeps the existing public web form working unchanged
(every web booking is implicitly `hairdresser_id = 1`), while the new
`/api/bookings` endpoint accepts an explicit `hairdresser_id`, so the data
model is ready for genuine multi-hairdresser support later.

The unique index was migrated from `scheduled_at` alone to a composite
`(hairdresser_id, scheduled_at)`, so two different hairdressers can be
booked at the same time, but the same hairdresser cannot be double-booked.

## How overbooking is actually prevented (Task #1 + #2, hardened by #14)

Two layers, intentionally redundant:

1. **Application-level check** — `BookingService::ensureSlotIsAvailable()`
   queries for an existing booking with the same `hairdresser_id` and
   `scheduled_at` before creating a new one, inside a `DB::transaction()`.
   This is what produces the clean `409 Conflict` JSON response in the
   common case.
2. **Database-level constraint** — the composite unique index is the real,
   final guarantee. If two requests somehow both pass the application
   check (a genuine race condition — see below), the second `INSERT` is
   rejected by MySQL with a unique-constraint violation (`QueryException`,
   SQLSTATE `23000`). `BookingService::create()` catches that and rethrows
   it as a `SlotAlreadyBookedException`, so the API never leaks a raw
   500 database error — the caller always gets the same clear, structured
   JSON conflict response regardless of which layer caught the conflict.

This is "belt and suspenders" by design: layer 1 gives a fast, friendly
error in the normal case; layer 2 is what actually makes the guarantee
true under concurrency, independent of whether the application code path
is ever bypassed or changed later.

## Assumptions

- A single client email (`client_email`) is used as both the booking's
  `name` and `email` field for now, since the API payload doesn't collect
  a separate display name — matching what the task brief describes for
  the JSON endpoint (client email, hairdresser ID, date, start time).
- Only full-hour slots (`:00` minutes) are accepted via the API, mirroring
  the original web form's hour-only picker. This wasn't explicitly
  required but keeps both booking flows consistent.
- `hairdresser_id` defaults to `1` for any booking created through the
  existing public web form, preserving its current single-hairdresser
  behavior with no breaking change.

## How to run and verify

```bash
php artisan optimize:clear
php artisan route:list
php artisan migrate:fresh
php artisan test
```

All tests pass, covering:

- `tests/Unit/BookingServiceTest.php` — service-level behavior in isolation
  (no HTTP, no JSON formatting)
- `tests/Feature/ApiBookingTest.php` — end-to-end API behavior: successful
  booking, duplicate-slot conflict, multi-hairdresser independence,
  validation errors (invalid email, weekend, non-full-hour slot)
- `tests/Feature/BookingConcurrencyTest.php` — database-level constraint
  enforcement and the API's conflict response

Manual verification of the new endpoint:

```bash
php artisan optimize:clear
php artisan route:list
php artisan serve
```

Simple test case:

```bash
curl -X POST http://localhost:8000/api/bookings \
  -H "Content-Type: application/json" \
  -d "{\"client_email\":\"client@example.com\",\"hairdresser_id\":1,\"date\":\"2026-07-01\",\"start_time\":\"10:00\"}"
```
Expected result: success (if you run the command 2nd time, it will fail, because the slot
                          is already booked).


Invalid email test case:

```bash
curl -X POST http://localhost:8000/api/bookings \
  -H "Content-Type: application/json" \
  -d "{\"client_email\":\"bad-email\",\"hairdresser_id\":1,\"date\":\"2026-07-01\",\"start_time\":\"10:00\"}"
```
Expected: failed.


Weekend test case:

```bash
curl -X POST http://localhost:8000/api/bookings \
  -H "Content-Type: application/json" \
  -d "{\"client_email\":\"client@example.com\",\"hairdresser_id\":1,\"date\":\"2026-07-04\",\"start_time\":\"10:00\"}"
```
Expected: failed.
