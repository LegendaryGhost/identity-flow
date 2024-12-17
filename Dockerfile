FROM php:8.1

# Install dependencies
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    unzip \
    git \
    && docker-php-ext-install pdo_pgsql zip

# Set working directory
WORKDIR /var/www/html

# Copy application code
COPY . .

RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Install Composer
COPY --from=composer:2.8.3 /usr/bin/composer /usr/bin/composer

RUN composer install --no-dev --optimize-autoloader

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
