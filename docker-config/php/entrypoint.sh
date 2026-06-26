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
chmod -R 777 /var/www/storage /var/www/bootstrap/cache

# cron起動
echo "Starting cron..."
service cron start

# Reverb起動（バックグラウンド）
echo "Starting Reverb..."
php artisan reverb:start --host=0.0.0.0 --port=8085 >> /var/log/reverb.log 2>&1 &

# php-fpm起動
exec "$@"