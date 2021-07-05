FROM php:7.2.34-cli-alpine

RUN apk update && apk add curl git wget

RUN apk add --update --no-cache --virtual .build-dependencies $PHPIZE_DEPS

RUN pecl update-channels

RUN docker-php-ext-install pdo pdo_mysql bcmath sockets opcache && docker-php-ext-enable opcache && pecl install apcu && docker-php-ext-enable apcu && pecl install xdebug && docker-php-ext-enable xdebug

WORKDIR /usr/local/etc/php/conf.d/

COPY config/docker/config/php-cli/php.ini .

WORKDIR /var/www/html

RUN chown -R www-data:www-data /var/www/html

COPY . .

ENTRYPOINT [ "php", "artisan", "fulfillment:projection_generator"]