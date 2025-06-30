 <?php
require_once 'Database.php';

class Producto {
    private $db;

    public function __construct() {
        $dbInstance = Database::getInstance();
        $this->db = $dbInstance->getConnection();
    }

    public function getAll($limit = null, $offset = null) {
        $sql = "SELECT p.*, c.nombre as categoria_nombre 
                FROM productos p 
                LEFT JOIN categorias c ON p.categoria_id = c.id";
        
        if ($limit !== null) {
            $sql .= " LIMIT :limit";
            if ($offset !== null) {
                $sql .= " OFFSET :offset";
            }
        }
        
        $stmt = $this->db->prepare($sql);
        
        if ($limit !== null) {
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            if ($offset !== null) {
                $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
            }
        }
        
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT p.*, c.nombre as categoria_nombre 
                                   FROM productos p 
                                   LEFT JOIN categorias c ON p.categoria_id = c.id 
                                   WHERE p.id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getByCategory($categoryId) {
        $stmt = $this->db->prepare("SELECT * FROM productos WHERE categoria_id = ?");
        $stmt->execute([$categoryId]);
        return $stmt->fetchAll();
    }

    public function getFeatured($limit = 4) {
        $stmt = $this->db->prepare("SELECT * FROM productos WHERE destacado = 1 LIMIT ?");
        $stmt->bindValue(1, (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function create($data) {
        $sql = "INSERT INTO productos (nombre, descripcion, precio, stock, categoria_id, imagen, destacado) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['nombre'],
            $data['descripcion'],
            $data['precio'],
            $data['stock'],
            $data['categoria_id'],
            $data['imagen'],
            $data['destacado'] ?? 0
        ]);
    }
    public function getAllWithCategory() {
    $sql = "SELECT p.*, c.nombre as categoria_nombre 
            FROM productos p 
            LEFT JOIN categorias c ON p.categoria_id = c.id
            ORDER BY p.nombre";
    $stmt = $this->db->query($sql);
    return $stmt->fetchAll();
}

public function crear($data) {
    $sql = "INSERT INTO productos (nombre, descripcion, precio, stock, categoria_id, imagen, destacado) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $this->db->prepare($sql);
    return $stmt->execute([
        $data['nombre'],
        $data['descripcion'],
        $data['precio'],
        $data['stock'],
        $data['categoria_id'],
        $data['imagen'],
        $data['destacado']
    ]);
}

public function actualizar($id, $data) {
    $sql = "UPDATE productos SET 
            nombre = ?, 
            descripcion = ?, 
            precio = ?, 
            stock = ?, 
            categoria_id = ?, 
            destacado = ?";
    
    // Si hay nueva imagen, la agregamos a la consulta
    if (!empty($data['imagen'])) {
        $sql .= ", imagen = ?";
    }
    
    $sql .= " WHERE id = ?";
    
    $params = [
        $data['nombre'],
        $data['descripcion'],
        $data['precio'],
        $data['stock'],
        $data['categoria_id'],
        $data['destacado']
    ];
    
    if (!empty($data['imagen'])) {
        $params[] = $data['imagen'];
    }
    
    $params[] = $id;
    
    $stmt = $this->db->prepare($sql);
    return $stmt->execute($params);
}

public function eliminar($id) {
    $stmt = $this->db->prepare("DELETE FROM productos WHERE id = ?");
    return $stmt->execute([$id]);
}
    // Más métodos según necesites (update, delete, search, etc.)
}
?>
