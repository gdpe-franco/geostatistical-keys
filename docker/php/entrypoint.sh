#!/bin/sh
set -eu

[ -f .env ] || cp .env.example .env
[ -f vendor/autoload.php ] || composer install --no-interaction
grep -q '^APP_KEY=$' .env && php artisan key:generate --force
[ -d node_modules ] || npm ci
[ -f public/build/manifest.json ] || npm run build
php artisan migrate --force --no-interaction

exec "$@"
