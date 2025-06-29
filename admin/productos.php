<?php
require_once '../includes/auth.php';
require_once '../includes/funciones.php';
requireAdmin();

$db = new Database();
$conn = $db->getConnection();

// Obtener categorías
$categorias = $conn->query("SELECT * FROM categorias")->fetchAll(PDO::FETCH_ASSOC);

// Procesar formulario de producto
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = sanitize($_POST['nombre']);
    $categoria_id = (int)$_POST['categoria_id'];
    $descripcion = sanitize($_POST['descripcion']);
    $precio = (float)$_POST['precio'];
    $precio_oferta = !empty($_POST['precio_oferta']) ? (float)$_POST['precio_oferta'] : null;
    $stock = (int)$_POST['stock'];
    $destacado = isset($_POST['destacado']) ? 1 : 0;
    
    // Manejar imagen
    $imagen = '';
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['imagen']['tmp_name'];
        $fileName = $_FILES['imagen']['name'];
        $fileType = $_FILES['imagen']['type'];
        
        // Validar tipo de imagen
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (in_array($fileType, $allowedTypes)) {
            $uploadDir = '../assets/img/productos/';
            $fileName = uniqid() . '_' . $fileName;
            $destPath = $uploadDir . $fileName;
            
            if (move_uploaded_file($fileTmpPath, $destPath)) {
                $imagen = $fileName;
            }
        }
    }
    
    // Insertar producto
    $sql = "INSERT INTO productos (categoria_id, nombre, descripcion, precio, precio_oferta, stock, imagen, destacado) 
            VALUES (:categoria_id, :nombre, :descripcion, :precio, :precio_oferta, :stock, :imagen, :destacado)";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':categoria_id' => $categoria_id,
        ':nombre' => $nombre,
        ':descripcion' => $descripcion,
        ':precio' => $precio,
        ':precio_oferta' => $precio_oferta,
        ':stock' => $stock,
        ':imagen' => $imagen,
        ':destacado' => $destacado
    ]);
    
    $_SESSION['flash_success'] = 'Producto creado exitosamente';
    redirect('productos.php');
}

// Obtener todos los productos
$productos = $conn->query("
    SELECT p.*, c.nombre as categoria_nombre 
    FROM productos p 
    JOIN categorias c ON p.categoria_id = c.id
")->fetchAll(PDO::FETCH_ASSOC);

include '../includes/header-admin.php';
?>

<div class="container mt-4">
    <h2>Gestión de Productos</h2>
    
    <!-- Formulario para crear producto -->
    <div class="card mb-4">
        <div class="card-header">Agregar Nuevo Producto</div>
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Nombre del Producto</label>
                            <input type="text" name="nombre" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Categoría</label>
                            <select name="categoria_id" class="form-control" required>
                                <?php foreach($categorias as $categoria): ?>
                                    <option value="<?= $categoria['id'] ?>"><?= $categoria['nombre'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label>Precio</label>
                            <input type="number" step="0.01" name="precio" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Precio de Oferta (opcional)</label>
                            <input type="number" step="0.01" name="precio_oferta" class="form-control">
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Descripción</label>
                            <textarea name="descripcion" class="form-control" rows="3"></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label>Stock Disponible</label>
                            <input type="number" name="stock" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Imagen del Producto</label>
                            <input type="file" name="imagen" class="form-control-file">
                        </div>
                        
                        <div class="form-check">
                            <input type="checkbox" name="destacado" class="form-check-input" id="destacado">
                            <label class="form-check-label" for="destacado">Destacado (mostrar en página principal)</label>
                        </div>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary mt-3">Guardar Producto</button>
            </form>
        </div>
    </div>
    
    <!-- Lista de productos -->
    <div class="card">
        <div class="card-header">Lista de Productos</div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Imagen</th>
                            <th>Nombre</th>
                            <th>Categoría</th>
                            <th>Precio</th>
                            <th>Stock</th>
                            <th>Destacado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($productos as $producto): ?>
                            <tr>
                                <td><?= $producto['id'] ?></td>
                                <td>
                                    <?php if($producto['imagen']): ?>
                                        <img src="../assets/img/productos/<?= $producto['imagen'] ?>" alt="<?= $producto['nombre'] ?>" width="50">
                                    <?php endif; ?>
                                </td>
                                <td><?= $producto['nombre'] ?></td>
                                <td><?= $producto['categoria_nombre'] ?></td>
                                <td><?= formatPrice($producto['precio']) ?></td>
                                <td><?= $producto['stock'] ?></td>
                                <td><?= $producto['destacado'] ? 'Sí' : 'No' ?></td>
                                <td>
                                    <a href="editar_producto.php?id=<?= $producto['id'] ?>" class="btn btn-sm btn-warning">Editar</a>
                                    <a href="eliminar_producto.php?id=<?= $producto['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro?')">Eliminar</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer-admin.php'; ?>