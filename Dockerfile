FROM php:8.2-apache

RUN docker-php-ext-install mysqli pdo pdo_mysql

# انسخي محتوى مجلد News إلى مجلد الويب مباشرة
COPY ./News/ /var/www/html/

# (اختياري لكن ممتاز) صلاحيات
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
