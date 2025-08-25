<?php 
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/db.php'; 
require_once __DIR__ . '/../../controllers/carritoController.php';
require_once __DIR__ . '/../../controllers/loginController.php';
require_once __DIR__ . '/../../controllers/checkoutController.php';

// Verificar autenticación
$pdo = getConnection();
$loginController = new LoginController($pdo);

if (!$loginController->estaAutenticado()) {
    header('Location: page-login-register.php');
    exit;
}

$usuario = $loginController->obtenerUsuarioActual();

// Obtener carrito
$carritoController = new CarritoController($pdo);
$carrito = $carritoController->obtenerCarritoDetallado();
$total = $carritoController->obtenerTotal();
$cantidadProductos = $carritoController->obtenerCantidadProductos();

// Si el carrito está vacío, redirigir
if (empty($carrito)) {
    header('Location: carrito.php');
    exit;
}

// Calcular totales
$subtotal = $total;
$envio = 0.00; // Sin envío para productos digitales
$impuestos = 0.00; // Sin impuestos por ahora
$totalFinal = $subtotal + $envio + $impuestos;
?>

<?php require_once 'head.php'; ?>
<?php require_once 'header.php'; ?>
<?php require_once 'menu.php'; ?>

<main class="main">
    <div class="page-header breadcrumb-wrap">
        <div class="container">
            <div class="breadcrumb">
                <a href="index.php" rel="nofollow">Inicio</a>
                <span></span> <a href="carrito.php">Carrito</a>
                <span></span> Checkout
            </div>
        </div>
    </div>
    
    <section class="mt-50 mb-50">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <div class="checkout-form">
                        <h3 class="mb-30">Información de Compra</h3>
                        
                        <form id="checkoutForm">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="nombre">Nombre completo *</label>
                                        <input type="text" id="nombre" name="nombre" class="form-control" 
                                               value="<?= htmlspecialchars($usuario['nombre'] ?? '') ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email">Email *</label>
                                        <input type="email" id="email" name="email" class="form-control" 
                                               value="<?= htmlspecialchars($usuario['email'] ?? '') ?>" required readonly>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="telefono">Teléfono *</label>
                                        <input type="tel" id="telefono" name="telefono" class="form-control" 
                                               value="<?= htmlspecialchars($usuario['telefono'] ?? '') ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="metodo_pago">Método de pago *</label>
                                        <select id="metodo_pago" name="metodo_pago" class="form-control" required>
                                            <option value="">Selecciona método de pago</option>
                                            <option value="efectivo">Efectivo</option>
                                            <option value="tarjeta">Tarjeta de crédito/débito</option>
                                            <option value="transferencia">Transferencia bancaria</option>
                                            <option value="paypal">PayPal</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="notas">Notas adicionales</label>
                                <textarea id="notas" name="notas" class="form-control" rows="2" 
                                          placeholder="Comentarios sobre tu compra (opcional)"></textarea>
                            </div>
                            
                            <div class="form-group">
                                <div class="form-check">
                                    <input type="checkbox" id="terminos" name="terminos" class="form-check-input" required>
                                    <label class="form-check-label" for="terminos">
                                        Acepto los <a href="#" target="_blank">términos y condiciones</a> y la 
                                        <a href="#" target="_blank">política de privacidad</a> *
                                    </label>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <button type="submit" id="btnProcesar" class="btn btn-primary btn-lg w-100">
                                    <i class="fi-rs-credit-card mr-2"></i>
                                    Procesar Orden
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="order-summary">
                        <h3 class="mb-30">Resumen de la Orden</h3>
                        
                        <div class="order-items">
                            <?php foreach ($carrito as $item): ?>
                            <div class="order-item d-flex align-items-center mb-3">
                                <div class="order-item-img me-3">
                                    <?php if (!empty($item['imagen_principal'])): ?>
                                        <img src="<?= getProductImageUrl($item['imagen_principal']) ?>" 
                                             alt="<?= htmlspecialchars($item['nombre']) ?>" 
                                             style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;">
                                    <?php else: ?>
                                        <img src="assets/imgs/shop/default.png" 
                                             alt="Imagen no disponible" 
                                             style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;">
                                    <?php endif; ?>
                                </div>
                                <div class="order-item-info flex-grow-1">
                                    <h6 class="mb-1"><?= htmlspecialchars($item['nombre']) ?></h6>
                                    <small class="text-muted">Cantidad: <?= $item['cantidad'] ?></small>
                                </div>
                                <div class="order-item-price text-end">
                                    <strong>$<?= number_format($item['precio'] * $item['cantidad'], 2) ?></strong>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <hr>
                        
                        <div class="order-totals">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal:</span>
                                <span>$<?= number_format($subtotal, 2) ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Envío:</span>
                                <span class="text-success">No aplica</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Impuestos:</span>
                                <span>$<?= number_format($impuestos, 2) ?></span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between mb-3">
                                <strong>Total:</strong>
                                <strong class="text-brand fs-5">$<?= number_format($totalFinal, 2) ?></strong>
                            </div>
                        </div>
                        
                        <div class="order-info mt-4">
                            <div class="alert alert-info">
                                <i class="fi-rs-info mr-2"></i>
                                <strong>Información importante:</strong>
                                <ul class="mb-0 mt-2">
                                    <li>Recibirás un código QR único por cada producto</li>
                                    <li>Productos digitales - acceso inmediato</li>
                                    <li>Sin costos de envío</li>
                                </ul>
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
.checkout-form {
    background: #fff;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 0 20px rgba(0,0,0,0.1);
}

