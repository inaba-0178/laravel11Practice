#!/bin/bash
set -e

# Laravel自動セットアップ（初回のみ）
if [ ! -f /var/www/artisan ]; then
    echo "🚀 Initializing Laravel environment..."
    
    composer create-project laravel/laravel /var/www --prefer-dist --no-interaction
    cd /var/www
    
    cp .env.example .env
    php artisan key:generate --no-interaction
    
    mkdir -p database
    touch database/database.sqlite
    chown -R www-data:www-data storage bootstrap/cache database database.sqlite
    chmod -R 775 storage bootstrap/cache database
    chmod 664 database/database.sqlite
    
    echo "✅ Laravel setup completed!"
fi

# php-fpm起動
exec "$@"
