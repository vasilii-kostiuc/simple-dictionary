FROM php:8.4-fpm

# System dependencies
RUN apt-get update && apt-get install -y git zip unzip libzip-dev libpng-dev libjpeg-dev libfreetype6-dev default-mysql-client \
 && docker-php-ext-configure gd --with-jpeg --with-freetype \
 && docker-php-ext-install gd pdo_mysql bcmath zip \
 && apt-get clean && rm -rf /var/lib/apt/lists/*

COPY ./_docker/app/php.ini /usr/local/etc/php/conf.d/php.ini

# Composer
ENV COMPOSER_ALLOW_SUPERUSER=1
RUN curl -sS https://getcomposer.org/installer | php -- \
    --filename=composer --install-dir=/usr/local/bin

WORKDIR /var/www/simple_dictionary_api

# Копируем весь код и ставим зависимости
COPY . .
RUN composer install --no-dev --optimize-autoloader

COPY ./_docker/app/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["entrypoint.sh"]
CMD ["php-fpm"]
