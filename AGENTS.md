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
- Validate all request input; whitelist sortable columns; use finite outbound HTTP timeouts.
- Never commit `.env`, credentials, generated dependencies, or build output.

## UI and verification

- Vue is the default interface. Keep only the minimal Blade Vite mount shell; do not add Blade screens.
- Prefer Bootstrap and existing dependencies over new UI libraries.
- After relevant changes, run `docker compose config --quiet`, `docker compose up -d --wait`, and `npm run build` from `laravel/`.
