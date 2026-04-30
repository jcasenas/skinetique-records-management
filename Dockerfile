FROM php:8.4-apache

# Install system packages and PHP extensions
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    libpq-dev \
    libzip-dev \
    libonig-dev \
    libxml2-dev \
    libpng-dev \
    zip \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql zip mbstring xml \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Enable Apache rewrite
RUN a2enmod rewrite

# Make Apache use Render's default web port 10000
RUN sed -i 's/Listen 80/Listen 10000/g' /etc/apache2/ports.conf \
    && sed -i 's/<VirtualHost \*:80>/<VirtualHost *:10000>/g' /etc/apache2/sites-available/000-default.conf

# Set Laravel public as document root
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf \
    && sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/apache2.conf

# Allow .htaccess for Laravel
RUN printf '<Directory /var/www/html/public>\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>\n' > /etc/apache2/conf-available/laravel.conf \
    && a2enconf laravel

# Install Node.js
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy full Laravel app
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Install frontend dependencies and build Vite assets
RUN npm install && npm run build

# Set permissions BEFORE artisan commands
RUN mkdir -p storage/framework/cache \
              storage/framework/sessions \
              storage/framework/views \
              bootstrap/cache \
              public/uploads \
    && chown -R www-data:www-data storage bootstrap/cache public/uploads \
    && chmod -R 775 storage bootstrap/cache public/uploads

# Create storage symlink
RUN php artisan storage:link || true

EXPOSE 10000

# ── IMPORTANT ──────────────────────────────────────────────────────────────
# Do NOT run config:cache, route:cache, or view:cache in the Dockerfile.
# Those commands bake environment variables into files at build time,
# but Render injects env vars at runtime — so the cached files end up
# with empty/wrong values and cause the 500 error.
#
# Instead we run them in the startup script below so they execute AFTER
# Render has injected the real environment variables.
# ───────────────────────────────────────────────────────────────────────────

# Write a startup script that runs migrations and caches at container start
RUN printf '#!/bin/bash\n\
set -e\n\
\n\
echo "==> Clearing stale caches..."\n\
php artisan config:clear\n\
php artisan route:clear\n\
php artisan view:clear\n\
\n\
echo "==> Caching config, routes, views..."\n\
php artisan config:cache\n\
php artisan route:cache\n\
php artisan view:cache\n\
\n\
echo "==> Running migrations..."\n\
php artisan migrate --force\n\
\n\
echo "==> Starting Apache..."\n\
exec apache2-foreground\n\
' > /usr/local/bin/start.sh \
    && chmod +x /usr/local/bin/start.sh

CMD ["/usr/local/bin/start.sh"]