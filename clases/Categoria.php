<?php
require_once 'Database.php';

class Categoria {
    private $db;

    public function __construct() {
        $dbInstance = Database::getInstance();
        $this->db = $dbInstance->getConnection();
    }

    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM categorias ORDER BY nombre");
        return $stmt->fetchAll();
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM categorias WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function crear($data) {
        $sql = "INSERT INTO categorias (nombre, descripcion) VALUES (?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$data['nombre'], $data['descripcion']]);
    }

    public function actualizar($id, $data) {
        $sql = "UPDATE categorias SET nombre = ?, descripcion = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$data['nombre'], $data['descripcion'], $id]);
    }

    public function eliminar($id) {
        // Verificar si hay productos asociados
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM productos WHERE categoria_id = ?");
        $stmt->execute([$id]);
        $count = $stmt->fetchColumn();

        if ($count > 0) {
            throw new Exception("No se puede eliminar la categoría porque tiene productos asociados");
        }

        $stmt = $this->db->prepare("DELETE FROM categorias WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
