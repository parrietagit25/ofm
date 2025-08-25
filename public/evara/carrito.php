<?php 
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/db.php'; 
require_once __DIR__ . '/../../controllers/carritoController.php';
require_once __DIR__ . '/../../controllers/loginController.php';

// Configurar entorno
define('ENVIRONMENT', 'development');

// Verificar autenticación
$pdo = getConnection();
$loginController = new LoginController($pdo);
$usuarioAutenticado = $loginController->estaAutenticado();

if (!$usuarioAutenticado) {
    header('Location: page-login-register.php');
    exit;
}

$usuario = $loginController->obtenerUsuarioActual();

// Obtener carrito
$carritoController = new CarritoController($pdo);
$carrito = $carritoController->obtenerCarritoDetallado();
$total = $carritoController->obtenerTotal();
$cantidadProductos = $carritoController->obtenerCantidadProductos();
?>

<?php require_once 'head.php'; ?>
<?php require_once 'header.php'; ?>
<?php require_once 'menu.php'; ?>

<main class="main">
    <div class="page-header breadcrumb-wrap">
        <div class="container">
            <div class="breadcrumb">
                <a href="index.php" rel="nofollow">Inicio</a>
                <span></span> Carrito de Compras
            </div>
        </div>
    </div>
    
    <section class="mt-50 mb-50">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="cart-main">
                        <div class="row">
                            <div class="col-lg-8">
                                <div class="table-responsive">
                                    <?php if (empty($carrito)): ?>
                                        <!-- Carrito vacío -->
                                        <div class="text-center py-5">
                                            <i class="fi-rs-shopping-cart" style="font-size: 4rem; color: #ccc;"></i>
                                            <h3 class="mt-3">Tu carrito está vacío</h3>
                                            <p class="text-muted mb-4">No tienes productos en tu carrito de compras</p>
                                            <a href="index.php" class="btn btn-primary">Continuar comprando</a>
                                        </div>
                                    <?php else: ?>
                                        <!-- Tabla del carrito -->
                                        <table class="table table-borderless table-hover">
                                            <thead>
                                                <tr>
                                                    <th scope="col">Producto</th>
                                                    <th scope="col">Precio</th>
                                                    <th scope="col">Cantidad</th>
                                                    <th scope="col">Total</th>
                                                    <th scope="col">Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($carrito as $item): ?>
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="cart-img me-3">
                                                                <?php if (!empty($item['imagen_principal'])): ?>
                                                                    <img src="<?= getProductImageUrl($item['imagen_principal']) ?>" 
                                                                         alt="<?= htmlspecialchars($item['nombre']) ?>" 
                                                                         style="width: 60px; height: 60px; object-fit: cover;">
                                                                <?php else: ?>
                                                                    <img src="assets/imgs/shop/default.png" 
                                                                         alt="Imagen no disponible" 
                                                                         style="width: 60px; height: 60px; object-fit: cover;">
                                                                <?php endif; ?>
                                                            </div>
                                                            <div>
                                                                <h6 class="mb-0"><?= htmlspecialchars($item['nombre']) ?></h6>
                                                                <small class="text-muted">SKU: <?= htmlspecialchars($item['producto_id']) ?></small>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="text-brand">$<?= number_format($item['precio'], 2) ?></span>
                                                    </td>
                                                    <td>
                                                        <div class="quantity d-flex align-items-center">
                                                            <button class="qty-btn qty-down" onclick="cambiarCantidad(<?= $item['producto_id'] ?>, -1)">-</button>
                                                            <span class="qty-val mx-2"><?= $item['cantidad'] ?></span>
                                                            <button class="qty-btn qty-up" onclick="cambiarCantidad(<?= $item['producto_id'] ?>, 1)">+</button>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="text-brand fw-bold">$<?= number_format($item['precio'] * $item['cantidad'], 2) ?></span>
                                                    </td>
                                                    <td>
                                                        <button class="btn btn-sm btn-outline-danger" onclick="eliminarProducto(<?= $item['producto_id'] ?>)">
                                                            <i class="fi-rs-trash"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="col-lg-4">
                                <div class="cart-summary">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="mb-0">Resumen del Carrito</h5>
                                        </div>
                                        <div class="card-body">
                                            <?php if (!empty($carrito)): ?>
                                                <div class="d-flex justify-content-between mb-2">
                                                    <span>Productos (<?= $cantidadProductos ?>)</span>
                                                    <span>$<?= number_format($total, 2) ?></span>
                                                </div>
                                                <div class="d-flex justify-content-between mb-2">
                                                    <span>Envío</span>
                                                    <span class="text-success">Gratis</span>
                                                </div>
                                                <hr>
                                                <div class="d-flex justify-content-between mb-3">
                                                    <strong>Total</strong>
                                                    <strong class="text-brand">$<?= number_format($total, 2) ?></strong>
                                                </div>
                                                
                                                <a href="checkout.php" class="btn btn-primary w-100 mb-3">
                                                    Proceder al Checkout
                                                </a>
                                                
                                                <button class="btn btn-outline-secondary w-100" onclick="limpiarCarrito()">
                                                    Limpiar Carrito
                                                </button>
                                                
                                                <div class="text-center mt-3">
                                                    <a href="index.php" class="text-muted">
                                                        <i class="fi-rs-arrow-left mr-2"></i> Continuar comprando
                                                    </a>
                                                </div>
                                            <?php else: ?>
                                                <p class="text-muted text-center">No hay productos en el carrito</p>
                                                <a href="index.php" class="btn btn-primary w-100">Ir a la tienda</a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php require_once 'footer.php'; ?>

