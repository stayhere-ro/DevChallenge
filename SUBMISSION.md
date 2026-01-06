# Submission

## Overview

I have chosen and completed Tasks **1–11** to challenge myself and get hands-on experience with Laravel (routing/controllers, validation, services/DTOs, testing, documentation, and CI).

This application implements a simple hairdresser booking flow with WEB routes and API endpoints, validation (including business rules), tests, API documentation (OpenAPI), and a GitHub Actions workflow that runs tests, static analysis and code styling (Pint).

---

## Tasks completed (1–11)

> Reason for selecting 1–11: I wanted to complete the full set to practice a realistic delivery workflow (feature work + tests + docs + CI).

### Tasks 1–9: Application features & tests

-   Implemented the booking flow:
    -   create bookings via API (with service/DTO separation)
    -   list bookings by client email
-   Added validation at two levels:
    -   request validation (required fields, formats, existence checks)
    -   business-rule validation (e.g. weekends, working hours, conflicting slots)
-   Standardized API responses using appropriate HTTP status codes (e.g. 201 on create, 422 on validation errors), with consistent JSON payloads.
-   Added PHPUnit feature tests for the booking flow, covering API responses, business rules, and email sending.
-   Updated seeders/factories to support reproducible local testing and CI runs.

### Task 10: API documentation

-   Added **OpenAPI spec** describing booking endpoints and error responses:
    -   `docs/openapi.yaml`

### Task 11: Continuous Integration (CI)

-   Added a **GitHub Actions** workflow that runs on every push / pull request:
    -   installs PHP dependencies
    -   installs Node dependencies + builds Vite assets
    -   runs DB migrations + seed
    -   runs Pint (style), PHPStan (static analysis), and tests
-   Workflow file:
    -   `.github/workflows/ci.yml`

---

## Assumptions

-   **PHP version**: 8.2 (requiring a minimum level of 8.1).
-   **Frontend assets**: built using vite.
-   **Database**: Local development uses **MySQL**. CI also uses a MySQL service container.
-   **API scope**: OpenAPI docs focuses on the **booking API endpoints** (the routes defined in `routes/api.php`) and the standard validation error shape.

---

## Trade-offs

-   CI uses MySQL to match local development behavior.
-   Static analysis uses **PHPStan via Larastan** (instead of Psalm) to align with Laravel conventions and keep setup minimal.
-   I chose to complete **Tasks 1–11** to deliver a full, end-to-end solution (features + tests + docs + CI) instead of stopping at partial functionality.

---

## How to run

Full setup instructions are documented in **README.md** / **DOCUMENTATION.md**.

Requirements:

-   PHP **8.1+**
-   Composer
-   Node.js 20+ / npm
-   MySQL server

Windows convenience scripts (`setup.bat`, `run.bat`) are included (optional).

---

## How to test and verify

Run all the tests

```bash
php artisan test
```

Run Pint (styling)

```bash
./vendor/bin/pint --test
```

Run PHPStan (static analysis)

```bash
./vendor/bin/phpstan analyse
```

CI

GitHub Actions runs automatically on every push/PR using `.github/workflows/ci.yml`.
