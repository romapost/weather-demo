FROM php:7.3-fpm

RUN apt-get update && apt-get install -y wget git unzip \
    && pecl install xdebug-2.9.4 \
    && docker-php-ext-enable xdebug

RUN pecl install redis && docker-php-ext-enable redis

ADD ./php.ini /usr/local/etc/php/php.ini

RUN wget https://getcomposer.org/installer -O - -q \
    | php -- --install-dir=/bin --filename=composer --quiet

WORKDIR /var/www
