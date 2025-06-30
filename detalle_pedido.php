<?php
require_once '../includes/config.php';
require_once '../includes/Auth.php';
require_once '../classes/Pedido.php';

// Verificar autenticación y rol de administrador o ejecutivo
$auth->requireRole([ROL_ADMIN, ROL_EJECUTIVO]);

// Verificar que se haya proporcionado un ID de pedido
if (!isset($_GET['id'])) {
    $_SESSION['flash_error'] = 'No se especificó el pedido';
    redirect('pedidos.php');
}

// Instanciar modelo
$pedidoModel = new Pedido();

// Obtener detalles del pedido
$pedidoId = intval($_GET['id']);
$pedido = $pedidoModel->getByIdWithUser($pedidoId);
$detalles = $pedidoModel->getDetalles($pedidoId);

if (!$pedido) {
    $_SESSION['flash_error'] = 'Pedido no encontrado';
    redirect('pedidos.php');
}

// Incluir header
include '../includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Detalle del Pedido #<?= $pedido['id'] ?></h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="pedidos.php" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Volver a pedidos
                    </a>
                </div>
            </div>
            
            <?php include '../includes/flash_messages.php'; ?>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Información del Pedido</h5>
                        </div>
                        <div class="card-body">
                            <dl class="row">
                                <dt class="col-sm-4">Fecha:</dt>
                                <dd class="col-sm-8"><?= date('d/m/Y H:i', strtotime($pedido['fecha_pedido'])) ?></dd>
                                
                                <dt class="col-sm-4">Estado:</dt>
                                <dd class="col-sm-8">
                                    <span class="badge 
                                        <?= $pedido['estado'] === ESTADO_PENDIENTE ? 'bg-warning' : 
                                           ($pedido['estado'] === ESTADO_APROBADO ? 'bg-info' : 
                                           ($pedido['estado'] === ESTADO_ENVIADO ? 'bg-primary' : 
                                           ($pedido['estado'] === ESTADO_COMPLETADO ? 'bg-success' : 'bg-danger'))) ?>">
                                        <?= ucfirst($pedido['estado']) ?>
                                    </span>
                                </dd>
                                
                                <dt class="col-sm-4">Total:</dt>
                                <dd class="col-sm-8">$<?= number_format($pedido['total'], 2) ?></dd>
                                
                                <dt class="col-sm-4">Método de Pago:</dt>
                                <dd class="col-sm-8"><?= ucfirst($pedido['metodo_pago']) ?></dd>
                            </dl>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Información del Cliente</h5>
                        </div>
                        <div class="card-body">
                            <dl class="row">
                                <dt class="col-sm-4">Nombre:</dt>
                                <dd class="col-sm-8"><?= htmlspecialchars($pedido['usuario_nombre']) ?></dd>
                                
                                <dt class="col-sm-4">Email:</dt>
                                <dd class="col-sm-8"><?= htmlspecialchars($pedido['usuario_email']) ?></dd>
                                
                                <dt class="col-sm-4">Teléfono:</dt>
                                <dd class="col-sm-8"><?= htmlspecialchars($pedido['usuario_telefono'] ?? 'No especificado') ?></dd>
                                
                                <dt class="col-sm-4">Dirección:</dt>
                                <dd class="col-sm-8"><?= htmlspecialchars($pedido['direccion_entrega']) ?></dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Productos</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>Precio Unitario</th>
                                    <th>Cantidad</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($detalles as $detalle): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <?php if (!empty($detalle['imagen'])): ?>
                                                    <img src="../assets/images/products/<?= $detalle['imagen'] ?>" 
                                                         alt="<?= htmlspecialchars($detalle['nombre']) ?>" 
                                                         width="50" class="me-3 img-thumbnail">
                                                <?php endif; ?>
                                                <div>
                                                    <h6 class="mb-0"><?= htmlspecialchars($detalle['nombre']) ?></h6>
                                                    <small class="text-muted"><?= htmlspecialchars($detalle['descripcion']) ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>$<?= number_format($detalle['precio'], 2) ?></td>
                                        <td><?= $detalle['cantidad'] ?></td>
                                        <td>$<?= number_format($detalle['precio'] * $detalle['cantidad'], 2) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="text-end"><strong>Total:</strong></td>
                                    <td>$<?= number_format($pedido['total'], 2) ?></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<?php 
// Incluir footer
include '../includes/footer.php'; 
?>
