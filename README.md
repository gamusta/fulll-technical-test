# Fulll technical test

- [`algo/`](algo) — FizzBuzz
- [`backend/`](backend) — vehicle fleet parking management (DDD / CQRS)

PHP 8.3, one Composer package per exercise. Built with AI assistance (Claude Code / Cursor).

## Run

Requirements: Docker + Make.

```sh
make up        # PHP image + Postgres + schema
make install   # composer install (both packages)
make qa        # code style, static analysis, tests (both packages)
make down
```

## CLI

```sh
docker compose run --rm -w /app/backend php sh
./fleet create <userId>
./fleet register-vehicle <fleetId> <vehiclePlateNumber>
./fleet localize-vehicle <fleetId> <vehiclePlateNumber> <lat> <lng> [alt]
```

Assumption: a vehicle's location is tracked per fleet.

## Code quality and CI/CD

- **PHP-CS-Fixer** (PER-CS 2.0): consistent style, enforced automatically.
- **PHPStan** (level max): type errors caught before runtime.
- **PHPUnit + Behat**: domain rules and business scenarios, in memory and on Postgres.
- **CI** ([GitHub Actions](.github/workflows/ci.yml)): `composer qa` per package on every push/PR, with a Postgres service.
- **CD** (not implemented): build a versioned Docker image, run migrations, deploy to staging, then to production after approval.
