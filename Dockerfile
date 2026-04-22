FROM php:8.4-cli

# Install required system packages & PHP extensions (including GD and PostgreSQL)
RUN apt-get update -y && apt-get install -y libpq-dev libpng-dev unzip git \
    && docker-php-ext-install pdo pdo_pgsql gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install Node.js & NPM (for frontend assets)
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# Set working directory
WORKDIR /app
COPY . /app

# Install PHP and NPM dependencies
RUN composer install --optimize-autoloader --no-dev
RUN npm install && npm run build

# Expose port and start Laravel server
EXPOSE 8000
CMD php artisan serve --host=0.0.0.0 --port=8000
