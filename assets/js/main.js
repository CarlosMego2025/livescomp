// livescomp/assets/js/main.js

/**
 * Funcionalidades principales del sitio web
 */

document.addEventListener('DOMContentLoaded', function() {
    // ========== INICIALIZACIÓN DE COMPONENTES ==========
    
    // 1. Menú móvil
    initMobileMenu();
    
    // 2. Carrusel de productos/testimonios
    initCarousels();
    
    // 3. Funcionalidad del carrito
    initCartFunctionality();
    
    // 4. Validación de formularios
    initFormValidation();
    
    // 5. Zoom en imágenes de productos
    initImageZoom();
    
    // 6. Controles de cantidad
    initQuantityControls();
    
    // 7. Scroll suave
    initSmoothScrolling();
    
    // 8. Botón "volver arriba"
    initBackToTopButton();
    
    // 9. Efectos de animación
    initAnimations();
});

// ========== FUNCIONES DE INICIALIZACIÓN ==========

/**
 * Inicializa el menú móvil
 */
function initMobileMenu() {
    const mobileMenuToggle = document.querySelector('.navbar-toggler');
    if (mobileMenuToggle) {
        mobileMenuToggle.addEventListener('click', function() {
            const target = this.getAttribute('data-bs-target');
            const menu = document.querySelector(target);
            menu.classList.toggle('show');
        });
    }
}

/**
 * Inicializa los carruseles (productos destacados, testimonios)
 */
function initCarousels() {
    // Carrusel principal
    const mainCarousel = document.querySelector('#mainCarousel');
    if (mainCarousel) {
        new bootstrap.Carousel(mainCarousel, {
            interval: 5000,
            pause: 'hover',
            wrap: true
        });
    }
    
    // Carrusel de testimonios
    const testimonialCarousel = document.querySelector('#testimonialCarousel');
    if (testimonialCarousel) {
        new bootstrap.Carousel(testimonialCarousel, {
            interval: 6000,
            pause: 'hover'
        });
    }
}

/**
 * Inicializa la funcionalidad del carrito
 */
function initCartFunctionality() {
    // Botones "Añadir al carrito"
    document.querySelectorAll('.add-to-cart').forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.getAttribute('data-id');
            const quantity = this.closest('.product-actions')?.querySelector('.quantity-input')?.value || 1;
            addToCart(productId, quantity);
        });
    });
    
    // Actualizar contador del carrito al cargar la página
    updateCartCount();
}

/**
 * Inicializa la validación de formularios
 */
function initFormValidation() {
    const forms = document.querySelectorAll('.needs-validation');
    forms.forEach(form => {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });
}

/**
 * Inicializa el zoom en imágenes de productos
 */
function initImageZoom() {
    document.querySelectorAll('.product-img-zoom').forEach(img => {
        img.addEventListener('click', function() {
            const modal = document.createElement('div');
            modal.classList.add('image-modal', 'd-flex', 'align-items-center', 'justify-content-center');
            modal.innerHTML = `
                <div class="modal-content position-relative">
                    <span class="close position-absolute top-0 end-0 m-3 fs-3">&times;</span>
                    <img src="${this.src}" alt="${this.alt}" class="img-fluid">
                </div>
            `;
            document.body.appendChild(modal);
            
            // Cerrar al hacer clic en la X
            modal.querySelector('.close').addEventListener('click', function() {
                modal.remove();
            });
            
            // Cerrar al hacer clic fuera de la imagen
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    modal.remove();
                }
            });
        });
    });
}

/**
 * Inicializa los controles de cantidad (+/-)
 */
function initQuantityControls() {
    document.querySelectorAll('.quantity-control').forEach(control => {
        const minusBtn = control.querySelector('.quantity-minus');
        const plusBtn = control.querySelector('.quantity-plus');
        const input = control.querySelector('.quantity-input');
        
        minusBtn?.addEventListener('click', function() {
            let value = parseInt(input.value) || 1;
            input.value = value > 1 ? value - 1 : 1;
        });
        
        plusBtn?.addEventListener('click', function() {
            let value = parseInt(input.value) || 1;
            input.value = value + 1;
        });
    });
}

