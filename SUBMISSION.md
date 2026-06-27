# DevChallenge Submission — Paul Hosu

**Fork:** [HthePaul2/DevChallenge](https://github.com/HthePaul2/DevChallenge)  
**Branch for review:** `paul-hosu-dev` (feature branches with `--no-ff` merges on `upstream/main`)  
**PR target:** `stayhere-ro/DevChallenge` → `main`

---

## Summary

Full-stack extension of the Laravel booking app: multi-stylist domain, REST API with concurrency controls, Livewire public wizard, admin dashboard (export, occupancy, stylist management, modal booking), queued emails, Docker/CI toolchain, and documented API contracts (OpenAPI + Postman).

Design priorities: **correctness under concurrency**, **single booking service for web + API**, **test isolation**, and **reviewable git history**.

---

## Task coverage

| Task | Status | Implementation |
|------|--------|----------------|
| **1** POST `/api/bookings` | Done | JSON API + shared `BookingService` with web UI |
| **2** Prevent overbooking | Done | Composite unique index + `lockForUpdate` + HTTP 409 |
| **3** Seeders & factories | Done | `HairdresserSeeder`, `BookingDemoSeeder`, factories |
| **4** `bookings:list {email}` | Done | Artisan command |
| **5** List bookings API | Done | `GET /api/bookings` with **Sanctum** (no public `?email=`) |
| **6** Email notifications | Done | Queued listener; sync in Docker dev |
| **8** Calendar / UX | Done | Livewire 3 wizard + availability polling |
| **9** Service layer + DTO | Done | `BookingService`, `CreateBookingData`, Form Requests |
| **10** API docs | Done | `docs/openapi.yaml` v1.1 + Postman collection |
| **11** CI | Done | GitHub Actions: migrate, PHPUnit, Pint, **PHPStan (level 5)** |
| **12** Idempotency + rate limits | Done | `Idempotency-Key`; 429 with `Retry-After` |
| **13** Async emails | Done | `ShouldQueue` listener |
| **14** Concurrency-safe bookings | Done | Transaction + row lock + unique constraint + concurrency tests |
| **15** Admin CSV export | Done | Date-range export |
| **16** Occupancy report | Done | `bookings:occupancy` + unit tests |
| **17** Performance | Done | Indexes; eager-load `hairdresser` in admin/API |
| **Docker** (stretch) | Done | Compose, Makefile, Mailpit, `.env.docker` |
| **7** Client portal | **Skipped** | Scope vs. Sanctum list API + admin (documented below) |

---

## Architecture

### Domain

- `hairdressers` with `is_active`; bookings FK to `hairdresser_id`.
- Unique `(hairdresser_id, scheduled_at)` — overbooking is per stylist, not global.
- Reference stylists via **`HairdresserSeeder`** (not data migrations).

### Booking pipeline

```
HTTP (web / API) → Form Request → CreateBookingData → BookingService
                              ↓
     BookingAvailabilityChecker + DB transaction + lockForUpdate
                              ↓
                    BookingCreated → queued emails
```

- **409** `booking_conflict` · **422** business validation · **429** rate limit with `Retry-After`.
- Idempotency-Key caches successful POST responses (24h, `config/booking.php`).

### Security

- Public: `POST /api/bookings`, `GET /api/availability`.
- Protected: `GET /api/bookings` — Sanctum, scoped to authenticated user email.
- Admin: session auth + existing dashboard gate.
- Rate limits: 20/min bookings, 120/min availability.

### UX

| Surface | Details |
|---------|---------|
| Public wizard | 3 steps; stylist cards; week view; **5s** slot polling |
| Admin dashboard | Search/sort bookings, CSV export, stylist CRUD, **modal** reuses `BookingWizard` (`embedded=true`) |

Admin features are **web-only** (documented in OpenAPI info block, not REST endpoints).

---

## How to run

### Quick start

```bash
git clone https://github.com/HthePaul2/DevChallenge.git
git checkout paul-hosu-dev
make setup
```

| Service | URL / credentials |
|---------|-------------------|
| App | http://localhost:8000 |
| Admin | `hairdresser@example.com` / `password` |
| Mailpit | http://localhost:8025 |
| MySQL (host) | `localhost:3307` · DB `hairdresser` · user `devchallenge` / `secret` |

Run Laravel commands **inside Docker**: `make test`, `make migrate`, `make seed`.

### Tests

```bash
docker compose exec -T mysql mysql -uroot -prootsecret -e \
  "CREATE DATABASE IF NOT EXISTS hairdresser_test; GRANT ALL ON hairdresser_test.* TO 'devchallenge'@'%';"
make test
make pint
make analyse   # PHPStan level 5
```

### API

- Spec: `docs/openapi.yaml`
- Postman: `docs/postman/DevChallenge-Booking.postman_collection.json`

```bash
curl -s 'http://localhost:8000/api/availability?hairdresser_id=1&week_start=2026-06-29&week_end=2026-07-05'
curl -s -X POST http://localhost:8000/api/bookings \
  -H 'Content-Type: application/json' -H 'Idempotency-Key: demo-1' \
  -d '{"name":"Jane","email":"jane@example.com","hairdresser_id":1,"date":"2026-06-30","start_time":"11:00"}'
```

---

## Git workflow (for reviewers)

Development followed **task order** with integration branches merged into `paul-hosu-dev` using **`--no-ff`**. Each epic splits into smaller branches first (reviewable slices), then merges into its integration branch, then into `paul-hosu-dev`.

> **Note:** Git refs cannot use both `feature/admin-dashboard` and `feature/admin-dashboard/foo` as branches simultaneously, so sub-branches use hyphenated names (e.g. `feature/admin-dashboard-table-sort-filter`) while merge commits document the hierarchy.

### Merge order on `paul-hosu-dev`

| Step | Integration branch | Sub-branches (merged first) |
|------|-------------------|------------------------------|
| 1 | `feature/setup/docker-local` | `docker-compose`, `docker-makefile`, `docker-ci` |
| 2 | `feature/domain-hairdresser` | `domain-hairdresser-schema`, `domain-hairdresser-seeders` |
| 3 | `feature/booking-service` | `booking-service-layer`, `booking-service-api`, `booking-service-concurrency`, `booking-service-artisan-list` |
| 4 | `feature/sanctum-api` | `sanctum-api-list` |
| 5 | `feature/livewire-booking-wizard` | `livewire-booking-wizard-theme`, `livewire-booking-wizard-component` |
| 6 | `feature/admin-reports` | `admin-reports-csv`, `admin-reports-occupancy`, `admin-reports-dashboard` |
| 7 | `feature/emails-queue` | — |
| 8 | `feature/devops-hardening` | `devops-hardening-test-db`, `devops-hardening-mailpit-seed`, `devops-hardening-auth` |
| 9 | `feature/admin-dashboard` | `admin-dashboard-stylist-management`, `admin-dashboard-booking-modal`, `admin-dashboard-table-sort-filter` |
| 10 | `feature/api-docs` | `api-docs-openapi`, `api-docs-submission` |
| 11 | `feature/quality-hardening` | `quality-hardening-concurrency-tests`, `quality-hardening-phpstan`, `quality-hardening-docs` |

### Useful commands

```bash
# Full merge graph on the PR branch (feature names appear in merge commits)
git log --oneline --graph upstream/main..paul-hosu-dev

# Files changed vs upstream
git diff --stat upstream/main..paul-hosu-dev

# Inspect one epic (example: booking service)
git log --oneline --grep "feature/booking-service" upstream/main..paul-hosu-dev
```

### Remote branches vs local history

Only **`paul-hosu-dev`** needs to exist on GitHub for review. Integration branches (e.g. `feature/admin-dashboard`) were merged with `--no-ff` into `paul-hosu-dev`; **refs are not pushed** after merge — standard practice (same as deleting remote feature branches after a merged PR). Reviewers see the nested merge structure in the **PR commit graph**, not as separate open branches.

---

## Engineering notes

### Test database isolation

PHPUnit always targets `hairdresser_test` (`CreatesApplication` + `phpunit.xml`). `TestCase` aborts if the active database name does not end with `_test`, preventing `RefreshDatabase` from wiping the dev database when a host `.env` overrides test config.

### Conflict handling (UI + API + Postman)

- **API:** duplicate slot → HTTP **409** `{ "error": "booking_conflict" }` (`Api/BookingController` catches `BookingConflictException`).
- **Livewire wizard:** same exception → user-visible error on `selectedHour`: *“This time slot has just been booked. Please choose another.”* + slot list refresh (`BookingWizard::confirmBooking`).
- **Legacy web form:** redirect back with error on `hour` field (`BookingController::store`).
- **DB last line of defence:** unique index `(hairdresser_id, scheduled_at)` + transaction/`lockForUpdate` in `BookingService`.
- **Tests:** `BookingConflictTest`, `BookingConcurrencyTest`, `BookingWizardConflictTest`.

### Quality toolchain

- **25 PHPUnit tests** (feature + unit), including concurrency and Livewire conflict cases.
- **PHPStan level 5** (Larastan) in CI and `make analyse`.

### Seed strategy

- **`HairdresserSeeder`** — three reference stylists (source of truth).
- **`BookingDemoSeeder`** — idempotent demo bookings when table is empty.
- **`make migrate`** chains seed; Docker entrypoint runs `DatabaseSeeder` on start.

### Intentionally skipped

- **Task 7 (client history UI):** Sanctum list API + admin dashboard cover the use case without expanding scope into a second portal.

### Scope note (for reviewers)

The task menu recommends 2–4 items; this submission covers more because the chosen tasks chain naturally (domain → API → auth → UX → admin → ops). Each slice is small and reviewable via nested merges. I used AI assistants for boilerplate and iteration, but **reviewed every change**, fixed incidents (test DB isolation, UI conflict feedback), and can walk through any file in the evaluation call.

### Assumptions

| Topic | Choice |
|-------|--------|
| Slots | 1 hour, Mon–Fri 08:00–17:00 |
| Real-time UI | Livewire polling (5s), not WebSockets |
| Email in dev | `QUEUE_CONNECTION=sync` + Mailpit |
| Local tooling | `.cursor`, `.squad`, `.codegraph` in `.gitignore` — not part of submission |

---

## Suggested review order

1. Read this file + skim `docs/openapi.yaml`
2. `make setup && make test`
3. Public flow: http://localhost:8000 → complete wizard
4. Admin: login → add stylist → **+ New booking** modal → export CSV
5. Walk epics in order: `feature/domain-hairdresser` → `feature/booking-service` → `feature/admin-dashboard`

---

## Contact

**Paul Hosu** — hosupaul16@gmail.com — GitHub: **HthePaul2**
