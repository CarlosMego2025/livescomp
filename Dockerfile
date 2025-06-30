FROM php:8.2-apache

# Habilita mod_rewrite si usas .htaccess
RUN a2enmod rewrite

# Copia los archivos del proyecto al servidor web
COPY . /var/www/html/

# Da permisos si usas carpetas de subida (opcional)
RUN chmod -R 755 /var/www/html/uploads

# Exponer el puerto 80
EXPOSE 80
