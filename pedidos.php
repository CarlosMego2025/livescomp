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
                        $sql .= " WHERE " . implode(" AND ", $conditions);
        }
        
        $sql .= " ORDER BY p.fecha_pedido DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByIdWithUser($id) {
        $sql = "SELECT p.*, u.nombre as usuario_nombre, u.email as usuario_email, u.telefono as usuario_telefono 
                FROM pedidos p 
                JOIN usuarios u ON p.usuario_id = u.id 
                WHERE p.id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getDetalles($pedidoId) {
        $sql = "SELECT d.*, p.nombre, p.descripcion, p.imagen 
                FROM detalles_pedido d 
                JOIN productos p ON d.producto_id = p.id 
                WHERE d.pedido_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$pedidoId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function actualizarEstado($id, $estado) {
        $sql = "UPDATE pedidos SET estado = ?, updated_at = NOW() WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$estado, $id]);
    }

    public function count() {
        $stmt = $this->db->query("SELECT COUNT(*) FROM pedidos");
        return $stmt->fetchColumn();
    }

    public function getTotalVentas() {
        $stmt = $this->db->query("SELECT SUM(total) FROM pedidos WHERE estado = '" . ESTADO_COMPLETADO . "'");
        return $stmt->fetchColumn() ?? 0;
    }

    public function getRecent($limit = 5) {
        $sql = "SELECT p.id, p.total, p.estado, p.fecha_pedido, u.nombre as usuario_nombre 
                FROM pedidos p 
                JOIN usuarios u ON p.usuario_id = u.id 
                ORDER BY p.fecha_pedido DESC 
                LIMIT ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByUser($userId, $limit = null) {
        $sql = "SELECT * FROM pedidos WHERE usuario_id = ? ORDER BY fecha_pedido DESC";
        
        if ($limit) {
            $sql .= " LIMIT ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$userId, $limit]);
        } else {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$userId]);
        }
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
