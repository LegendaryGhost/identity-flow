FROM php:8.2-cli

# Installer les dépendances nécessaires
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    unzip \
    git \
    && docker-php-ext-install pdo_pgsql zip

# Définir le répertoire de travail
WORKDIR /var/www/html

# Installer Composer
COPY --from=composer:2.8.3 /usr/bin/composer /usr/bin/composer

# Copier le code de l'application
COPY . .

RUN git config --global --add safe.directory /var/www/html

# Installer les dépendances Laravel
RUN composer install --no-dev --optimize-autoloader --prefer-dist

# Configurer les permissions pour le stockage et le cache de Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Exposer le port 8000
EXPOSE 8000

# Démarrer Laravel avec le script pour attendre PostgreSQL
CMD ["sh", "-c", "php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=8000"]
