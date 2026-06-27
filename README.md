# Hairdresser Booking System — DevChallenge

Laravel 10 app for hairdresser appointment booking (Code Studio technical challenge).

### Documentation

- [PROJECT_OVERVIEW.md](PROJECT_OVERVIEW.md) — project description, business rules, stack
- [todo.md](todo.md) — original task menu and evaluation criteria
- **[SUBMISSION.md](SUBMISSION.md)** — what was implemented, how to run, architecture notes
- [docs/openapi.yaml](docs/openapi.yaml) — OpenAPI 3 spec
- [docs/postman/DevChallenge-Booking.postman_collection.json](docs/postman/DevChallenge-Booking.postman_collection.json)

### Quick start (Docker)

```bash
make setup
# App: http://localhost:8000
# Admin: hairdresser@example.com / password
```

```bash
make test     # PHPUnit (creates hairdresser_test DB if needed)
make pint     # code style
make analyse  # PHPStan level 5
```
