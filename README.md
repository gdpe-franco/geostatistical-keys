# Geostatistical Keys

Public Laravel application for browsing Mexico's federal entities and their
municipalities using the INEGI Geo Catalog.

## Demo

- [Public demo](https://geostatistical-keys-production-5bxwnm.laravel.cloud)
- [Source repository](https://github.com/gdpe-franco/geostatistical-keys)
- Review environment planned shutdown: **2026-09-06** (unless the review is extended)

The site is public and intentionally has no login or registration flow.

## Features

- Idempotent import of the 32 Mexican federal entities from INEGI.
- Searchable, sortable, paginated Bootstrap DataTable.
- Inline municipality tables fetched from INEGI on selection.
- One-day browser cache for municipality responses.
- Persisted light/dark color preference.

## Stack

Laravel 13, Vue 3, Pinia, Bootstrap 5, MySQL 8.4, Docker Compose, GitHub
Actions, and Laravel Cloud.

## Local setup

Docker Compose is the local development environment. From the repository root:

```bash
docker compose up -d --build --wait
```

Open `http://localhost:8080`.

On continued work after adding a migration:

```bash
docker compose exec app php artisan migrate
```

For live Vue updates:

```bash
docker compose exec app npm run dev
```

Vite runs on port 5173, but the application remains available through port
8080.

## Import states

Import or refresh the persisted states explicitly:

```bash
docker compose exec app php artisan states:import
```

The command reads INEGI's `/mgee` catalog and upserts records by state code, so
it is safe to run repeatedly. Municipalities are not stored; the application
retrieves them from INEGI only when a state is selected.

## Quality checks

Run from `laravel/`:

```bash
composer lint
composer analyse
npm run lint
php artisan test
npm run build
```

GitHub Actions runs the same checks for pull requests and changes to `main`.

## Deployment

The public environment runs on Laravel Cloud with private managed MySQL.
Deployments are currently triggered manually from Laravel Cloud. After the
first deployment, run `php artisan states:import` from Laravel Cloud's Commands
tab to populate the catalog; it is deliberately not part of the deployment
command so an INEGI outage cannot block a release.

See [project documentation](docs/project.md) and
[architecture documentation](docs/architecture.md) for setup and design
decisions.
