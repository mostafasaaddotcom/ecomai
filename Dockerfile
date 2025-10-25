# Multi-stage Dockerfile for Laravel Application
# Stage 1: Build frontend assets (with vendor dependencies)
FROM php:8.2-fpm-alpine AS frontend-builder

WORKDIR /app

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy composer files and install PHP dependencies first
# (needed because app.css imports vendor/livewire/flux files)
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --no-interaction --no-progress

# Install Node.js and npm
RUN apk add --no-cache nodejs npm

# Copy package files and install Node dependencies
COPY package*.json ./
RUN npm ci

# Copy frontend source files
COPY resources/ resources/
COPY vite.config.js ./
COPY public/ public/

# Build assets (now vendor/livewire/flux exists)
RUN npm run build

# Stage 2: Production PHP Application
FROM php:8.2-fpm-alpine

# Install system dependencies and PHP extensions
RUN apk add --no-cache \
    nginx \
    supervisor \
    git \
    curl \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    zip \
    unzip \
    oniguruma-dev \
    sqlite-dev \
    mysql-client \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    pdo_mysql \
    pdo_sqlite \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip

# Install Redis extension
RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del .build-deps

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy composer files
COPY composer.json composer.lock ./

# Copy artisan file (needed for composer post-install scripts)
COPY artisan ./

# Install PHP dependencies (production mode, skip scripts for now)
RUN composer install --no-dev --no-scripts --no-interaction --no-progress

# Copy application files
COPY . .

# Generate optimized autoloader and run post-install scripts
RUN composer dump-autoload --optimize

# Copy built frontend assets from first stage
COPY --from=frontend-builder /app/public/build ./public/build

# Set permissions for Laravel
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Copy Nginx configuration
COPY docker/nginx/nginx.conf /etc/nginx/nginx.conf
COPY docker/nginx/default.conf /etc/nginx/http.d/default.conf

# Copy Supervisor configuration
COPY docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Create SQLite database file if needed
RUN touch /var/www/html/database/database.sqlite \
    && chown www-data:www-data /var/www/html/database/database.sqlite

# Expose port 80
EXPOSE 80

# Start Supervisor (manages both Nginx and PHP-FPM)
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
