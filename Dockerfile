FROM php:8.2-apache

# تثبيت الحزم المطلوبة وتفعيل تعريفات قاعدة البيانات لـ PHP
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git

# تثبيت وتفعيل درايفر MySQL الخاص بـ PHP
RUN docker-php-ext-install pdo pdo_mysql mysqli

# تفعيل وحدات أباتشي
RUN a2enmod rewrite

# نسخ ملفات المشروع
COPY . /var/www/html

# ضبط الصلاحيات
RUN chown -R www-data:www-data /var/www/html
