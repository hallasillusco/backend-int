#!/usr/bin/env bash
set -eux
composer install --no-dev --optimize-autoloader
php artisan optimize:clear || true
php artisan storage:link || true
php artisan config:cache
php artisan route:cache || true
php artisan view:cache || true