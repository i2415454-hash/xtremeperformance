FROM php:8.2-apache

# Instalar extensiones de PHP necesarias (ejemplo para MySQL)
RUN docker-php-ext-install pdo pdo_mysql

# Si tu proyecto usa otras extensiones (como GD, zip, etc.), puedes añadirlas aquí

# Copiar el código del proyecto al directorio web de Apache
COPY . /var/www/html/

# Asegurar los permisos correctos para Apache
RUN chown -r www-data:www-data /var/www/html

# Habilitar el módulo de reescritura de Apache (útil para frameworks como Laravel o rutas amigables)
RUN a2enmod rewrite

EXPOSE 80
