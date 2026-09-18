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

# نسخ ملفات المشروع
COPY . /var/www/html

# إنشاء مجلدات التخزين والكاش إذا لم تكن موجودة لتجنب الأخطاء
RUN mkdir -p /var/www/html/storage /var/www/html/bootstrap/cache

# تغيير مجلد العمل إلى public الخاص بلارافيل
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf

# صلاحيات المجلدات
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
