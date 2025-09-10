#!/bin/bash
set -e

echo "🚀 Running migrations and cache clear..."
php artisan migrate --force || true
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan key:generate --force

echo "🚀 Starting Laravel server..."
php artisan serve --host=0.0.0.0 --port=8080
