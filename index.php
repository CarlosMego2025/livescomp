<?php
require_once 'includes/config.php';
require_once 'classes/Producto.php';
require_once 'classes/Categoria.php';

// Instanciar modelos
$productoModel = new Producto();
$categoriaModel = new Categoria();

// Obtener productos destacados
$destacados = $productoModel->getDestacados(6);

// Obtener categorías para el menú
$categorias = $categoriaModel->getAll();

// Configurar título de la página
$tituloPagina = "Inicio - Livescomp";

// Incluir header
include 'includes/header_public.php';
?>

<main class="container mt-5">
    <!-- Banner principal -->
    <section class="banner mb-5">
        <div class="row">
            <div class="col-md-12">
                <div class="card bg-dark text-white">
                    <img src="assets/images/banner.jpg" class="card-img" alt="Banner principal">
                    <div class="card-img-overlay d-flex align-items-center justify-content-center">
                        <div class="text-center">
                            <h1 class="card-title display-4 fw-bold">Bienvenido a Livescomp</h1>
                            <p class="card-text lead">Los mejores productos al mejor precio</p>
                            <a href="productos.php" class="btn btn-primary btn-lg">Ver productos</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Productos destacados -->
    <section class="destacados mb-5">
        <h2 class="text-center mb-4">Productos Destacados</h2>
        <div class="row">
            <?php if (empty($destacados)): ?>
                <div class="col-12">
                    <div class="alert alert-info">No hay productos destacados</div>
                </div>
            <?php else: ?>
                <?php foreach ($destacados as $producto): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <img src="assets/images/products/<?= $producto['imagen'] ?>" 
                                 class="card-img-top" alt="<?= htmlspecialchars($producto['nombre']) ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($producto['nombre']) ?></h5>
                                <p class="card-text"><?= htmlspecialchars(substr($producto['descripcion'], 0, 100)) ?>...</p>
                                <p class="h4 text-primary">$<?= number_format($producto['precio'], 2) ?></p>
                                <?php if ($producto['categoria_nombre']): ?>
                                    <span class="badge bg-secondary"><?= htmlspecialchars($producto['categoria_nombre']) ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="card-footer bg-white">
                                <a href="producto_detalle.php?id=<?= $producto['id'] ?>" class="btn btn-primary">Ver detalle</a>
                                <button class="btn btn-outline-success add-to-cart" data-id="<?= $producto['id'] ?>">
                                    <i class="fas fa-shopping-cart"></i> Añadir
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <div class="text-center mt-3">
            <a href="productos.php" class="btn btn-outline-primary">Ver todos los productos</a>
        </div>
    </section>

    <!-- Categorías -->
    <section class="categorias mb-5">
        <h2 class="text-center mb-4">Explora nuestras categorías</h2>
        <div class="row">
            <?php foreach ($categorias as $categoria): ?>
                <div class="col-md-3 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-folder fa-3x mb-3 text-primary"></i>
                            <h5 class="card-title"><?= htmlspecialchars($categoria['nombre']) ?></h5>
                            <p class="card-text"><?= htmlspecialchars(substr($categoria['descripcion'], 0, 100)) ?>...</p>
                            <a href="productos.php?categoria_id=<?= $categoria['id'] ?>" class="btn btn-sm btn-outline-primary">
                                Ver productos
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Testimonios -->
    <section class="testimonios mb-5">
        <h2 class="text-center mb-4">Lo que dicen nuestros clientes</h2>
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex mb-3">
                            <img src="assets/images/avatar1.jpg" class="rounded-circle me-3" width="50" alt="Cliente 1">
                            <div>
                                <h5 class="mb-0">María González</h5>
                                <div class="text-warning">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                </div>
                            </div>
                        </div>
                        <p class="card-text">"Excelente servicio y productos de alta calidad. Siempre compro aquí y nunca me decepcionan."</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex mb-3">
                            <img src="assets/images/avatar2.jpg" class="rounded-circle me-3" width="50" alt="Cliente 2">
                            <div>
                                <h5 class="mb-0">Juan Pérez</h5>
                                <div class="text-warning">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star-half-alt"></i>
                                </div>
                            </div>
                        </div>
                        <p class="card-text">"Entrega rápida y buen soporte al cliente. Recomiendo Livescomp a todos mis amigos."</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex mb-3">
                            <img src="assets/images/avatar3.jpg" class="rounded-circle me-3" width="50" alt="Cliente 3">
                            <div>
                                <h5 class="mb-0">Carlos Rodríguez</h5>
                                <div class="text-warning">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="far fa-star"></i>
                                </div>
                            </div>
                        </div>
                        <p class="card-text">"Buena relación calidad-precio. Volveré a comprar sin duda."</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php 
// Incluir footer
include 'includes/footer.php'; 
?>

<!-- Script para el carrito -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Manejar clic en botones "Añadir al carrito"
    document.querySelectorAll('.add-to-cart').forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.getAttribute('data-id');
            
            fetch('api/carrito.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'agregar',
                    producto_id: productId,
                    cantidad: 1
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Actualizar contador del carrito
                    const cartCount = document.getElementById('cart-count');
                    if (cartCount) {
                        cartCount.textContent = data.cart_count;
                    }
                    
                    // Mostrar notificación
                    alert('Producto añadido al carrito');
                } else {
                    alert(data.message || 'Error al añadir al carrito');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al conectar con el servidor');
            });
        });
    });
});
</script>
