<?php
require_once '../includes/config.php';
require_once '../includes/Auth.php';
require_once '../classes/Usuario.php';

// Verificar autenticación y rol de administrador
$auth->requireRole(ROL_ADMIN);

// Instanciar modelo de usuarios
$usuarioModel = new Usuario();

// Procesar formularios
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    try {
        switch ($action) {
            case 'editar':
                $id = intval($_POST['id'] ?? 0);
                $data = [
                    'nombre' => trim($_POST['nombre'] ?? ''),
                    'email' => trim($_POST['email'] ?? ''),
                    'rol' => trim($_POST['rol'] ?? ROL_CLIENTE),
                    'direccion' => trim($_POST['direccion'] ?? ''),
                    'telefono' => trim($_POST['telefono'] ?? '')
                ];
                
                // Validaciones
                if (empty($data['nombre']) || empty($data['email'])) {
                    throw new Exception("Nombre y email son requeridos");
                }
                
                // Actualizar usuario
                $usuarioModel->actualizar($id, $data);
                $_SESSION['flash_success'] = "Usuario actualizado correctamente";
                break;
                
            case 'eliminar':
                $id = intval($_POST['id'] ?? 0);
                
                // No permitir eliminarse a sí mismo
                if ($id == $_SESSION['user_id']) {
                    throw new Exception("No puedes eliminar tu propia cuenta");
                }
                
                $usuarioModel->eliminar($id);
                $_SESSION['flash_success'] = "Usuario eliminado correctamente";
                break;
        }
        
        // Redirigir para evitar reenvío de formulario
        redirect('usuarios.php');
    } catch (Exception $e) {
        $_SESSION['flash_error'] = $e->getMessage();
        $_SESSION['form_data'] = $_POST;
    }
}

// Obtener todos los usuarios
$usuarios = $usuarioModel->getAll();

// Incluir header
include '../includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Gestión de Usuarios</h1>
            </div>
            
            <?php include '../includes/flash_messages.php'; ?>
            
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Rol</th>
                            <th>Registro</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($usuarios)): ?>
                            <tr>
                                <td colspan="6" class="text-center">No hay usuarios registrados</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($usuarios as $usuario): ?>
                                <tr>
                                    <td><?= $usuario['id'] ?></td>
                                    <td><?= htmlspecialchars($usuario['nombre']) ?></td>
                                    <td><?= htmlspecialchars($usuario['email']) ?></td>
                                    <td>
                                        <span class="badge <?= $usuario['rol'] === ROL_ADMIN ? 'bg-danger' : ($usuario['rol'] === ROL_EJECUTIVO ? 'bg-warning' : 'bg-secondary') ?>">
                                            <?= ucfirst($usuario['rol']) ?>
                                        </span>
                                    </td>
                                    <td><?= date('d/m/Y', strtotime($usuario['fecha_registro'])) ?></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-warning" 
                                                data-bs-toggle="modal" data-bs-target="#modalEditarUsuario"
                                                data-id="<?= $usuario['id'] ?>"
                                                data-nombre="<?= htmlspecialchars($usuario['nombre']) ?>"
                                                data-email="<?= htmlspecialchars($usuario['email']) ?>"
                                                data-rol="<?= $usuario['rol'] ?>"
                                                data-direccion="<?= htmlspecialchars($usuario['direccion']) ?>"
                                                data-telefono="<?= htmlspecialchars($usuario['telefono']) ?>">
                                            <i class="fas fa-edit"></i> Editar
                                        </button>
                                        
                                        <form method="post" action="usuarios.php" class="d-inline">
                                            <input type="hidden" name="action" value="eliminar">
                                            <input type="hidden" name="id" value="<?= $usuario['id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-danger" 
                                                    onclick="return confirm('¿Estás seguro de eliminar este usuario?')"
                                                    <?= $usuario['id'] == $_SESSION['user_id'] ? 'disabled' : '' ?>>
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

<!-- Modal para editar usuario -->
<div class="modal fade" id="modalEditarUsuario" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post" action="usuarios.php">
                <input type="hidden" name="action" value="editar">
                <input type="hidden" name="id" id="edit_id" value="">
                
                <div class="modal-header">
                    <h5 class="modal-title">Editar Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_nombre" class="form-label">Nombre *</label>
                        <input type="text" class="form-control" id="edit_nombre" name="nombre" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_email" class="form-label">Email *</label>
                        <input type="email" class="form-control" id="edit_email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_rol" class="form-label">Rol *</label>
                        <select class="form-select" id="edit_rol" name="rol" required>
                            <option value="<?= ROL_CLIENTE ?>">Cliente</option>
                            <option value="<?= ROL_EJECUTIVO ?>">Ejecutivo</option>
                            <option value="<?= ROL_ADMIN ?>">Administrador</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_direccion" class="form-label">Dirección</label>
                        <textarea class="form-control" id="edit_direccion" name="direccion" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="edit_telefono" class="form-label">Teléfono</label>
                        <input type="text" class="form-control" id="edit_telefono" name="telefono">
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
    var modalEditar = document.getElementById('modalEditarUsuario');
    
    modalEditar.addEventListener('show.bs.modal', function(event) {
        var button = event.relatedTarget;
        var id = button.getAttribute('data-id');
        var nombre = button.getAttribute('data-nombre');
        var email = button.getAttribute('data-email');
        var rol = button.getAttribute('data-rol');
        var direccion = button.getAttribute('data-direccion');
        var telefono = button.getAttribute('data-telefono');
        
        document.getElementById('edit_id').value = id;
        document.getElementById('edit_nombre').value = nombre;
        document.getElementById('edit_email').value = email;
        document.getElementById('edit_rol').value = rol;
        document.getElementById('edit_direccion').value = direccion;
        document.getElementById('edit_telefono').value = telefono;
    });
});
</script>
