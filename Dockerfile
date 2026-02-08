FROM php:8.2-apache

# 0) Apache: habilitar rewrite y FIX de MPM (dejar SOLO prefork)
RUN a2enmod rewrite \
 && a2dismod mpm_event mpm_worker mpm_prefork || true \
 && a2enmod mpm_prefork \
 && apache2ctl -t

# 1) Dependencias del sistema + extensiones PHP
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    unzip \
    postgresql-client \
 && docker-php-ext-configure gd --with-freetype --with-jpeg \
 && docker-php-ext-install pdo pdo_mysql pdo_pgsql zip gd bcmath \
 && apt-get clean && rm -rf /var/lib/apt/lists/*

# 2) Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 3) App
WORKDIR /var/www/html
COPY . .

# 4) Instalar dependencias Laravel
RUN composer install --no-dev --optimize-autoloader --no-interaction

# 5) Permisos (Laravel)
RUN chown -R www-data:www-data /var/www/html \
 && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# 6) Apache DocumentRoot => /public (bien hecho)
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
 && sed -ri 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# 7) Entrypoint (caches solo si hay .env y APP_KEY)
RUN printf '%s\n' \
'#!/bin/sh' \
'set -e' \
'' \
'cd /var/www/html' \
'' \
'if [ ! -f .env ]; then' \
'  if [ -f .env.example ]; then cp .env.example .env; fi' \
'fi' \
'' \
'# Si no hay APP_KEY, no caches (para evitar crash en deploy)' \
'if [ -f .env ] && php -r "exit(env(\"APP_KEY\") ? 0 : 1);" ; then' \
'  php artisan config:cache || true' \
'  php artisan route:cache || true' \
'  php artisan view:cache || true' \
'else' \
'  echo \"APP_KEY no definido o .env faltante: omitiendo caches.\"' \
'fi' \
'' \
'exec docker-php-entrypoint apache2-foreground' \
> /usr/local/bin/docker-entrypoint.sh \
&& chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 80
ENTRYPOINT ["docker-entrypoint.sh"]