<style>
.cart-main {
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 0 20px rgba(0,0,0,0.1);
    padding: 30px;
}

.cart-img img {
    border-radius: 8px;
}

.qty-btn {
    width: 30px;
    height: 30px;
    border: 1px solid #ddd;
    background: #fff;
    border-radius: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
}

.qty-btn:hover {
    background: #f8f9fa;
    border-color: #007bff;
}

.qty-val {
    min-width: 30px;
    text-align: center;
    font-weight: 500;
}

.cart-summary .card {
    border: none;
    box-shadow: 0 0 20px rgba(0,0,0,0.1);
    border-radius: 10px;
}

.cart-summary .card-header {
    background: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
    border-radius: 10px 10px 0 0 !important;
}

.table th {
    border-top: none;
    font-weight: 600;
    color: #495057;
}

.table td {
    vertical-align: middle;
    border-top: 1px solid #dee2e6;
}

.btn-outline-danger:hover {
    background-color: #dc3545;
    border-color: #dc3545;
    color: white;
}
</style>

<script>
function cambiarCantidad(productoId, cambio) {
    const fila = event.target.closest('tr');
    const qtyVal = fila.querySelector('.qty-val');
    let cantidadActual = parseInt(qtyVal.textContent);
    let nuevaCantidad = cantidadActual + cambio;
    
    if (nuevaCantidad < 1) {
        nuevaCantidad = 1;
    }
    
    // Actualizar cantidad en el carrito
    fetch('ajax/actualizar-cantidad-carrito.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            producto_id: productoId,
            cantidad: nuevaCantidad
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            qtyVal.textContent = nuevaCantidad;
            // Recargar página para actualizar totales
            setTimeout(() => {
                location.reload();
            }, 500);
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al actualizar cantidad');
    });
}

function eliminarProducto(productoId) {
    if (confirm('¿Estás seguro de que quieres eliminar este producto del carrito?')) {
        fetch('ajax/eliminar-del-carrito.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                producto_id: productoId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al eliminar producto');
        });
    }
}

function limpiarCarrito() {
    if (confirm('¿Estás seguro de que quieres limpiar todo el carrito?')) {
        fetch('ajax/limpiar-carrito.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al limpiar carrito');
        });
    }
}


</script>

<?php require_once 'foot.php'; ?>
