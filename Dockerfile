FROM php:8.2-cli

# Evita advertencias de Composer por ejecutarse como root
ENV COMPOSER_ALLOW_SUPERUSER=1

# Paquetes y extensiones necesarias
RUN apt-get update && apt-get install -y \
    git unzip libzip-dev libonig-dev libxml2-dev \
    libpng-dev libjpeg62-turbo-dev libfreetype6-dev \
 && docker-php-ext-configure gd --with-freetype --with-jpeg \
 && docker-php-ext-install pdo_mysql mbstring zip bcmath gd xml \
 && apt-get clean && rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# 1) Instala deps SIN scripts (aún no existe artisan)
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --prefer-dist --no-scripts

# 2) Copia el resto del código (ya existe artisan)
COPY . .

# 3) Re-ejecuta composer con scripts y optimiza
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist \
 && php artisan optimize:clear || true \
 && php artisan storage:link || true \
 && php artisan config:cache || true \
 && php artisan route:cache || true \
 && php artisan view:cache || true

EXPOSE 8080

# Escribe el CA y levanta Laravel
CMD bash -lc 'printf "%s" "$MYSQL_SSL_CA" > /tmp/mysql-ca.pem; php -d variables_order=EGPCS -S 0.0.0.0:${PORT:-8080} -t public public/index.php'
