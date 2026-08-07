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

## Milestones

1. Add the `states` migration, INEGI client, and idempotent import command.
2. Build the states table and municipality view.
3. Test and deploy the demo.

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

Laravel Cloud is the selected production platform. Its provisioning and
automatic deployment remain Phase 5 work; the workflow above is only the
quality gate.
