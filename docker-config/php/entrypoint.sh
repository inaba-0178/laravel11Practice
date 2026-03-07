#!/bin/bash
set -e

if [ ! -f /var/www/artisan ]; then
    echo "Initializing Laravel environment..."
    
    composer create-project laravel/laravel /var/www --prefer-dist --no-interaction
    cd /var/www
    
    cp .env.example .env
    php artisan key:generate --no-interaction
    
    mkdir -p database
    touch database/database.sqlite
    chmod 664 database/database.sqlite
    
    echo "Laravel setup completed!"
fi

# 毎回起動時に権限設定
echo "Setting permissions..."
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# php-fpm起動
exec "$@"