FROM php:8.2-apache

# تثبيت الحزم المطلوبة
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git

# تفعيل وحدات أباتشي
RUN a2enmod rewrite

# السماح بعرض محتويات المجلد إذا لم يوجد ملف index
RUN echo "Options +Indexes" >> /etc/apache2/apache2.conf

# نسخ ملفات المشروع
COPY . /var/www/html

# ضبط الصلاحيات
RUN chown -R www-data:www-data /var/www/html
