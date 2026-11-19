FROM php:8.2-apache
RUN apt-get update && apt-get install -y libonig-dev && docker-php-ext-install pdo_mysql mbstring && rm -rf /var/lib/apt/lists/*
COPY uploads.ini /usr/local/etc/php/conf.d/uploads.ini
RUN a2enmod headers
WORKDIR /var/www/html
