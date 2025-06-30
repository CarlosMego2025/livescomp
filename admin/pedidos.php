<?php
require_once '../includes/config.php';
require_once '../includes/Auth.php';
require_once '../classes/Pedido.php';
require_once '../classes/Usuario.php';

// Verificar autenticación y rol de administrador o ejecutivo
$auth->requireRole([ROL_ADMIN, ROL_EJECUTIVO]);

// Instanciar modelos
$pedidoModel = new Pedido();
$usuarioModel = new Usuario();

// Procesar cambios de estado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    try {
        switch ($action) {
            case 'cambiar_estado':
                $pedidoId = intval($_POST['pedido_id'] ?? 0);
                $nuevoEstado = trim($_POST['nuevo_estado'] ?? '');
                
                if (empty($nuevoEstado)) {
                    throw new Exception("Estado no válido");
                }
                
                $pedidoModel->actualizarEstado($pedidoId, $nuevoEstado);
                $_SESSION['flash_success'] = "Estado del pedido actualizado correctamente";
                break;
        }
        
        // Redirigir para evitar reenvío de formulario
        redirect('pedidos.php');
    } catch (Exception $e) {
        $_SESSION['flash_error'] = $e->getMessage();
    }
}

// Obtener parámetros de filtrado
$estado = $_GET['estado'] ?? null;
$usuarioId = $_GET['usuario_id'] ?? null;

// Obtener pedidos según filtros
$pedidos = $pedidoModel->getAllWithUser($estado, $usuarioId);
$usuarios = $usuarioModel->getAll();

// Incluir header
include '../includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Gestión de Pedidos</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <div class="btn-group me-2">
                        <a href="pedidos.php" class="btn btn-sm btn-outline-secondary <?= !$estado ? 'active' : '' ?>">
                            Todos
                        </a>
                        <?php foreach ([ESTADO_PENDIENTE, ESTADO_APROBADO, ESTADO_ENVIADO, ESTADO_COMPLETADO, ESTADO_CANCELADO] as $est): ?>
                            <a href="pedidos.php?estado=<?= $est ?>" 
                               class="btn btn-sm btn-outline-secondary <?= $estado === $est ? 'active' : '' ?>">
                                <?= ucfirst($est) ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            
            <?php include '../includes/flash_messages.php'; ?>
            
            <!-- Filtros -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="get" action="pedidos.php" class="row g-3">
                        <div class="col-md-6">
                            <label for="usuario_id" class="form-label">Filtrar por cliente</label>
                            <select class="form-select" id="usuario_id" name="usuario_id">
                                <option value="">Todos los clientes</option>
                                <?php foreach ($usuarios as $user): ?>
                                    <option value="<?= $user['id'] ?>" <?= $usuarioId == $user['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($user['nombre']) ?> (<?= htmlspecialchars($user['email']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="estado" class="form-label">Filtrar por estado</label>
                            <select class="form-select" id="estado" name="estado">
                                <option value="">Todos los estados</option>
                                <?php foreach ([ESTADO_PENDIENTE, ESTADO_APROBADO, ESTADO_ENVIADO, ESTADO_COMPLETADO, ESTADO_CANCELADO] as $est): ?>
                                    <option value="<?= $est ?>" <?= $estado === $est ? 'selected' : '' ?>>
                                        <?= ucfirst($est) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary">Filtrar</button>
                            <?php if ($estado || $usuarioId): ?>
                                <a href="pedidos.php" class="btn btn-outline-secondary ms-2">Limpiar</a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Cliente</th>
                            <th>Fecha</th>
                            <th>Total</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($pedidos)): ?>
                            <tr>
                                <td colspan="6" class="text-center">No hay pedidos registrados</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($pedidos as $pedido): ?>
                                <tr>
                                    <td><?= $pedido['id'] ?></td>
                                    <td>
                                        <?= htmlspecialchars($pedido['usuario_nombre']) ?><br>
                                        <small><?= htmlspecialchars($pedido['usuario_email']) ?></small>
                                    </td>
                                    <td><?= date('d/m/Y H:i', strtotime($pedido['fecha_pedido'])) ?></td>
                                    <td>$<?= number_format($pedido['total'], 2) ?></td>
                                    <td>
                                        <span class="badge 
                                            <?= $pedido['estado'] === ESTADO_PENDIENTE ? 'bg-warning' : 
                                               ($pedido['estado'] === ESTADO_APROBADO ? 'bg-info' : 
                                               ($pedido['estado'] === ESTADO_ENVIADO ? 'bg-primary' : 
                                               ($pedido['estado'] === ESTADO_COMPLETADO ? 'bg-success' : 'bg-danger'))) ?>">
                                            <?= ucfirst($pedido['estado']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="detalle_pedido.php?id=<?= $pedido['id'] ?>" class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i> Ver
                                        </a>
                                        
                                        <?php if ($auth->hasRole(ROL_ADMIN) || ($auth->hasRole(ROL_EJECUTIVO) && $pedido['estado'] !== ESTADO_COMPLETADO && $pedido['estado'] !== ESTADO_CANCELADO)): ?>
                                            <button type="button" class="btn btn-sm btn-warning" 
                                                    data-bs-toggle="modal" data-bs-target="#modalCambiarEstado"
                                                    data-pedido-id="<?= $pedido['id'] ?>"
                                                    data-pedido-estado="<?= $pedido['estado'] ?>">
                                                <i class="fas fa-sync-alt"></i> Cambiar Estado
                                            </button>
                                        <?php endif; ?>
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

<!-- Modal para cambiar estado -->
<div class="modal fade" id="modalCambiarEstado" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post" action="pedidos.php">
                <input type="hidden" name="action" value="cambiar_estado">
                <input type="hidden" name="pedido_id" id="pedido_id" value="">
                
                <div class="modal-header">
                    <h5 class="modal-title">Cambiar Estado del Pedido</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="nuevo_estado" class="form-label">Nuevo Estado</label>
                        <select class="form-select" id="nuevo_estado" name="nuevo_estado" required>
                            <?php foreach ([ESTADO_PENDIENTE, ESTADO_APROBADO, ESTADO_ENVIADO, ESTADO_COMPLETADO, ESTADO_CANCELADO] as $est): ?>
                                <option value="<?= $est ?>"><?= ucfirst($est) ?></option>
                            <?php endforeach; ?>
                        </select>
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
// Incluir footer
include '../includes/footer.php'; 
?>

<script>
// Script para manejar el modal de cambio de estado
document.addEventListener('DOMContentLoaded', function() {
    var modalCambiarEstado = document.getElementById('modalCambiarEstado');
    
    modalCambiarEstado.addEventListener('show.bs.modal', function(event) {
        var button = event.relatedTarget;
        var pedidoId = button.getAttribute('data-pedido-id');
        var pedidoEstado = button.getAttribute('data-pedido-estado');
        
        document.getElementById('pedido_id').value = pedidoId;
        document.getElementById('nuevo_estado').value = pedidoEstado;
    });
});
</script>
