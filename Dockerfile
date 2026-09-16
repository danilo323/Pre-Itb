# Imagen para el entorno de prueba en Railway.
# Apache + mod_php para reutilizar los .htaccess del proyecto tal cual
# (rutas limpias, 404 personalizado, bloqueo de data/, no-PHP en img/).
FROM php:8.2-apache

# Extensiones que usa el panel: gd (compresión de imágenes) y pdo_mysql.
# fileinfo y mbstring ya vienen compiladas en la imagen oficial.
RUN apt-get update && apt-get install -y --no-install-recommends \
        libpng-dev libjpeg62-turbo-dev libwebp-dev libfreetype6-dev \
    && docker-php-ext-configure gd --with-jpeg --with-webp --with-freetype \
    && docker-php-ext-install -j"$(nproc)" gd pdo_mysql \
    && a2enmod rewrite headers \
    && rm -rf /var/lib/apt/lists/*

# php.ini de producción + ajustes del proyecto (subidas de 32 MB, errores a log)
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
COPY docker/php.ini "$PHP_INI_DIR/conf.d/zz-itb.ini"

# VirtualHost con AllowOverride All para que apliquen los .htaccess
COPY docker/apache-vhost.conf /etc/apache2/sites-available/000-default.conf

# Código del sitio. Lo listado en .dockerignore (test/, .git, cookies.txt...) no entra.
COPY --chown=www-data:www-data . /var/www/html/
# La imagen base deja /var/www/html en 1777 (sticky + escribible por todos). Con
# fs.protected_symlinks=1 eso impide que www-data siga los symlinks al volume
# (is_dir() devuelve false y las subidas fallan). Un DocumentRoot normal es 755.
RUN chmod 755 /var/www/html

# Entrypoint: ajusta el puerto de Railway y enlaza el volume persistente
COPY docker/entrypoint.sh /usr/local/bin/itb-entrypoint
RUN sed -i 's/\r$//' /usr/local/bin/itb-entrypoint && chmod +x /usr/local/bin/itb-entrypoint

ENV PORT=8080
EXPOSE 8080
ENTRYPOINT ["itb-entrypoint"]
CMD ["apache2-foreground"]
