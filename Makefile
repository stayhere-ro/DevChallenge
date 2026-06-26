.PHONY: help build up down restart logs shell install env assets setup migrate seed test fresh pint analyse

COMPOSE = docker compose
APP = $(COMPOSE) exec -T app

help:
	@echo "DevChallenge — comenzi locale (Docker)"
	@echo "  make build    — build imagini"
	@echo "  make up         — pornire containere"
	@echo "  make down       — oprire"
	@echo "  make install    — composer install"
	@echo "  make assets     — npm install + build (Vite, pe host)"
	@echo "  make env        — copiază .env.docker → .env + APP_KEY"
	@echo "  make setup      — install + env + migrate + seed + assets (recomandat)"
	@echo "  make migrate    — migrate + seed în container (împreună)"
	@echo "  make seed       — doar db:seed (dacă ai nevoie separat)"
	@echo "  make test       — php artisan test"
	@echo "  make analyse    — PHPStan static analysis"
	@echo "  make fresh      — migrate:fresh + seed"
	@echo "  make shell      — bash în container app"

build:
	$(COMPOSE) build

up:
	$(COMPOSE) up -d

down:
	$(COMPOSE) down

restart: down up

logs:
	$(COMPOSE) logs -f

shell:
	$(COMPOSE) exec app bash

install:
	$(APP) composer install --no-interaction --prefer-dist

assets:
	npm install
	npm run build

env:
	@test -f .env.docker || (echo "Lipsește .env.docker"; exit 1)
	cp .env.docker .env
	$(APP) php artisan key:generate

migrate:
	$(APP) php artisan migrate --force
	$(APP) php artisan db:seed --force

migrate-only:
	$(APP) php artisan migrate --force

seed:
	$(APP) php artisan db:seed --force

setup: build up install env migrate seed assets
	@echo ""
	@echo "✓ App: http://localhost:8000"
	@echo "✓ Mailpit: http://localhost:8025"
	@echo "✓ MySQL (host): localhost:3307 — DB: hairdresser"

test-db:
	-docker compose exec -T mysql mysql -uroot -prootsecret -e "CREATE DATABASE IF NOT EXISTS hairdresser_test; GRANT ALL ON hairdresser_test.* TO 'devchallenge'@'%';" 2>/dev/null || true

test: test-db
	$(APP) php artisan test

fresh:
	$(APP) php artisan migrate:fresh --seed --force

pint:
	$(APP) ./vendor/bin/pint --test

analyse:
	$(APP) ./vendor/bin/phpstan analyse --no-progress --memory-limit=512M
