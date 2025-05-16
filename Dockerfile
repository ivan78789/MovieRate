FROM php:8.2-apache

RUN docker-php-ext-install mysqli
RUN a2enmod rewrite

COPY . /var/www/html

# Установка прав
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html