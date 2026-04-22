FROM php:8.4-cli

# Install required system packages & PHP extensions
RUN apt-get update -y && apt-get install -y libpq-dev libpng-dev libzip-dev unzip zip git \
    && docker-php-ext-install pdo pdo_pgsql gd zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install Node.js & NPM
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# Set working directory
WORKDIR /app
COPY . /app

# Install PHP and NPM dependencies
RUN composer install --optimize-autoloader --no-dev --ignore-platform-req=ext-zip
RUN npm install && npm run build

# Fix Laravel Storage and Cache Permissions (This fixes the 500 Error)
RUN mkdir -p /app/storage/logs /app/storage/framework/views /app/storage/framework/cache /app/storage/framework/sessions /app/bootstrap/cache \
    && chmod -R 777 /app/storage /app/bootstrap/cache

# Expose port and start Laravel server with automatic Database Migration
EXPOSE 8000
CMD php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=8000