/**
 * Inicializa el scroll suave para enlaces internos
 */
function initSmoothScrolling() {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const targetId = this.getAttribute('href');
            if (targetId !== '#') {
                e.preventDefault();
                const targetElement = document.querySelector(targetId);
                if (targetElement) {
                    targetElement.scrollIntoView({
                        behavior: 'smooth'
                    });
                }
            }
        });
    });
}

/**
 * Inicializa el botón "volver arriba"
 */
function initBackToTopButton() {
    const backToTopButton = document.querySelector('.back-to-top');
    if (backToTopButton) {
        window.addEventListener('scroll', function() {
            if (window.pageYOffset > 300) {
                backToTopButton.classList.add('show');
            } else {
                backToTopButton.classList.remove('show');
            }
        });
        
        backToTopButton.addEventListener('click', function(e) {
            e.preventDefault();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }
}

/**
 * Inicializa animaciones (usando Intersection Observer)
 */
function initAnimations() {
    const animatedElements = document.querySelectorAll('.animate-on-scroll');
    
    if (animatedElements.length > 0 && 'IntersectionObserver' in window) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animated');
                    observer.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.1
        });
        
        animatedElements.forEach(element => {
            observer.observe(element);
        });
    } else {
        // Fallback para navegadores que no soportan IntersectionObserver
        animatedElements.forEach(element => {
            element.classList.add('animated');
        });
    }
}

// ========== FUNCIONES DEL CARRITO ==========

/**
 * Añade un producto al carrito
 */
function addToCart(productId, quantity = 1) {
    fetch('api/cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            action: 'add',
            product_id: productId,
            quantity: quantity
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateCartCount(data.cart_count);
            showToast('Producto añadido al carrito', 'success');
        } else {
            showToast(data.message || 'Error al añadir al carrito', 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Error al conectar con el servidor', 'danger');
    });
}

/**
 * Actualiza el contador del carrito
 */
function updateCartCount() {
    fetch('api/cart.php?action=count')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.querySelectorAll('.cart-count').forEach(el => {
                    el.textContent = data.cart_count;
                });
            }
        })
        .catch(error => console.error('Error:', error));
}

// ========== FUNCIONES DE NOTIFICACIÓN ==========

/**
 * Muestra una notificación toast
 */
function showToast(message, type = 'info') {
    const toastContainer = document.getElementById('toastContainer') || createToastContainer();
    const toastId = 'toast-' + Date.now();
    
    const toast = document.createElement('div');
    toast.id = toastId;
    toast.classList.add('toast', 'align-items-center', 'text-white', `bg-${type}`);
    toast.setAttribute('role', 'alert');
    toast.setAttribute('aria-live', 'assertive');
    toast.setAttribute('aria-atomic', 'true');
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                ${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    `;
    
    toastContainer.appendChild(toast);
    
    const bsToast = new bootstrap.Toast(toast, {
        autohide: true,
        delay: 3000
    });
    bsToast.show();
    
    toast.addEventListener('hidden.bs.toast', function() {
        toast.remove();
    });
}

/**
 * Crea el contenedor para las notificaciones toast
 */
function createToastContainer() {
    const container = document.createElement('div');
    container.id = 'toastContainer';
    container.style.position = 'fixed';
    container.style.top = '20px';
    container.style.right = '20px';
    container.style.zIndex = '9999';
    document.body.appendChild(container);
    return container;
}

// ========== FUNCIONES UTILITARIAS ==========

/**
 * Formatea un precio numérico a formato monetario
 */
function formatPrice(price) {
    return new Intl.NumberFormat('es-MX', {
        style: 'currency',
        currency: 'MXN'
    }).format(price);
}

/**
 * Maneja errores de fetch
 */
function handleFetchError(error) {
    console.error('Error:', error);
    showToast('Ocurrió un error al procesar la solicitud', 'danger');
}
