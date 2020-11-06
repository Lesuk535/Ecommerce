FROM php:7.4-cli

RUN apt-get update && apt-get install -y \
    libpq-dev unzip\
    wget \
    zlib1g-dev \
    libfreetype6-dev \
    libmcrypt-dev \
    libjpeg62-turbo-dev \
    libzip-dev \
    libpng-dev

RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pdo_pgsql

COPY ./xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

RUN wget https://getcomposer.org/installer -O - -q | php -- --install-dir=/bin --filename=composer --quiet

ENV COMPOSER_ALLOW_SUPERUSER 1

WORKDIR /symfony