.order-summary {
    background: #fff;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 0 20px rgba(0,0,0,0.1);
    position: sticky;
    top: 20px;
}

.order-item {
    padding: 10px 0;
    border-bottom: 1px solid #f0f0f0;
}

.order-item:last-child {
    border-bottom: none;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    font-weight: 600;
    margin-bottom: 8px;
    color: #333;
}

.form-control {
    border: 2px solid #e9ecef;
    border-radius: 8px;
    padding: 12px 15px;
    transition: all 0.3s ease;
}

.form-control:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.25);
}

.btn-primary {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    border: none;
    padding: 15px 30px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,123,255,0.4);
}

.alert-info {
    background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%);
    border: 1px solid #bee5eb;
    color: #0c5460;
}

.order-totals .text-brand {
    color: #007bff;
}
</style>

<script>
document.getElementById('checkoutForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Validar formulario
    if (!validarFormulario()) {
        return;
    }
    
    // Mostrar indicador de carga
    const btnProcesar = document.getElementById('btnProcesar');
    const textoOriginal = btnProcesar.innerHTML;
    btnProcesar.innerHTML = '<i class="fi-rs-spinner mr-2 fa-spin"></i>Procesando...';
    btnProcesar.disabled = true;
    
    // Recopilar datos del formulario
    const formData = new FormData(this);
    const datosCheckout = {
        nombre: formData.get('nombre'),
        email: formData.get('email'),
        telefono: formData.get('telefono'),
        metodo_pago: formData.get('metodo_pago'),
        notas: formData.get('notas'),
        total: <?= $totalFinal ?>,
        subtotal: <?= $subtotal ?>,
        envio: <?= $envio ?>,
        impuestos: <?= $impuestos ?>
    };
    
    // Procesar checkout
    fetch('ajax/procesar-checkout.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(datosCheckout)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Mostrar mensaje de éxito
            mostrarNotificacion(data.message, 'success');
            
            // Redirigir a confirmación de orden
            setTimeout(() => {
                window.location.href = `confirmacion-orden.php?orden_id=${data.orden_id}&numero=${data.numero_orden}`;
            }, 2000);
            
        } else {
            mostrarNotificacion(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        mostrarNotificacion('Error al procesar la orden', 'error');
    })
    .finally(() => {
        // Restaurar botón
        btnProcesar.innerHTML = textoOriginal;
        btnProcesar.disabled = false;
    });
});

function validarFormulario() {
    const campos = ['nombre', 'email', 'telefono', 'metodo_pago'];
    let valido = true;
    
    campos.forEach(campo => {
        const elemento = document.getElementById(campo);
        if (!elemento.value.trim()) {
            elemento.classList.add('is-invalid');
            valido = false;
        } else {
            elemento.classList.remove('is-invalid');
        }
    });
    
    if (!document.getElementById('terminos').checked) {
        mostrarNotificacion('Debes aceptar los términos y condiciones', 'error');
        valido = false;
    }
    
    return valido;
}

function mostrarNotificacion(mensaje, tipo) {
    // Crear notificación
    const notificacion = document.createElement('div');
    notificacion.className = `notificacion notificacion-${tipo}`;
    notificacion.innerHTML = `
        <div class="notificacion-contenido">
            <span class="notificacion-mensaje">${mensaje}</span>
            <button class="notificacion-cerrar" onclick="this.parentElement.parentElement.remove()">&times;</button>
        </div>
    `;
    
    // Agregar estilos
    notificacion.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        padding: 15px 20px;
        border-radius: 5px;
        color: white;
        font-weight: 500;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        animation: slideIn 0.3s ease-out;
    `;
    
    if (tipo === 'success') {
        notificacion.style.backgroundColor = '#28a745';
    } else {
        notificacion.style.backgroundColor = '#dc3545';
    }
    
    // Agregar al DOM
    document.body.appendChild(notificacion);
    
    // Auto-remover después de 5 segundos
    setTimeout(() => {
        if (notificacion.parentElement) {
            notificacion.remove();
        }
    }, 5000);
}

// Agregar estilos CSS para las notificaciones
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    .notificacion-contenido {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    
    .notificacion-cerrar {
        background: none;
        border: none;
        color: white;
        font-size: 20px;
        cursor: pointer;
        margin-left: 15px;
    }
    
    .notificacion-cerrar:hover {
        opacity: 0.8;
    }
    
    .is-invalid {
        border-color: #dc3545 !important;
    }
`;
document.head.appendChild(style);
</script>

<?php require_once 'foot.php'; ?>
