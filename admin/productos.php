<?php
require_once '../includes/config.php';
require_once '../includes/Auth.php';
require_once '../classes/Producto.php';
require_once '../classes/Categoria.php';

// Verificar autenticación y rol de administrador
$auth->requireRole(ROL_ADMIN);

// Instanciar modelos
$productoModel = new Producto();
$categoriaModel = new Categoria();

// Procesar formularios
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    try {
        switch ($action) {
            case 'crear':
                $data = [
                    'nombre' => trim($_POST['nombre'] ?? ''),
                    'descripcion' => trim($_POST['descripcion'] ?? ''),
                    'precio' => floatval($_POST['precio'] ?? 0),
                    'stock' => intval($_POST['stock'] ?? 0),
                    'categoria_id' => intval($_POST['categoria_id'] ?? 0),
                    'destacado' => isset($_POST['destacado']) ? 1 : 0
                ];
                
                // Validaciones básicas
                if (empty($data['nombre'])) {
                    throw new Exception("El nombre es requerido");
                }
                if ($data['precio'] <= 0) {
                    throw new Exception("El precio debe ser mayor a cero");
                }
                if ($data['stock'] < 0) {
                    throw new Exception("El stock no puede ser negativo");
                }
                
                // Procesar imagen
                if (!empty($_FILES['imagen']['name'])) {
                    $imagen = subirImagen($_FILES['imagen'], 'products');
                    $data['imagen'] = $imagen;
                } else {
                    throw new Exception("La imagen es requerida");
                }
                
                $productoModel->crear($data);
                $_SESSION['flash_success'] = "Producto creado correctamente";
                break;
                
            case 'editar':
                $id = intval($_POST['id'] ?? 0);
                $data = [
                    'nombre' => trim($_POST['nombre'] ?? ''),
                    'descripcion' => trim($_POST['descripcion'] ?? ''),
                    'precio' => floatval($_POST['precio'] ?? 0),
                    'stock' => intval($_POST['stock'] ?? 0),
                    'categoria_id' => intval($_POST['categoria_id'] ?? 0),
                    'destacado' => isset($_POST['destacado']) ? 1 : 0
                ];
                
                // Validaciones básicas
                if (empty($data['nombre'])) {
                    throw new Exception("El nombre es requerido");
                }
                if ($data['precio'] <= 0) {
                    throw new Exception("El precio debe ser mayor a cero");
                }
                if ($data['stock'] < 0) {
                    throw new Exception("El stock no puede ser negativo");
                }
                
                // Procesar imagen si se subió una nueva
                if (!empty($_FILES['imagen']['name'])) {
                    $imagen = subirImagen($_FILES['imagen'], 'products');
                    $data['imagen'] = $imagen;
                    
                    // Eliminar imagen anterior si existe
                    $producto = $productoModel->getById($id);
                    if ($producto && !empty($producto['imagen'])) {
                        eliminarImagen($producto['imagen'], 'products');
                    }
                }
                
                $productoModel->actualizar($id, $data);
                $_SESSION['flash_success'] = "Producto actualizado correctamente";
                break;
                
            case 'eliminar':
                $id = intval($_POST['id'] ?? 0);
                $producto = $productoModel->getById($id);
                
                if ($producto && !empty($producto['imagen'])) {
                    eliminarImagen($producto['imagen'], 'products');
                }
                
                $productoModel->eliminar($id);
                $_SESSION['flash_success'] = "Producto eliminado correctamente";
                break;
        }
        
        // Redirigir para evitar reenvío de formulario
        redirect('productos.php');
    } catch (Exception $e) {
        $_SESSION['flash_error'] = $e->getMessage();
        $_SESSION['form_data'] = $_POST;
    }
}

// Obtener todos los productos con información de categoría
$productos = $productoModel->getAllWithCategory();
$categorias = $categoriaModel->getAll();

