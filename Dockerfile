FROM php:8.2-apache

# PostgreSQL + dependencias PHP
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    unzip \
    postgresql-client \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql zip gd bcmath

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . .

# Composer install
RUN composer install --no-dev --optimize-autoloader --no-interaction

# ✅ .env COMPLETO con PostgreSQL
RUN cp .env.example .env && \
    sed -i 's/DB_CONNECTION=sqlite/DB_CONNECTION=pgsql/' .env && \
    sed -i 's|DB_DATABASE=.*|DB_DATABASE=dbedu_f16g|' .env && \
    sed -i 's|DB_HOST=.*|DB_HOST=dpg-d641lt0gjchc739dj6i0-a|' .env && \
    sed -i 's|DB_PORT=.*|DB_PORT=5432|' .env && \
    sed -i 's|DB_USERNAME=.*|DB_USERNAME=root|' .env && \
    sed -i 's|DB_PASSWORD=.*|DB_PASSWORD=Kidiz9jgj7NKkzQ5WvCTVXLPnrK9iXVL|' .env

# Permisos
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage /var/www/html/bootstrap/cache

# Laravel optimize (SIN config:cache que usa DB)
RUN php artisan key:generate --force

# Apache config Laravel
RUN a2enmod rewrite \
    && echo '<Directory /var/www/html/public>' >> /etc/apache2/conf-available/laravel.conf \
    && echo '    AllowOverride All' >> /etc/apache2/conf-available/laravel.conf \
    && echo '    Require all granted' >> /etc/apache2/conf-available/laravel.conf \
    && echo '</Directory>' >> /etc/apache2/conf-available/laravel.conf \
    && a2enconf laravel \
    && sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

EXPOSE 8080
CMD ["apache2-foreground"]
