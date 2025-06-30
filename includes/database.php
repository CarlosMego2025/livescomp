<?php
require_once 'config.php';

class Database {
    private static $instance = null;
    private $conn;

    private function __construct() {
        try {
            $dsn = "mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8";
            $this->conn = new PDO($dsn, DB_USER, DB_PASS);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error de conexión: " . $e->getMessage());
            die("Error al conectar con la base de datos. Por favor, inténtalo más tarde.");
        }
    }

    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->conn;
    }

    public function query($sql, $params = []) {
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            error_log("Error en consulta SQL: " . $e->getMessage() . "\nConsulta: " . $sql);
            return false;
        }
    }

    public function lastInsertId() {
        return $this->conn->lastInsertId();
    }
}
$host = getenv("DB_HOST");
$port = getenv("DB_PORT");
$dbname = getenv("DB_NAME");
$user = getenv("DB_USER");
$pass = getenv("DB_PASSWORD");

try {
    $conn = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Conexión exitosa";
} catch (PDOException $e) {
    echo "Error de conexión: " . $e->getMessage();
    exit;
}
?>