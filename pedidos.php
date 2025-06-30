<?php
require_once 'Database.php';

class Pedido {
    private $db;

    public function __construct() {
        $dbInstance = Database::getInstance();
        $this->db = $dbInstance->getConnection();
    }

    public function getAllWithUser($estado = null, $usuarioId = null) {
        $sql = "SELECT p.*, u.nombre as usuario_nombre, u.email as usuario_email 
                FROM pedidos p 
                JOIN usuarios u ON p.usuario_id = u.id";
        
        $conditions = [];
        $params = [];
        
        if ($estado) {
            $conditions[] = "p.estado = ?";
            $params[] = $estado;
        }
        
        if ($usuarioId) {
            $conditions[] = "p.usuario_id = ?";
            $params[] = $usuarioId;
        }
        
        if (!empty($conditions))
