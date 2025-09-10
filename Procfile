web: vendor/bin/heroku-php-apache2 public/
release: php artisan migrate --force && php artisan config:clear && php artisan cache:clear && php artisan config:cache
