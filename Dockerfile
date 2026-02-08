FROM php:8.2-apache

# 1. Instalar dependencias del sistema
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

# 2. FIX APACHE: Desactivar módulos conflictivos (ESTO ARREGLA TU ERROR)
RUN a2dismod mpm_event || true \
    && a2dismod mpm_worker || true \
    && a2enmod mpm_prefork \
    && a2enmod rewrite

# 3. Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . .

# 4. Instalar dependencias de Laravel
RUN composer install --no-dev --optimize-autoloader --no-interaction

# 5. Permisos
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage /var/www/html/bootstrap/cache

# 6. Configurar Apache DocumentRoot
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# 7. Entrypoint (El script de arranque)
RUN echo '#!/bin/sh' > /usr/local/bin/docker-entrypoint.sh && \
    echo 'set -e' >> /usr/local/bin/docker-entrypoint.sh && \
    echo 'if [ ! -f .env ]; then cp .env.example .env; fi' >> /usr/local/bin/docker-entrypoint.sh && \
    echo 'php artisan config:cache' >> /usr/local/bin/docker-entrypoint.sh && \
    echo 'php artisan route:cache' >> /usr/local/bin/docker-entrypoint.sh && \
    echo 'php artisan view:cache' >> /usr/local/bin/docker-entrypoint.sh && \
    echo 'exec docker-php-entrypoint apache2-foreground' >> /usr/local/bin/docker-entrypoint.sh && \
    chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 80
ENTRYPOINT ["docker-entrypoint.sh"]
