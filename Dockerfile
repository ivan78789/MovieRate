FROM php:8.2-apache

# Включаем mod_rewrite, если нужно
RUN a2enmod rewrite

# Устанавливаем расширения для работы с MySQL через PDO:
RUN docker-php-ext-install pdo pdo_mysql

# (опционально) Проверяем, что модуль загружен
RUN docker-php-ext-enable pdo_mysql
