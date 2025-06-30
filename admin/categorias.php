<?php
require_once '../includes/config.php';
require_once '../includes/Auth.php';
require_once '../classes/Categoria.php';

// Verificar autenticación y rol de administrador
$auth->requireRole(ROL_ADMIN);

// Instanciar modelo de categorías
$categoriaModel = new Categoria();

// Procesar formularios
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    try {
        switch ($action) {
            case 'crear':
                $nombre = trim($_POST['nombre'] ?? '');
                $descripcion = trim($_POST['descripcion'] ?? '');
                
                if (empty($nombre)) {
                    throw new Exception("El nombre es requerido");
                }
                
                $categoriaModel->crear([
                    'nombre' => $nombre,
                    'descripcion' => $descripcion
                ]);
                
                $_SESSION['flash_success'] = "Categoría creada correctamente";
                break;
                
            case 'editar':
                $id = $_POST['id'] ?? 0;
                $nombre = trim($_POST['nombre'] ?? '');
                $descripcion = trim($_POST['descripcion'] ?? '');
                
                if (empty($nombre)) {
                    throw new Exception("El nombre es requerido");
                }
                
                $categoriaModel->actualizar($id, [
                    'nombre' => $nombre,
                    'descripcion' => $descripcion
                ]);
                
                $_SESSION['flash_success'] = "Categoría actualizada correctamente";
                break;
                
            case 'eliminar':
                $id = $_POST['id'] ?? 0;
                $categoriaModel->eliminar($id);
                $_SESSION['flash_success'] = "Categoría eliminada correctamente";
                break;
        }
        
        // Redirigir para evitar reenvío de formulario
        redirect('categorias.php');
    } catch (Exception $e) {
        $_SESSION['flash_error'] = $e->getMessage();
        $_SESSION['form_data'] = $_POST;
    }
}

// Obtener todas las categorías
$categorias = $categoriaModel->getAll();

// Incluir header
include '../includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Gestión de Categorías</h1>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCrearCategoria">
                    <i class="fas fa-plus"></i> Nueva Categoría
                </button>
            </div>
            
            <?php include '../includes/flash_messages.php'; ?>
            
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($categorias)): ?>
                            <tr>
                                <td colspan="4" class="text-center">No hay categorías registradas</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($categorias as $categoria): ?>
                                <tr>
                                    <td><?= $categoria['id'] ?></td>
                                    <td><?= htmlspecialchars($categoria['nombre']) ?></td>
                                    <td><?= htmlspecialchars($categoria['descripcion']) ?></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-warning" 
                                                data-bs-toggle="modal" data-bs-target="#modalEditarCategoria"
                                                data-id="<?= $categoria['id'] ?>"
                                                data-nombre="<?= htmlspecialchars($categoria['nombre']) ?>"
                                                data-descripcion="<?= htmlspecialchars($categoria['descripcion']) ?>">
                                            <i class="fas fa-edit"></i> Editar
                                        </button>
                                        
                                        <form method="post" action="categorias.php" class="d-inline">
                                            <input type="hidden" name="action" value="eliminar">
                                            <input type="hidden" name="id" value="<?= $categoria['id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-danger" 
                                                    onclick="return confirm('¿Estás seguro de eliminar esta categoría?')">
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

<!-- Modal para crear categoría -->
<div class="modal fade" id="modalCrearCategoria" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post" action="categorias.php">
                <input type="hidden" name="action" value="crear">
                
                <div class="modal-header">
                    <h5 class="modal-title">Nueva Categoría</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre *</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" required
                               value="<?= htmlspecialchars($_SESSION['form_data']['nombre'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <textarea class="form-control" id="descripcion" name="descripcion" rows="3"><?= 
                            htmlspecialchars($_SESSION['form_data']['descripcion'] ?? '') 
                        ?></textarea>
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

<!-- Modal para editar categoría -->
<div class="modal fade" id="modalEditarCategoria" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post" action="categorias.php">
                <input type="hidden" name="action" value="editar">
                <input type="hidden" name="id" id="edit_id" value="">
                
                <div class="modal-header">
                    <h5 class="modal-title">Editar Categoría</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_nombre" class="form-label">Nombre *</label>
                        <input type="text" class="form-control" id="edit_nombre" name="nombre" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_descripcion" class="form-label">Descripción</label>
                        <textarea class="form-control" id="edit_descripcion" name="descripcion" rows="3"></textarea>
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
    var modalEditar = document.getElementById('modalEditarCategoria');
    
    modalEditar.addEventListener('show.bs.modal', function(event) {
        var button = event.relatedTarget;
        var id = button.getAttribute('data-id');
        var nombre = button.getAttribute('data-nombre');
        var descripcion = button.getAttribute('data-descripcion');
        
        document.getElementById('edit_id').value = id;
        document.getElementById('edit_nombre').value = nombre;
        document.getElementById('edit_descripcion').value = descripcion;
    });
});
</script>
