FROM composer:latest

WORKDIR /usr/local/etc/php/conf.d/

COPY config/docker/config/php-cli/php.ini .

WORKDIR /var/www/html

ENTRYPOINT [ "composer", "--ignore-platform-reqs" ]