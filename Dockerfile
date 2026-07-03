FROM php:8.2-apache

# Instalar extensiones necesarias
RUN docker-php-ext-install pdo pdo_mysql

# Copiar el código del proyecto
COPY . /var/www/html/

# --- LA MAGIA SUCEDE AQUÍ ---
# Cambiamos el DocumentRoot de Apache a la carpeta public
ENV APACHE_DOCUMENT_ROOT /var/www/html/public

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf
# -----------------------------

# Asegurar permisos
RUN chown -R www-data:www-data /var/www/html

# Habilitar mod_rewrite de Apache para que funcione el .htaccess
RUN a2enmod rewrite

EXPOSE 80
