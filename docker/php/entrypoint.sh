#!/bin/sh
set -e
cd /var/www
php artisan migrate --force --no-interaction
exec docker-php-entrypoint "$@"