// Incluir header
include '../includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Gestión de Productos</h1>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCrearProducto">
                    <i class="fas fa-plus"></i> Nuevo Producto
                </button>
            </div>
            
            <?php include '../includes/flash_messages.php'; ?>
            
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Imagen</th>
                            <th>Nombre</th>
                            <th>Precio</th>
                            <th>Stock</th>
                            <th>Categoría</th>
                            <th>Destacado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($productos)): ?>
                            <tr>
                                <td colspan="8" class="text-center">No hay productos registrados</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($productos as $producto): ?>
                                <tr>
                                    <td><?= $producto['id'] ?></td>
                                    <td>
                                        <img src="../assets/images/products/<?= $producto['imagen'] ?>" 
                                             alt="<?= htmlspecialchars($producto['nombre']) ?>" 
                                             width="50" class="img-thumbnail">
                                    </td>
                                    <td><?= htmlspecialchars($producto['nombre']) ?></td>
                                    <td>$<?= number_format($producto['precio'], 2) ?></td>
                                    <td><?= $producto['stock'] ?></td>
                                    <td><?= htmlspecialchars($producto['categoria_nombre'] ?? 'Sin categoría') ?></td>
                                    <td>
                                        <?php if ($producto['destacado']): ?>
                                            <span class="badge bg-success">Sí</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">No</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-warning" 
                                                data-bs-toggle="modal" data-bs-target="#modalEditarProducto"
                                                data-id="<?= $producto['id'] ?>"
                                                data-nombre="<?= htmlspecialchars($producto['nombre']) ?>"
                                                data-descripcion="<?= htmlspecialchars($producto['descripcion']) ?>"
                                                data-precio="<?= $producto['precio'] ?>"
                                                data-stock="<?= $producto['stock'] ?>"
                                                data-categoria_id="<?= $producto['categoria_id'] ?>"
                                                data-destacado="<?= $producto['destacado'] ?>"
                                                data-imagen="<?= $producto['imagen'] ?>">
                                            <i class="fas fa-edit"></i> Editar
                                        </button>
                                        
                                        <form method="post" action="productos.php" class="d-inline">
                                            <input type="hidden" name="action" value="eliminar">
                                            <input type="hidden" name="id" value="<?= $producto['id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-danger" 
                                                    onclick="return confirm('¿Estás seguro de eliminar este producto?')">
                                                <i class="fas fa-trash"></i> Eliminar
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</div>

