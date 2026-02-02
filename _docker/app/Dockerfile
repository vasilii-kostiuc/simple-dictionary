FROM php:8.4-fpm

# System dependencies
RUN apt-get update && apt-get install -y \
    git \
    zip \
    unzip \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    default-mysql-client \
    && docker-php-ext-configure gd --with-jpeg --with-freetype \
    && docker-php-ext-install gd pdo_mysql bcmath zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# PHP configuration
COPY ./_docker/app/php.ini /usr/local/etc/php/conf.d/php.ini

# Install Composer
ENV COMPOSER_ALLOW_SUPERUSER=1
RUN curl -sS https://getcomposer.org/installer | php -- \
    --filename=composer --install-dir=/usr/local/bin

WORKDIR /var/www/simple_dictionary_api

# Copy application code first (needed for local packages)
COPY . .

# Install dependencies (disable symlinks for Docker)
RUN composer config repositories.0 '{"type": "path", "url": "./packages/vasilii-kostiuc/laravel-messaging-library", "options": {"symlink": false}}' \
    && composer install --no-dev --optimize-autoloader

# Set permissions
RUN chown -R www-data:www-data /var/www/simple_dictionary_api/storage /var/www/simple_dictionary_api/bootstrap/cache

# Copy and set entrypoint
COPY ./_docker/app/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["entrypoint.sh"]
CMD ["php-fpm"]
