#!/bin/sh
set -eu

# Install dependencies if missing (works with named volume for /vendor)
if [ ! -f "vendor/autoload.php" ]; then
  echo "[entrypoint] Installing composer dependencies..."
  composer install --no-interaction --prefer-dist
fi

# Ensure runtime dirs exist (cache/log)
mkdir -p var/cache var/log

exec "$@"



