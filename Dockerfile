# Imagen oficial de PHP con Apache
FROM php:8.3-apache

# Extensión PDO para MySQL (la imagen base no la trae)
RUN docker-php-ext-install pdo pdo_mysql

# Activamos mod_rewrite (lo necesitarás en el Paso 7 para las rutas limpias)
RUN a2enmod rewrite

# Apache sirve public/ en vez de la raíz del proyecto
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Composer dentro del contenedor
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer