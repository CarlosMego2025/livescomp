<?php
require_once '../includes/config.php';
require_once '../includes/Auth.php';

// Requerir rol de administrador
$auth->requireRole(ROL_ADMIN);

// Obtener estadísticas
$db = Database::getInstance()->getConnection();
$stats = [
    'productos' => $db->query("SELECT COUNT(*) FROM productos")->fetchColumn(),
    'usuarios' => $db->query("SELECT COUNT(*) FROM usuarios")->fetchColumn(),
    'pedidos' => $db->query("SELECT COUNT(*) FROM pedidos")->fetchColumn(),
    'ventas' => $db->query("SELECT SUM(total) FROM pedidos WHERE estado = 'completado'")->fetchColumn()
];

include '../includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Dashboard</h1>
            </div>
            
            <!-- Estadísticas -->
            <div class="row">
                <div class="col-md-3 mb-4">
                    <div class="card text-white bg-primary">
                        <div class="card-body">
                            <h5 class="card-title">Productos</h5>
                            <p class="card-text display-4"><?= $stats['productos'] ?></p>
                            <a href="productos.php" class="text-white">Ver todos</a>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3 mb-4">
                    <div class="card text-white bg-success">
                        <div class="card-body">
                            <h5 class="card-title">Usuarios</h5>
                            <p class="card-text display-4"><?= $stats['usuarios'] ?></p>
                            <a href="usuarios.php" class="text-white">Ver todos</a>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3 mb-4">
                    <div class="card text-white bg-info">
                        <div class="card-body">
                            <h5 class="card-title">Pedidos</h5>
                            <p class="card-text display-4"><?= $stats['pedidos'] ?></p>
                            <a href="pedidos.php" class="text-white">Ver todos</a>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3 mb-4">
                    <div class="card text-white bg-warning">
                        <div class="card-body">
                            <h5 class="card-title">Ventas</h5>
                            <p class="card-text display-4">$<?= number_format($stats['ventas'], 2) ?></p>
                            <a href="pedidos.php" class="text-white">Ver reporte</a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Gráficos o más información -->
        </main>
    </div>
</div>

<?php include '../includes/footer.php'; ?>