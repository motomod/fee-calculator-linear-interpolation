FROM php:8.1-cli

WORKDIR /usr/src/myapp

RUN apt update && apt install git libzip-dev -y
RUN docker-php-ext-install zip bcmath
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
