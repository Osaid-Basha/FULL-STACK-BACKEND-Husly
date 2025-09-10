#!/bin/bash

# استنى قاعدة البيانات لحد ما تجهز
until php artisan migrate --force; do
  echo "⏳ Waiting for database..."
  sleep 5
done

php artisan config:cache
vendor/bin/heroku-php-apache2 public/
