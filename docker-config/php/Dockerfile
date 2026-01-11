FROM php:8.5-fpm

# PHP拡張 + Composer + Node.js（既存のまま）
RUN apt-get update && apt-get install -y \
    gcc make git unzip vim libpng-dev libjpeg-dev libfreetype6-dev \
    libpq-dev curl gnupg openssl ca-certificates gnupg libzip-dev libicu-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) pdo_mysql mysqli gd zip bcmath intl \
    && apt-get clean && rm -rf /var/lib/apt/lists/* \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - && apt-get install -y nodejs

# 🔥 entrypointスクリプトをコピー＆実行権限付与
COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

WORKDIR /var/www
ENTRYPOINT ["/entrypoint.sh"]
CMD ["php-fpm"]
