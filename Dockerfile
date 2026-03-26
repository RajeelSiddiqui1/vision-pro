# ---------- Stage 1: Build ----------
FROM composer:2 AS build
WORKDIR /app

# Copy composer files first
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader

# Copy EVERYTHING from root (excluding what's in .dockerignore)
COPY . .

# ---------- Stage 2: Production ----------
FROM php:8.2-apache

# Install PHP extensions
RUN apt-get update && apt-get install -y \
    libzip-dev libpng-dev libonig-dev \
 && docker-php-ext-install mysqli pdo pdo_mysql zip mbstring gd \
 && apt-get clean && rm -rf /var/lib/apt/lists/*

# Enable Apache mods
RUN a2enmod rewrite
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Allow .htaccess overrides
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

# Set working directory
WORKDIR /var/www/html

# Copy everything from the build stage back to production
COPY --from=build /app /var/www/html

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
 && chmod -R 755 /var/www/html

EXPOSE 80
CMD ["apache2-foreground"]