<?php
require_once 'Database.php';
require_once 'funciones.php';

class Auth {
    private $db;

    public function __construct() {
        $dbInstance = Database::getInstance();
        $this->db = $dbInstance->getConnection();
    }

    public function login($email, $password) {
        $stmt = $this->db->prepare("SELECT * FROM usuarios WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['rol'];
            $_SESSION['user_name'] = $user['nombre'];
            return true;
        }
        return false;
    }

    public function logout() {
        session_unset();
        session_destroy();
    }

    public function isAuthenticated() {
        return isset($_SESSION['user_id']);
    }

    public function getUser() {
        if (!$this->isAuthenticated()) return null;
        
        $stmt = $this->db->prepare("SELECT * FROM usuarios WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        return $stmt->fetch();
    }

    public function hasRole($role) {
        return $this->isAuthenticated() && $_SESSION['user_role'] === $role;
    }

    public function requireAuth() {
        if (!$this->isAuthenticated()) {
            $_SESSION['redirect_to'] = $_SERVER['REQUEST_URI'];
            $_SESSION['flash_error'] = 'Debes iniciar sesión para acceder a esta página';
            redirect('login.php');
        }
    }

    public function requireRole($role) {
        $this->requireAuth();
        if (!$this->hasRole($role)) {
            $_SESSION['flash_error'] = 'No tienes permiso para acceder a esta área';
            redirect('index.php');
        }
    }
}

// Instancia global de Auth
$auth = new Auth();
?>