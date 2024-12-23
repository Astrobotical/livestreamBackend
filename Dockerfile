# Use the official PHP image
FROM php:8.2-fpm

# Set the working directory
WORKDIR /var/www/html

# Install system dependencies
RUN apt-get update && apt-get install -y \
    curl \
    zip \
    unzip \
    git \
    libmcrypt-dev \
    libonig-dev \
    libzip-dev \
    && docker-php-ext-install pdo_mysql mbstring zip

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copy the application files to the container
COPY . .

# Install PHP dependencies
RUN composer install --optimize-autoloader --no-dev

# Set permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Expose port 8000
EXPOSE 8000

# Start the Laravel application
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]