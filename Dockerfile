FROM php:8.2-fpm

# تثبيت المتطلبات
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    curl \
    git \
    libzip-dev \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# تثبيت Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# مجلد المشروع
WORKDIR /var/www

# نسخ ملفات المشروع
COPY . .

# تحميل الباكج
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# إعطاء صلاحيات
RUN chown -R www-data:www-data /var/www

# تشغيل Laravel
CMD php artisan serve --host=0.0.0.0 --port=8080
# تأكد من صلاحيات الكتابة على مجلد storage و bootstrap/cache
RUN chmod -R 775 storage bootstrap/cache && \
    chown -R www-data:www-data storage bootstrap/cache
