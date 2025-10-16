FROM php:8.4-fpm

# Устанавливаем GD для PHP (как для FPM, так и CLI)
RUN apt-get update && apt-get install -y \
    libjpeg62-turbo-dev libpng-dev libfreetype6-dev \
 && docker-php-ext-configure gd --with-jpeg --with-freetype \
 && docker-php-ext-install gd

RUN apt-get update && apt-get install -y \
      apt-utils \
      libpq-dev \
      libpng-dev \
      libzip-dev \
      zip unzip \
      git && \
      docker-php-ext-install pdo_mysql && \
      docker-php-ext-install bcmath && \
      docker-php-ext-install zip && \
      apt-get clean && \
      rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

RUN apt-get update && apt-get install -y default-mysql-client

COPY ./_docker/app/php.ini /usr/local/etc/php/conf.d/php.ini

ENV COMPOSER_ALLOW_SUPERUSER=1
RUN curl -sS https://getcomposer.org/installer | php -- \
    --filename=composer \
    --install-dir=/usr/local/bin

WORKDIR /var/www/simple_dictionary_api

ENTRYPOINT ["entrypoint.sh"]
CMD ["php-fpm"]
