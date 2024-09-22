FROM php:8.2-fpm-alpine

# Install nano editor
RUN apk update && apk add nano

# Menambahkan konfigurasi PHP-FPM
ADD ./docker/php/www.conf /usr/local/etc/php-fpm.d/www.conf

# Membuat user dan grup laravel
RUN addgroup -g 1000 laravel && adduser -G laravel -g laravel -s /bin/sh -D laravel

# Membuat direktori untuk proyek
RUN mkdir -p /var/www/html

# Menambahkan source code ke dalam container
ADD ./src/ /var/www/html

# Menginstal dependensi MySQL dan ekstensi PHP
RUN set -ex \
  && apk --no-cache add \
    mysql-dev \
    && docker-php-ext-install pdo pdo_mysql

# Mengubah kepemilikan direktori menjadi user laravel
RUN chown -R laravel:laravel /var/www/html
