#!/bin/bash

# استنى قاعدة البيانات لحد ما تجهز
until php artisan migrate --force; do
  echo "⏳ Waiting for database..."
  sleep 5
done

php artisan config:cache
php artisan serve --host=0.0.0.0 --port=8080

