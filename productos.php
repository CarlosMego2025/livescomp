<?php
require_once 'includes/config.php';
require_once 'includes/Auth.php';
require_once 'classes/Producto.php';
 
require_once 'classes/Categoria.php';

// Configurar título de página
$tituloPagina = "Nombre de la página";

// Obtener datos necesarios (ej. categorías para el menú)
$categoriaModel = new Categoria();
$categorias = $categoriaModel->getAll();

// Incluir header
include 'includes/header.php';

$productoModel = new Producto();
$categoriaActual = $_GET['categoria'] ?? null;
$paginaActual = $_GET['pagina'] ?? 1;
$porPagina = 12;

if ($categoriaActual) {
    $productos = $productoModel->getByCategory($categoriaActual);
    $totalProductos = count($productos);
} else {
    $totalProductos = $productoModel->getTotal();
    $offset = ($paginaActual - 1) * $porPagina;
    $productos = $productoModel->getAll($porPagina, $offset);
}

$totalPaginas = ceil($totalProductos / $porPagina);

include 'includes/header.php';
?>

<div class="container mt-4">
    <h1 class="mb-4">Nuestros Productos</h1>
    
    <div class="row">
        <!-- Sidebar con categorías -->
        <div class="col-md-3">
            <div class="card mb-4">
                <div class="card-header">Categorías</div>
                <div class="list-group list-group-flush">
                    <a href="productos.php" class="list-group-item list-group-item-action <?= !$categoriaActual ? 'active' : '' ?>">
                        Todas las categorías
                    </a>
                    <?php foreach ($categorias as $categoria): ?>
                        <a href="productos.php?categoria=<?= $categoria['id'] ?>" 
                           class="list-group-item list-group-item-action <?= $categoriaActual == $categoria['id'] ? 'active' : '' ?>">
                            <?= htmlspecialchars($categoria['nombre']) ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        
        <!-- Lista de productos -->
        <div class="col-md-9">
            <div class="row">
                <?php foreach ($productos as $prod): ?>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="card h-100">
                            <img src="assets/images/products/<?= htmlspecialchars($prod['imagen']) ?>" 
                                 class="card-img-top" alt="<?= htmlspecialchars($prod['nombre']) ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($prod['nombre']) ?></h5>
                                <h6>$<?= number_format($prod['precio'], 2) ?></h6>
                                <p class="card-text"><?= htmlspecialchars(substr($prod['descripcion'], 0, 100)) ?>...</p>
                            </div>
                            <div class="card-footer bg-transparent">
                                <a href="producto.php?id=<?= $prod['id'] ?>" class="btn btn-primary btn-sm">Ver Detalles</a>
                                <button class="btn btn-success btn-sm add-to-cart" data-id="<?= $prod['id'] ?>">
                                    Añadir al carrito
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Paginación -->
            <?php if (!$categoriaActual && $totalPaginas > 1): ?>
                <nav aria-label="Page navigation">
                    <ul class="pagination justify-content-center">
                        <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                            <li class="page-item <?= $i == $paginaActual ? 'active' : '' ?>">
                                <a class="page-link" href="productos.php?pagina=<?= $i ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
