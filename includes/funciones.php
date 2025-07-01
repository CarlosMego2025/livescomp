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
function subirImagen($file, $folder) {
    $allowed = ['jpg', 'jpeg', 'png', 'webp'];
    $maxSize = 2 * 1024 * 1024; // 2MB
    
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed)) {
        throw new Exception("Formato de imagen no permitido. Use JPG, PNG o WEBP");
    }
    
    if ($file['size'] > $maxSize) {
        throw new Exception("La imagen es demasiado grande. Máximo 2MB");
    }
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new Exception("Error al subir la imagen");
    }
    
    $filename = uniqid() . '.' . $ext;
    $uploadPath = APP_PATH . "/assets/images/$folder/" . $filename;
    
    if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
        throw new Exception("Error al guardar la imagen");
    }
    
    return $filename;
}

function eliminarImagen($filename, $folder) {
    $path = APP_PATH . "/assets/images/$folder/" . $filename;
    if (file_exists($path)) {
        unlink($path);
    }
}
// Función para mostrar mensajes flash
function flashMessage() {
    if (isset($_SESSION['flash_message'])) {
        echo '<div class="alert alert-'.$_SESSION['flash_type'].'">'.$_SESSION['flash_message'].'</div>';
        unset($_SESSION['flash_message']);
        unset($_SESSION['flash_type']);
    }
}

// Función para subir imágenes
function uploadImage($file, $folder) {
    $allowed = ['jpg', 'jpeg', 'png', 'webp'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    if (!in_array($ext, $allowed)) {
        throw new Exception("Formato de imagen no permitido");
    }
    
    $filename = uniqid() . '.' . $ext;
    $destination = "assets/images/$folder/" . $filename;
    
    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        throw new Exception("Error al subir la imagen");
    }
    
    return $filename;
}
?>
