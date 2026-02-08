FROM php:8.2-apache

# Fix Apache MPM + dependencias
RUN apt-get update && apt-get install -y \
    libpq-dev libzip-dev libpng-dev libjpeg-dev libfreetype6-dev unzip postgresql-client \
    && a2dismod mpm_event mpm_worker && a2enmod mpm_prefork \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql zip gd bcmath

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . .

RUN composer install --no-dev --optimize-autoloader --no-interaction

# ✅ .env MANUAL (sin sed)
RUN echo "APP_NAME=Laravel" > .env && \
    echo "APP_ENV=production" >> .env && \
    echo "APP_KEY=" >> .env && \
    echo "APP_DEBUG=false" >> .env && \
    echo "APP_URL=https://dss-proyecto-bqsj.onrender.com" >> .env && \
    echo "" >> .env && \
    echo "DB_CONNECTION=pgsql" >> .env && \
    echo "DB_HOST=dpg-d641lt0gjchc739dj6i0-a" >> .env && \
    echo "DB_PORT=5432" >> .env && \
    echo "DB_DATABASE=dbedu_f16g" >> .env && \
    echo "DB_USERNAME=root" >> .env && \
    echo "DB_PASSWORD=Kidiz9jgj7NKkzQ5WvCTVXLPnrK9iXVL" >> .env && \
    echo "" >> .env && \
    echo "SESSION_DRIVER=file" >> .env && \
    echo "SESSION_LIFETIME=120" >> .env

RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html/storage /var/www/html/bootstrap/cache

# Apache Laravel
RUN a2enmod rewrite && \
    echo '<Directory /var/www/html/public>' > /etc/apache2/conf-available/laravel.conf && \
    echo '    AllowOverride All' >> /etc/apache2/conf-available/laravel.conf && \
    echo '    Require all granted' >> /etc/apache2/conf-available/laravel.conf && \
    echo '</Directory>' >> /etc/apache2/conf-available/laravel.conf && \
    a2enconf laravel && \
    sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

EXPOSE 8080
CMD ["apache2-foreground"]
