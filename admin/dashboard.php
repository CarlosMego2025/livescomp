 <?php
require_once '../includes/config.php';
require_once '../includes/Auth.php';
require_once '../classes/Pedido.php';
require_once '../classes/Producto.php';
require_once '../classes/Usuario.php';

// Verificar autenticación y rol de administrador
$auth->requireRole(ROL_ADMIN);

// Instanciar modelos
$pedidoModel = new Pedido();
$productoModel = new Producto();
$usuarioModel = new Usuario();

// Obtener estadísticas
$stats = [
    'productos' => $productoModel->count(),
    'usuarios' => $usuarioModel->count(),
    'pedidos' => $pedidoModel->count(),
    'ventas' => $pedidoModel->getTotalVentas(),
    'pedidos_recientes' => $pedidoModel->getRecent(5),
    'productos_bajo_stock' => $productoModel->getLowStock(5)
];

// Incluir header
include '../includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Dashboard</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <div class="btn-group me-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary">Exportar</button>
                    </div>
                </div>
            </div>
            
            <?php include '../includes/flash_messages.php'; ?>
            
            <!-- Estadísticas -->
            <div class="row mb-4">
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        Productos</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stats['productos'] ?></div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-boxes fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-success shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                        Usuarios</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stats['usuarios'] ?></div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-users fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-info shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                        Pedidos</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stats['pedidos'] ?></div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-warning shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                        Ventas Totales</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">$<?= number_format($stats['ventas'], 2) ?></div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Gráficos y más información -->
            <div class="row">
                <!-- Últimos pedidos -->
                <div class="col-lg-6 mb-4">
                    <div class="card shadow">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Últimos Pedidos</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Cliente</th>
                                            <th>Total</th>
                                            <th>Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($stats['pedidos_recientes'] as $pedido): ?>
                                            <tr>
                                                <td><a href="detalle_pedido.php?id=<?= $pedido['id'] ?>">#<?= $pedido['id'] ?></a></td>
                                                <td><?= htmlspecialchars($pedido['usuario_nombre']) ?></td>
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
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Productos con bajo stock -->
                <div class="col-lg-6 mb-4">
                    <div class="card shadow">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Productos con Bajo Stock</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Producto</th>
                                            <th>Stock</th>
                                            <th>Precio</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($stats['productos_bajo_stock'] as $producto): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($producto['nombre']) ?></td>
                                                <td>
                                                    <span class="badge <?= $producto['stock'] < 5 ? 'bg-danger' : 'bg-warning' ?>">
                                                        <?= $producto['stock'] ?>
                                                    </span>
                                                </td>
                                                <td>$<?= number_format($producto['precio'], 2) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
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
