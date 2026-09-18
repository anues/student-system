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

# نسخ ملفات المشروع مباشرة إلى مسار الأباتشي الافتراضي
COPY . /var/www/html

# إنشاء مجلدات التخزين والكاش تلقائياً لمنع أي أخطاء
RUN mkdir -p /var/www/html/storage /var/www/html/bootstrap/cache

# ضبط الصلاحيات
RUN chown -R www-data:www-data /var/www/html
