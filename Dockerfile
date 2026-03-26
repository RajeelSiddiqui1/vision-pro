# ---------- Stage 1: Build ----------
FROM composer:2 AS build

WORKDIR /app

# Copy composer files
COPY composer.json composer.lock ./

# Install dependencies (without dev for production)
RUN composer install --no-dev --optimize-autoloader

# Copy all project files
COPY . .

# ---------- Stage 2: Production ----------
FROM php:8.2-apache

# Install required extensions
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Enable Apache mod_rewrite
RUN a2enmod rewrite
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Set working directory
WORKDIR /var/www/html

# Copy files from build stage
COPY --from=build /app /var/www/html

# Set permissions
RUN chown -R www-data:www-data /var/www/html

# Expose port
EXPOSE 80