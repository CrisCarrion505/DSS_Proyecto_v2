#!/bin/sh
set -e

# Si no existe .env, copiar el de ejemplo
if [ ! -f .env ]; then
    echo "Copiando .env.example a .env..."
    cp .env.example .env
fi

# Generar Key si no está configurada (fallback)
if [ -z "$APP_KEY" ]; then
    echo "Generando APP_KEY..."
    php artisan key:generate --force
fi

# Optimizar caché
echo "Cacheando configuración..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Iniciando Apache..."
exec docker-php-entrypoint apache2-foreground
