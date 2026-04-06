FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    git curl zip unzip libpng-dev libonig-dev libxml2-dev

RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . /var/www

# create env first
RUN cp .env.example .env

RUN composer install --no-interaction --prefer-dist --optimize-autoloader --no-scripts

RUN php artisan key:generate

RUN chown -R www-data:www-data /var/www
RUN chmod -R 775 storage bootstrap/cache

CMD ["php-fpm"]
