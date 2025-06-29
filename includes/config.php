<?php
// Configuración de entorno (desarrollo/producción)
define('ENVIRONMENT', 'development'); // Cambiar a 'production' en Render

// Configuración de la aplicación
define('APP_NAME', 'LivesComp');
define('APP_URL', ENVIRONMENT === 'development' ? 'http://localhost/livescomp' : 'https://your-render-app.onrender.com');
define('APP_PATH', __DIR__ . '/..');

// Configuración de la base de datos (usaremos variables de entorno en Render)
if (ENVIRONMENT === 'development') {
    define('DB_HOST', 'localhost');
    define('DB_NAME', 'livescomp');
    define('DB_USER', 'root');
    define('DB_PASS', '');
} else {
    define('DB_HOST', getenv('DB_HOST'));
    define('DB_NAME', getenv('DB_NAME'));
    define('DB_USER', getenv('DB_USER'));
    define('DB_PASS', getenv('DB_PASSWORD'));
}

// Roles de usuario
define('ROL_ADMIN', 'admin');
define('ROL_EJECUTIVO', 'ejecutivo');
define('ROL_CLIENTE', 'cliente');

// Estados de pedido
define('ESTADO_PENDIENTE', 'pendiente');
define('ESTADO_APROBADO', 'aprobado');
define('ESTADO_ENVIADO', 'enviado');
define('ESTADO_COMPLETADO', 'completado');
define('ESTADO_CANCELADO', 'cancelado');

// Iniciar sesión y manejo de errores
session_start();
error_reporting(ENVIRONMENT === 'development' ? E_ALL : 0);
ini_set('display_errors', ENVIRONMENT === 'development' ? '1' : '0');
?>