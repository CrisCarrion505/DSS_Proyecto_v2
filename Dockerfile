FROM php:8.2-apache

# 1) Apache FIX (Railway-proof): dejar SOLO 1 MPM (prefork) + rewrite
RUN set -eux; \
    a2dismod mpm_event mpm_worker mpm_prefork || true; \
    rm -f /etc/apache2/mods-enabled/mpm_event.load /etc/apache2/mods-enabled/mpm_worker.load /etc/apache2/mods-enabled/mpm_prefork.load || true; \
    a2enmod mpm_prefork rewrite; \
    apache2ctl -t

# 2) Dependencias del sistema + extensiones PHP
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

# 3) Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 4) App
WORKDIR /var/www/html
COPY . .

# 5) Dependencias Laravel
RUN composer install --no-dev --optimize-autoloader --no-interaction

# 6) Permisos Laravel
RUN chown -R www-data:www-data /var/www/html \
 && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# 7) DocumentRoot a /public (sin reemplazos peligrosos)
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
 && sed -ri 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

RUN a2dismod mpm_event mpm_worker && a2enmod mpm_prefork

# 8) Entrypoint (Railway: evita crashear si falta APP_KEY)
RUN printf '%s\n' \
'#!/bin/sh' \
'set -e' \
'cd /var/www/html' \
'' \
'# Railway setea env vars en runtime; .env puede no existir y está bien' \
'if [ -f .env ] || [ -n "$APP_KEY" ]; then' \
'  php artisan config:cache || true' \
'  php artisan route:cache || true' \
'  php artisan view:cache || true' \
'else' \
'  echo "No .env y no APP_KEY: omitiendo caches."' \
'fi' \
'' \
'exec docker-php-entrypoint apache2-foreground' \
> /usr/local/bin/docker-entrypoint.sh \
&& chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 80
ENTRYPOINT ["docker-entrypoint.sh"]
