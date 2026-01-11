#!/bin/bash
set -e

# ホストのappフォルダにLaravelが存在しない場合のみ生成
if [ ! -f "/var/www/artisan" ]; then
    echo "Installing Laravel to ./app..."
    
    # 一時ディレクトリでLaravel生成
    cd /tmp
    composer create-project laravel/laravel laravel_new --prefer-dist --no-interaction
    
    # ホストのappフォルダに完全コピー（Volume経由）
    rm -rf /var/www/*
    cp -a laravel_new/. /var/www/
    cp -a laravel_new/.* /var/www/ 2>/dev/null || true
    
    cd /var/www
    php artisan key:generate --no-interaction
    chown -R www-data:www-data storage bootstrap/cache
    
    echo "Laravel installation completed!"
else
    echo "Laravel already exists, skipping installation"
fi

# 元のCMD（php-fpm）を起動
exec "$@"
