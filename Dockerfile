# Usa una imagen oficial de PHP con Apache
FROM php:8.2-apache

# Copia todos los archivos al contenedor
COPY . /var/www/html/

# Habilita mod_rewrite si usas .htaccess
RUN a2enmod rewrite

# Da permisos si necesitas para la carpeta de subidas
RUN chmod -R 755 /var/www/html/uploads

# Exponer el puerto
EXPOSE 80
