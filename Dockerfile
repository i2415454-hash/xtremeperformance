# 1. Usar la imagen oficial de PHP con Apache
FROM php:8.2-apache

# 2. Habilitar el módulo rewrite de Apache (indispensable para tu .htaccess)
RUN a2enmod rewrite

# 3. Instalar dependencias del sistema necesarias para Composer
RUN apt-get update && apt-get install -y git unzip libzip-dev \
    && docker-php-ext-install zip pdo
# Nota: Si tu sistema usa alguna base de datos en específico (ej. SQL Server o MySQL), 
# debes agregar la instalación de la extensión de PHP correspondiente en este paso.

# 4. Copiar todos tus archivos al contenedor
COPY . /var/www/html/

# 5. Instalar Composer en el contenedor
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 6. Ejecutar Composer para instalar las dependencias de tu composer.json
RUN composer install --no-dev --optimize-autoloader

# 7. Configurar Apache para que apunte directamente a tu carpeta "public"
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# 8. Cambiar el puerto de Apache al dinámico que requiere Cloud Run
RUN sed -i 's/80/${PORT}/g' /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf

# 9. Dar permisos de lectura/escritura correctos
RUN chown -R www-data:www-data /var/www/html
