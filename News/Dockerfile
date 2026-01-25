FROM php:8.2-apache

RUN docker-php-ext-install mysqli pdo pdo_mysql

# انسخي ملفات مشروعك اللي داخل مجلد News إلى مسار Apache
COPY ./News/ /var/www/html/

# (اختياري) لو عندك .htaccess أو routing
RUN a2enmod rewrite

EXPOSE 80
