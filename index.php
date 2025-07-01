<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';
require_once 'classes/Producto.php';
require_once 'classes/Categoria.php';

// Obtener productos destacados
$productoModel = new Producto();
$destacados = $productoModel->getDestacados(4);

// Obtener categorías para menú
$categoriaModel = new Categoria();
$categorias = $categoriaModel->getAll();

$tituloPagina = "Inicio - Livescomp";
include 'includes/header.php';
?>

<main class="container mt-4">
    <section class="productos-destacados">
        <h2 class="text-center mb-4">Productos Destacados</h2>
        <div class="row">
            <?php foreach ($destacados as $producto): ?>
                <div class="col-md-3 mb-4">
                    <div class="card h-100">
                        <img src="assets/images/products/<?= $producto['imagen'] ?>" 
                             class="card-img-top" alt="<?= htmlspecialchars($producto['nombre']) ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($producto['nombre']) ?></h5>
                            <p class="card-text">$<?= number_format($producto['precio'], 2) ?></p>
                            <a href="producto.php?id=<?= $producto['id'] ?>" class="btn btn-primary">Ver Detalle</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>
