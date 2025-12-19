**Interview Homework — Choose Your Tasks**

Goal:
- Treat this as a short take‑home assignment. You have a few days to complete it.
- Pick the tasks you find most relevant to your strengths. You can choose multiple tasks (recommended 2–4) across levels below.
- Quality, reasoning, and tests matter more than quantity.

Submission:
- Fork or branch this repo and implement your changes.
- Add a short "What I did" section to README or create `SUBMISSION.md` describing:
  - Which tasks you selected and why
  - Assumptions and trade‑offs
  - How to run, test, and verify your work
- Open a PR or share a patch/repo link.

Timebox: a few days. Aim for focused, incremental, well‑tested work.

Evaluation criteria:
- Correctness and adherence to business rules
- Code quality (readability, structure, naming, consistency)
- Tests and edge‑case handling
- Git hygiene (commits, messages)
- Clear reasoning documented in your notes

Task menu (pick any):

Level 1 — Core API and Validation
1. Create a basic booking endpoint
   - POST `/api/bookings`
   - Accept: client email, hairdresser ID, date, start time
   - Validate: required fields, email format, business rules (no weekends, business hours, one per hour)
   - Return JSON: success/error with clear messages
2. Prevent overbooking
   - Ensure no overlapping bookings for the same hairdresser and time slot
   - Add feature tests covering successful booking and conflicts

Level 2 — Tooling and UX
3. Seeder and factories
   - Seed the DB with realistic fake data (multiple hairdressers and bookings)
   - Make it easy to reset and reseed
4. Artisan command
   - `php artisan bookings:list {email}`
   - Output a table with date, time, and hairdresser ID for the given email
5. Basic API listing
   - GET `/api/bookings?email=...` to list a client's bookings

Level 3 — Enhancements (pick any)
6. Email notifications
   - Notify hairdresser on new booking
   - Send confirmation email to client on success
7. Client account & history
   - Allow user registration/login
   - Show personal booking history (below the booking form or in a dashboard)
   - Optional: paging, search, filter by date/hour
8. Calendar view or improved UI/UX for booking
   - Visualize available slots; disable weekends and occupied times

Level 4 — Architecture & Quality
9. Service layer and validation hardening
   - Extract booking creation logic into a dedicated service (with a simple DTO)
   - Use Form Requests for validation; return consistent error schema
   - Add unit/feature tests for the service and validation
10. API documentation
   - Provide an OpenAPI/Swagger spec for the booking endpoints and errors
   - Include a Postman/Insomnia collection or `docs/openapi.yaml`
11. Continuous Integration (CI)
   - Add a GitHub Actions workflow running tests, static analysis (phpstan/psalm), and code style (pint)

Level 5 — Scalability & Resilience
12. Idempotency and rate limiting
   - Support `Idempotency-Key` for POST `/api/bookings` to avoid duplicate bookings on retries
   - Add sensible rate limits and return 429 with retry hints
13. Async processing for emails
   - Dispatch emails to the queue; configure retry with backoff and dead-letter handling
   - Expose/administer failed jobs locally (e.g., `horizon` or default failed_jobs)
14. Concurrency-safe bookings
   - Ensure race conditions cannot create double bookings (transaction + unique index/lock)
   - Add tests simulating concurrent requests

Level 6 — Data & Reporting
15. Admin export
   - Export bookings to CSV for a selected date range
   - Include headers and proper formatting for date/time
16. Occupancy/throughput report
   - Simple daily/weekly occupancy summary by hairdresser
   - Return via command or API; include tests for calculations
17. Performance & indexing
   - Add DB indexes for frequent queries; measure impact
   - Detect/fix N+1 issues in dashboard/listing

Stretch Ideas (optional)
- Cancellation/reschedule policy with constraints
- Rate limiting or idempotency for booking endpoint
- Observability (structured logs, simple metrics)
- Docker Compose for local dev
- Security headers and basic audit (e.g., Laravel Security advisories)
- Load testing (k6/Artillery) with a short report on limits and bottlenecks
- Git hooks or Makefile to streamline common tasks (test, lint, fix)
- Accessibility pass on the booking UI (labels, contrast, keyboard nav)

Notes
- This is a demo: we do not expect a complete product. We want to understand your thinking, how you structure code, and how you test.
- Prefer small, clean commits, explain decisions briefly, and adhere to best practices already used in the project.



