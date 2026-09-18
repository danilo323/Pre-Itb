FROM php:8.2-apache

# Instalar dependencias del sistema y extensiones de PHP requeridas
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libzip-dev \
    zip \
    unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) pdo_mysql mysqli mbstring gd fileinfo \
    && rm -rf /var/lib/apt/lists/*

# Habilitar mod_rewrite para URLs limpias y redirecciones del .htaccess
RUN a2enmod rewrite

# Permitir lectura completa de .htaccess en /var/www/html
RUN sed -ri -e 's!AllowOverride None!AllowOverride All!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Ajustes de rendimiento y tamaño de subida para la biblioteca de medios
RUN echo "upload_max_filesize = 32M\npost_max_size = 32M\nmemory_limit = 256M\n" > /usr/local/etc/php/conf.d/custom.ini

WORKDIR /var/www/html
