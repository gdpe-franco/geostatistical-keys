# Project brief

## Goal

Deliver a public Laravel application that imports Mexico's 32 states from INEGI, lists them, and shows the municipalities for a selected state.

## Scope

| Area | Choice |
| --- | --- |
| Application | Laravel 13 in `laravel/`, Vue 3 + Pinia, Bootstrap 5, MySQL 8.4 |
| Stored data | English-named `states` table |
| Municipality data | Read from INEGI when selected, then cached in browser storage for one day; never persisted to MySQL |
| Color preference | Pinia persists the selected light or dark mode in browser storage |
| State listing | Bootstrap-styled DataTables with code, name, INEGI short name, formatted population, pagination, search, and ordering |
| Responsive design | Bootstrap-responsive layout and tables from small mobile to desktop |
| Import access | Artisan command |
| Access | One public web page; no login or registration flow |
| Delivery | Source repository and a browsable URL |

## Local development

```bash
docker compose up -d --build --wait
```

On a fresh checkout, Compose creates the local environment file, installs locked PHP/Node dependencies, builds Vue assets, applies migrations, and waits until Laravel is healthy. Open the application at `http://localhost:8080`.

After adding a new migration during continued work, run `docker compose exec app php artisan migrate`.

For live Vue updates during active frontend work, run `docker compose exec app npm run dev`; port `5173` is Vite's asset-development server, not the application URL. The local environment file is ignored by Git.

## Deployment

GitHub Actions validates pull requests to `main` and pushes to `main` with the
same quality commands used locally. It uses the in-memory SQLite test
configuration from `phpunit.xml`; no deployment or production database
credentials are available to the workflow.

Run the equivalent checks from `laravel/` before opening a pull request:

```bash
composer lint
composer analyse
npm run lint
php artisan test
npm run build
```

Laravel Cloud hosts the public production environment. GitHub Actions is the
quality gate for `main`; deployments are intentionally triggered manually from
Laravel Cloud while the demo is evaluated.

### Laravel Cloud production build

This repository is a monorepo for deployment purposes. Select `laravel/` as
the Laravel Cloud application root; the root-level Compose and Docker files are
local-development infrastructure only.

Set the PHP runtime to 8.4. The application already exposes Laravel's native
health endpoint at `/up`, which must not depend on INEGI.

Use this build command in the environment's deployment settings:

```bash
composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader
npm ci --audit false
npm run build
```

After the private MySQL resource is attached, use this deploy command:

```bash
php artisan migrate --force --no-interaction
```

### Laravel Cloud environment variables

Create `APP_KEY` as a Laravel Cloud secret and link it only to the production
environment. Generate its value locally with `php artisan key:generate --show`;
do not add it to this repository or share it in issue trackers, chat, or build
logs.

Laravel Cloud injects `APP_NAME`, `APP_ENV`, `APP_DEBUG`, `APP_URL`,
`LOG_CHANNEL=laravel-cloud-socket`, `SESSION_DRIVER=cookie`, and the attached
MySQL connection values. Keep those values. The cookie session driver does not
authenticate the public API and does not require a database session table.

Add only these custom production environment variables:

| Variable | Value | Reason |
| --- | --- | --- |
| `QUEUE_CONNECTION` | `sync` | No queue worker is in scope |
| `LOG_LEVEL` | `warning` | Keep the hosted demo logs concise |

Do not override the injected `DB_CONNECTION`, `CACHE_STORE`, `DB_HOST`,
`DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, or `DB_PASSWORD` values. Keep the
MySQL public endpoint disabled; it is not required by the application.
