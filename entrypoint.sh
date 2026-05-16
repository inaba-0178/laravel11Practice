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

# ストレージディレクトリ作成
echo "Creating storage directories..."
mkdir -p /var/www/storage/app/mst-uploads
chown -R www-data:www-data /var/www/storage/app/mst-uploads
chmod -R 775 /var/www/storage/app/mst-uploads

# cron起動
echo "Starting cron..."
crontab /etc/cron.d/laravel-cron  # 追加
service cron start

# php-fpm起動
exec "$@"