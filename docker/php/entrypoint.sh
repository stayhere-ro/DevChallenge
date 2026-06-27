#!/bin/sh
set -e

cd /var/www

php artisan migrate --force --no-interaction
# HairdresserSeeder: 3 stylists (upsert). BookingDemoSeeder: demo rows if table empty.
php artisan db:seed --force --no-interaction

exec docker-php-entrypoint "$@"
