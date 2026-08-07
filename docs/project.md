# Project brief

## Goal

Deliver a public Laravel application that imports Mexico's 32 states from INEGI, lists them, and shows the municipalities for a selected state.

## Scope

| Area | Choice |
| --- | --- |
| Application | Laravel 13 in `laravel/`, Vue 3, Bootstrap 5, MySQL 8.4 |
| Stored data | English-named `states` table |
| Municipality data | Read from INEGI when selected and show inline; do not persist yet |
| State listing | Bootstrap-styled DataTables with pagination, search, ordering, and formatted population |
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
