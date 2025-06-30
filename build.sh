#!/bin/bash
echo "Instalando dependencias si es necesario..."

# Si usas Composer (opcional)
# if [ -f "composer.json" ]; then
#     php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
#     php composer-setup.php
#     php -r "unlink('composer-setup.php');"
#     php composer.phar install --no-dev
# fi

# Crear directorios necesarios
mkdir -p uploads/products
chmod -R 755 uploads

echo "Build completado"