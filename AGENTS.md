# Engineering guide

## Repository layout

- `laravel/` is the Laravel application.
- Root `docker-compose.yml` is the local runtime contract.
- `tasks/` holds local PRD/Ralph workflow artifacts and is intentionally ignored.
- `docs/project.md` defines scope, setup, and deployment. `docs/architecture.md` defines design and diagrams. Do not duplicate content across them.

## Implementation rules

- Keep the public site unauthenticated unless the requirement changes.
- Use English plural database table names: `states`, then `municipalities` only when municipality storage is required.
- Preserve geo keys as strings; `state_code` must retain leading zeroes.
- Treat INEGI responses as untrusted. Read records from `datos`, validate/map the required fields, and do not persist raw payloads or metadata.
- State import must be idempotent: unique index on `states.state_code` plus an upsert.
- Define database tables, constraints, timestamp behavior, and lifecycle fields in the architecture ER diagrams; treat them as the schema authority.
- Validate all request input; whitelist sortable columns; use finite outbound HTTP timeouts.
- Never commit `.env`, credentials, generated dependencies, or build output.
- Follow Laravel conventions: use migrations, Eloquent models/scopes, single-purpose services, Artisan commands, controllers, Form Requests at HTTP boundaries, and readonly DTOs for consumed external response shapes. Do not add a repository abstraction over Eloquent without a concrete need.
- Commit subjects use `<type>: <imperative summary>` with no scope, task identifier, body, or footer.

## UI and verification

- Vue is the default interface. Keep only the minimal Blade Vite mount shell; do not add Blade screens.
- Prefer Bootstrap and existing dependencies over new UI libraries.
- Keep tests concise and behavior-focused: cover the happy path and one meaningful failure path. Use data providers for equivalent input variations, constants for routes and repeated values, and remove redundant cases.
- Mirror application paths in tests after the test type; for example, `app/Models/State.php` maps to `tests/Unit/Models/StateTest.php`.
- Keep test API fixtures independent from production DTO field constants so they can detect an incorrect external-field mapping.
- After relevant changes, run `docker compose config --quiet`, `docker compose up -d --wait`, and `npm run build` from `laravel/`.
