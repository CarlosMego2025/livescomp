<?php
require_once 'config.php';

// Función para redirigir
function redirect($url) {
    header("Location: $url");
    exit;
}

// Función para verificar si el usuario está autenticado
function isAuthenticated() {
    return isset($_SESSION['usuario_id']);
}

// Función para verificar el rol del usuario
function hasRole($role) {
    return isset($_SESSION['usuario_rol']) && $_SESSION['usuario_rol'] === $role;
}

// Función para mostrar mensajes flash
function flash($name = '', $message = '') {
    if (!empty($name)) {
        if (!empty($message)) {
            // Establecer mensaje
            $_SESSION[$name] = $message;
        } else {
            // Mostrar mensaje
            if (isset($_SESSION[$name])) {
                $message = $_SESSION[$name];
                unset($_SESSION[$name]);
                return $message;
            }
        }
    }
    return '';
}

// Función para sanitizar datos
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

// Función para formatear precio
function formatPrice($price) {
    return number_format($price, 2, '.', ',');
}
?>