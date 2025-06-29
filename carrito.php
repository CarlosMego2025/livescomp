<?php
require_once 'includes/config.php';
require_once 'includes/Auth.php';
require_once 'classes/Carrito.php';

$carrito = new Carrito();

// Procesar acciones del carrito
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $productoId = $_POST['producto_id'] ?? 0;
    
    switch ($action) {
        case 'actualizar':
            $cantidad = $_POST['cantidad'] ?? 1;
            $carrito->actualizarCantidad($productoId, $cantidad);
            break;
        case 'eliminar':
            $carrito->eliminarProducto($productoId);
            break;
        case 'vaciar':
            $carrito->vaciar();
            break;
    }
    
    redirect('carrito.php');
}

$items = $carrito->getContenido();
$total = $carrito->getTotalPrecio();

include 'includes/header.php';
?>

<div class="container mt-4">
    <h1 class="mb-4">Tu Carrito de Compras</h1>
    
    <?php if (empty($items)): ?>
        <div class="alert alert-info">
            Tu carrito está vacío. <a href="productos.php">Ver productos</a>
        </div>
    <?php else: ?>
        <form method="post" action="carrito.php">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Precio</th>
                            <th>Cantidad</th>
                            <th>Subtotal</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item): ?>
                            <tr>
                                <td>
                                    <img src="assets/images/products/<?= htmlspecialchars($item['imagen']) ?>" 
                                         width="50" class="me-3" alt="<?= htmlspecialchars($item['nombre']) ?>">
                                    <?= htmlspecialchars($item['nombre']) ?>
                                </td>
                                <td>$<?= number_format($item['precio'], 2) ?></td>
                                <td>
                                    <input type="number" name="cantidad[<?= $item['id'] ?>]" 
                                           value="<?= $item['cantidad'] ?>" min="1" class="form-control" style="width: 70px;">
                                </td>
                                <td>$<?= number_format($item['precio'] * $item['cantidad'], 2) ?></td>
                                <td>
                                    <button type="submit" name="action" value="eliminar" 
                                            class="btn btn-danger btn-sm" formnovalidate>
                                        Eliminar
                                    </button>
                                    <input type="hidden" name="producto_id" value="<?= $item['id'] ?>">
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-end"><strong>Total:</strong></td>
                            <td colspan="2">$<?= number_format($total, 2) ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            
            <div class="d-flex justify-content-between">
                <button type="submit" name="action" value="vaciar" class="btn btn-outline-danger" formnovalidate>
                    Vaciar Carrito
                </button>
                <div>
                    <button type="submit" name="action" value="actualizar" class="btn btn-secondary me-2">
                        Actualizar Carrito
                    </button>
                    <a href="checkout.php" class="btn btn-primary">Proceder al Pago</a>
                </div>
            </div>
        </form>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>