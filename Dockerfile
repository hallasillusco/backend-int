# PHP 8.2 CLI
FROM php:8.2-cli

# Paquetes para extensiones PHP necesarias (Laravel, Excel, Dompdf, Barcode)
RUN apt-get update && apt-get install -y \
    git unzip libzip-dev libonig-dev libxml2-dev \
    libpng-dev libjpeg62-turbo-dev libfreetype6-dev \
 && docker-php-ext-configure gd --with-freetype --with-jpeg \
 && docker-php-ext-install pdo_mysql mbstring zip bcmath gd xml \
 && apt-get clean && rm -rf /var/lib/apt/lists/*

# Composer desde imagen oficial
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# App
WORKDIR /app

# Instala deps con mejor caché
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

# Copia el resto del código
COPY . .

# Optimiza (si algo falla, que no rompa el build)
RUN php artisan optimize:clear || true \
 && php artisan storage:link || true \
 && php artisan config:cache \
 && php artisan route:cache || true \
 && php artisan view:cache || true

# Render usa $PORT; exponemos 8080 por defecto
EXPOSE 8080

# Escribimos el CA de MySQL desde env y levantamos Laravel con PHP server
CMD bash -lc 'printf "%s" "$MYSQL_SSL_CA" > /tmp/mysql-ca.pem; php -d variables_order=EGPCS -S 0.0.0.0:${PORT:-8080} -t public public/index.php'