<!-- Modal para crear producto -->
<div class="modal fade" id="modalCrearProducto" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="post" action="productos.php" enctype="multipart/form-data">
                <input type="hidden" name="action" value="crear">
                
                <div class="modal-header">
                    <h5 class="modal-title">Nuevo Producto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nombre" class="form-label">Nombre *</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" required
                                       value="<?= htmlspecialchars($_SESSION['form_data']['nombre'] ?? '') ?>">
                            </div>
                            <div class="mb-3">
                                <label for="precio" class="form-label">Precio *</label>
                                <input type="number" step="0.01" min="0.01" class="form-control" id="precio" name="precio" required
                                       value="<?= htmlspecialchars($_SESSION['form_data']['precio'] ?? '') ?>">
                            </div>
                            <div class="mb-3">
                                <label for="stock" class="form-label">Stock *</label>
                                <input type="number" min="0" class="form-control" id="stock" name="stock" required
                                       value="<?= htmlspecialchars($_SESSION['form_data']['stock'] ?? '0') ?>">
                            </div>
                            <div class="mb-3">
                                <label for="categoria_id" class="form-label">Categoría</label>
                                <select class="form-select" id="categoria_id" name="categoria_id">
                                    <option value="0">Sin categoría</option>
                                    <?php foreach ($categorias as $cat): ?>
                                        <option value="<?= $cat['id'] ?>" 
                                            <?= (($_SESSION['form_data']['categoria_id'] ?? 0) == $cat['id'] ? 'selected' : '') ?>>
                                            <?= htmlspecialchars($cat['nombre']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="destacado" name="destacado"
                                    <?= (($_SESSION['form_data']['destacado'] ?? 0) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="destacado">Producto destacado</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="descripcion" class="form-label">Descripción</label>
                                <textarea class="form-control" id="descripcion" name="descripcion" rows="5"><?= 
                                    htmlspecialchars($_SESSION['form_data']['descripcion'] ?? '') 
                                ?></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="imagen" class="form-label">Imagen *</label>
                                <input type="file" class="form-control" id="imagen" name="imagen" accept="image/*" required>
                                <div class="form-text">Formatos: JPG, PNG, WEBP. Tamaño máximo: 2MB</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para editar producto -->
<div class="modal fade" id="modalEditarProducto" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="post" action="productos.php" enctype="multipart/form-data">
                <input type="hidden" name="action" value="editar">
                <input type="hidden" name="id" id="edit_id" value="">
                
                <div class="modal-header">
                    <h5 class="modal-title">Editar Producto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_nombre" class="form-label">Nombre *</label>
                                <input type="text" class="form-control" id="edit_nombre" name="nombre" required>
                            </div>
                            <div class="mb-3">
                                <label for="edit_precio" class="form-label">Precio *</label>
                                <input type="number" step="0.01" min="0.01" class="form-control" id="edit_precio" name="precio" required>
                            </div>
                            <div class="mb-3">
                                <label for="edit_stock" class="form-label">Stock *</label>
                                <input type="number" min="0" class="form-control" id="edit_stock" name="stock" required>
                            </div>
                            <div class="mb-3">
                                <label for="edit_categoria_id" class="form-label">Categoría</label>
                                <select class="form-select" id="edit_categoria_id" name="categoria_id">
                                    <option value="0">Sin categoría</option>
                                    <?php foreach ($categorias as $cat): ?>
                                        <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['nombre']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="edit_destacado" name="destacado">
                                <label class="form-check-label" for="edit_destacado">Producto destacado</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_descripcion" class="form-label">Descripción</label>
                                <textarea class="form-control" id="edit_descripcion" name="descripcion" rows="5"></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="edit_imagen" class="form-label">Imagen</label>
                                <input type="file" class="form-control" id="edit_imagen" name="imagen" accept="image/*">
                                <div class="form-text">Dejar en blanco para mantener la imagen actual</div>
                                <div class="mt-2">
                                    <img id="edit_imagen_preview" src="" class="img-thumbnail" style="max-height: 150px;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php 
// Limpiar datos de formulario almacenados en sesión
unset($_SESSION['form_data']);

// Incluir footer
include '../includes/footer.php'; 
?>

<script>
// Script para manejar el modal de edición
document.addEventListener('DOMContentLoaded', function() {
    var modalEditar = document.getElementById('modalEditarProducto');
    
    modalEditar.addEventListener('show.bs.modal', function(event) {
        var button = event.relatedTarget;
        var id = button.getAttribute('data-id');
        var nombre = button.getAttribute('data-nombre');
        var descripcion = button.getAttribute('data-descripcion');
        var precio = button.getAttribute('data-precio');
        var stock = button.getAttribute('data-stock');
        var categoriaId = button.getAttribute('data-categoria_id');
        var destacado = button.getAttribute('data-destacado');
        var imagen = button.getAttribute('data-imagen');
        
        document.getElementById('edit_id').value = id;
        document.getElementById('edit_nombre').value = nombre;
        document.getElementById('edit_descripcion').value = descripcion;
        document.getElementById('edit_precio').value = precio;
        document.getElementById('edit_stock').value = stock;
        document.getElementById('edit_categoria_id').value = categoriaId;
        document.getElementById('edit_destacado').checked = destacado === '1';
        
        // Mostrar imagen actual
        var imgPreview = document.getElementById('edit_imagen_preview');
        imgPreview.src = '../assets/images/products/' + imagen;
        imgPreview.alt = nombre;
    });
    
    // Previsualización de imagen al seleccionar
    document.getElementById('edit_imagen').addEventListener('change', function(e) {
        var file = e.target.files[0];
        if (file) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('edit_imagen_preview').src = e.target.result;
            }
            reader.readAsDataURL(file);
        }
    });
});
</script> 
