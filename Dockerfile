# Dockerfile universal para desarrollo local (docker compose) y despliegue en la nube (Railway)
FROM php:8.2-apache

# Extensiones del sistema y PHP requeridas (GD con soporte webp/jpeg/freetype, PDO MySQL, etc.)
RUN apt-get update && apt-get install -y --no-install-recommends \
        libpng-dev \
        libjpeg62-turbo-dev \
        libwebp-dev \
        libfreetype6-dev \
        libonig-dev \
        libzip-dev \
        zip \
        unzip \
    && docker-php-ext-configure gd --with-jpeg --with-webp --with-freetype \
    && docker-php-ext-install -j"$(nproc)" gd pdo_mysql mysqli mbstring fileinfo \
    && a2enmod rewrite headers \
    && rm -rf /var/lib/apt/lists/*

# Asegurar MPM prefork para mod_php
RUN a2dismod -q mpm_event mpm_worker 2>/dev/null || true \
    && a2enmod -q mpm_prefork

# Configuración de producción de PHP y ajustes del proyecto
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
COPY docker/php.ini "$PHP_INI_DIR/conf.d/zz-itb.ini"

# Configuración de VirtualHost con soporte completo de .htaccess
COPY docker/apache-vhost.conf /etc/apache2/sites-available/000-default.conf

# Código del sitio
COPY --chown=www-data:www-data . /var/www/html/
RUN chmod 755 /var/www/html

# Entrypoint dinámico (soporte para puerto Railway $PORT y volúmenes)
COPY docker/entrypoint.sh /usr/local/bin/itb-entrypoint
RUN sed -i 's/\r$//' /usr/local/bin/itb-entrypoint && chmod +x /usr/local/bin/itb-entrypoint

# Validar sintaxis de Apache en tiempo de compilación
RUN apache2ctl -t

ENV PORT=8080
EXPOSE 8080

ENTRYPOINT ["itb-entrypoint"]
CMD ["apache2-foreground"]
