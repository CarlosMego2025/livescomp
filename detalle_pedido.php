<?php
require_once 'includes/auth.php';
require_once 'includes/funciones.php';
require_once 'includes/database.php';

$db = new Database();
$conn = $db->getConnection();

// Obtener ID del pedido
$pedido_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Obtener información del pedido
$stmt = $conn->prepare("
    SELECT p.*, u.nombre AS cliente_nombre, u.direccion, u.telefono 
    FROM pedidos p
    JOIN usuarios u ON p.usuario_id = u.id
    WHERE p.id = ? AND p.usuario_id = ?
");
$stmt->execute([$pedido_id, $_SESSION['usuario_id']]);
$pedido = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$pedido) {
    $_SESSION['flash_error'] = 'Pedido no encontrado';
    redirect('pedidos.php');
}

// Obtener detalles del pedido
$stmt = $conn->prepare("
    SELECT dp.*, pr.nombre AS producto_nombre 
    FROM detalle_pedido dp
    JOIN productos pr ON dp.producto_id = pr.id
    WHERE dp.pedido_id = ?
");
$stmt->execute([$pedido_id]);
$detalles = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Si se solicita descargar PDF
if (isset($_GET['pdf'])) {
    require_once 'tcpdf/tcpdf.php';
    
    // Crear nuevo documento PDF
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    
    // Configurar documento
    $pdf->SetCreator('LivesComp');
    $pdf->SetAuthor('LivesComp');
    $pdf->SetTitle('Pedido #' . $pedido_id);
    $pdf->SetSubject('Detalle de pedido');
    
    // Agregar una página
    $pdf->AddPage();
    
    // Contenido HTML para el PDF
    $html = '
    <h1 style="text-align:center;">LivesComp - Pedido #' . $pedido_id . '</h1>
    <table border="0" cellpadding="5">
        <tr>
            <td><strong>Fecha:</strong> ' . date('d/m/Y H:i', strtotime($pedido['fecha'])) . '</td>
        </tr>
        <tr>
            <td><strong>Cliente:</strong> ' . $pedido['cliente_nombre'] . '</td>
        </tr>
        <tr>
            <td><strong>Dirección:</strong> ' . $pedido['direccion'] . '</td>
        </tr>
        <tr>
            <td><strong>Teléfono:</strong> ' . $pedido['telefono'] . '</td>
        </tr>
    </table>
    
    <h3>Detalle del Pedido</h3>
    <table border="1" cellpadding="5">
        <thead>
            <tr>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Precio Unitario</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>';
    
    $total = 0;
    foreach ($detalles as $detalle) {
        $subtotal = $detalle['cantidad'] * $detalle['precio_unitario'];
        $total += $subtotal;
        $html .= '
            <tr>
                <td>' . $detalle['producto_nombre'] . '</td>
                <td>' . $detalle['cantidad'] . '</td>
                <td>' . formatPrice($detalle['precio_unitario']) . '</td>
                <td>' . formatPrice($subtotal) . '</td>
            </tr>';
    }
    
    $html .= '
            <tr>
                <td colspan="3" align="right"><strong>Total:</strong></td>
                <td><strong>' . formatPrice($total) . '</strong></td>
            </tr>
        </tbody>
    </table>';
    
    // Escribir el contenido HTML
    $pdf->writeHTML($html, true, false, true, false, '');
    
    // Salida del PDF (descarga)
    $pdf->Output('pedido_' . $pedido_id . '.pdf', 'D');
    exit;
}

include 'includes/header.php';
?>

<div class="container mt-4">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4>Detalle del Pedido #<?= $pedido_id ?></h4>
            <a href="detalle_pedido.php?id=<?= $pedido_id ?>&pdf=1" class="btn btn-primary">
                <i class="fas fa-download"></i> Descargar PDF
            </a>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6">
                    <h5>Información del Pedido</h5>
                    <p><strong>Fecha:</strong> <?= date('d/m/Y H:i', strtotime($pedido['fecha'])) ?></p>
                    <p><strong>Estado:</strong> 
                        <span class="badge badge-<?= 
                            $pedido['estado'] === 'completado' ? 'success' : 
                            ($pedido['estado'] === 'pendiente' ? 'warning' : 
                            ($pedido['estado'] === 'aprobado' ? 'info' : 
                            ($pedido['estado'] === 'enviado' ? 'primary' : 'danger')))
                        ?>">
                            <?= ucfirst($pedido['estado']) ?>
                        </span>
                    </p>
                    <p><strong>Total:</strong> <?= formatPrice($pedido['total']) ?></p>
                </div>
                <div class="col-md-6">
                    <h5>Información del Cliente</h5>
                    <p><strong>Nombre:</strong> <?= $pedido['cliente_nombre'] ?></p>
                    <p><strong>Dirección:</strong> <?= $pedido['direccion'] ?></p>
                    <p><strong>Teléfono:</strong> <?= $pedido['telefono'] ?></p>
                </div>
            </div>
            
            <h5>Productos</h5>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Precio Unitario</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $total = 0;
                        foreach ($detalles as $detalle): 
                            $subtotal = $detalle['cantidad'] * $detalle['precio_unitario'];
                            $total += $subtotal;
                        ?>
                            <tr>
                                <td><?= $detalle['producto_nombre'] ?></td>
                                <td><?= $detalle['cantidad'] ?></td>
                                <td><?= formatPrice($detalle['precio_unitario']) ?></td>
                                <td><?= formatPrice($subtotal) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <tr>
                            <td colspan="3" class="text-right"><strong>Total</strong></td>
                            <td><strong><?= formatPrice($total) ?></strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>