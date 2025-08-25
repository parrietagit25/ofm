<?php 
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/db.php'; 
require_once __DIR__ . '/../../controllers/checkoutController.php';
require_once __DIR__ . '/../../controllers/loginController.php';

// Verificar autenticación
$pdo = getConnection();
$loginController = new LoginController($pdo);

if (!$loginController->estaAutenticado()) {
    header('Location: page-login-register.php');
    exit;
}

// Obtener parámetros de la URL
$ordenId = isset($_GET['orden_id']) ? (int)$_GET['orden_id'] : 0;
$numeroOrden = isset($_GET['numero']) ? $_GET['numero'] : '';

if (!$ordenId || !$numeroOrden) {
    header('Location: index.php');
    exit;
}

// Obtener información de la orden
$checkoutController = new CheckoutController($pdo);
$resultadoOrden = $checkoutController->obtenerOrden($ordenId);

if (!$resultadoOrden['success']) {
    header('Location: index.php');
    exit;
}

$orden = $resultadoOrden['orden'];
$usuario = $loginController->obtenerUsuarioActual();

// Verificar que la orden pertenece al usuario actual
if ($orden['usuario_id'] != $usuario['id']) {
    header('Location: index.php');
    exit;
}
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
                <span></span> <a href="checkout.php">Checkout</a>
                <span></span> Confirmación
            </div>
        </div>
    </div>
    
    <section class="mt-50 mb-50">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="order-confirmation">
                        <!-- Header de confirmación -->
                        <div class="text-center mb-50">
                            <div class="success-icon mb-4">
                                <i class="fi-rs-check-circle" style="font-size: 4rem; color: #28a745;"></i>
                            </div>
                            <h1 class="text-success mb-3">¡Orden Confirmada!</h1>
                            <p class="text-muted fs-5">Tu orden ha sido procesada exitosamente</p>
                            <div class="order-number-badge">
                                <strong>Orden #<?= htmlspecialchars($orden['numero_orden']) ?></strong>
                            </div>
                        </div>
                        
                        <!-- Información de la orden -->
                        <div class="row">
                            <div class="col-lg-8">
                                <div class="order-details">
                                    <h3 class="mb-30">Detalles de la Orden</h3>
                                    
                                    <div class="order-info-grid">
                                        <div class="info-item">
                                            <strong>Fecha de orden:</strong>
                                            <span><?= date('d/m/Y H:i', strtotime($orden['fecha_orden'])) ?></span>
                                        </div>
                                        <div class="info-item">
                                            <strong>Estado:</strong>
                                            <span class="badge bg-success"><?= ucfirst(str_replace('_', ' ', $orden['estado'])) ?></span>
                                        </div>
                                        <div class="info-item">
                                            <strong>Método de pago:</strong>
                                            <span><?= ucfirst($orden['metodo_pago']) ?></span>
                                        </div>
                                        <div class="info-item">
                                            <strong>Total:</strong>
                                            <span class="text-brand fs-5">$<?= number_format($orden['total'], 2) ?></span>
                                        </div>
                                    </div>
                                    
                                    <div class="shipping-info mt-4">
                                        <h5>Información de Compra</h5>
                                        <div class="shipping-details">
                                            <p><strong>Tipo de producto:</strong> <span class="text-success">Digital</span></p>
                                            <p><strong>Acceso:</strong> <span class="text-success">Inmediato</span></p>
                                            <?php if (!empty($orden['notas'])): ?>
                                                <p><strong>Notas:</strong> <?= htmlspecialchars($orden['notas']) ?></p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Productos de la orden -->
                                <div class="order-products mt-4">
                                    <h3 class="mb-30">Productos Ordenados</h3>
                                    
                                    <?php foreach ($orden['detalles'] as $detalle): ?>
                                    <div class="order-product-item">
                                        <div class="row align-items-center">
                                            <div class="col-md-2">
                                                <?php if (!empty($detalle['imagen_principal'])): ?>
                                                    <img src="<?= getProductImageUrl($detalle['imagen_principal']) ?>" 
                                                         alt="<?= htmlspecialchars($detalle['nombre_producto']) ?>" 
                                                         class="img-fluid rounded">
                                                <?php else: ?>
                                                    <img src="assets/imgs/shop/default.png" 
                                                         alt="Imagen no disponible" 
                                                         class="img-fluid rounded">
                                                <?php endif; ?>
                                            </div>
                                            <div class="col-md-6">
                                                <h6 class="mb-1"><?= htmlspecialchars($detalle['nombre_producto']) ?></h6>
                                                <small class="text-muted">Cantidad: <?= $detalle['cantidad'] ?></small>
                                            </div>
                                            <div class="col-md-2 text-center">
                                                <span class="text-brand">$<?= number_format($detalle['precio_total'], 2) ?></span>
                                            </div>
                                            <div class="col-md-2 text-end">
                                                <button class="btn btn-sm btn-outline-primary" 
                                                        onclick="mostrarQR('<?= $detalle['codigo_qr'] ?>', '<?= htmlspecialchars($detalle['nombre_producto']) ?>')">
                                                    <i class="fi-rs-qr-code mr-1"></i> Ver QR
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            
                            <div class="col-lg-4">
                                <div class="order-summary-sidebar">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="mb-0">Resumen de la Orden</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="summary-item d-flex justify-content-between mb-2">
                                                <span>Subtotal:</span>
                                                <span>$<?= number_format($orden['subtotal'], 2) ?></span>
                                            </div>
                                            <div class="summary-item d-flex justify-content-between mb-2">
                                                <span>Envío:</span>
                                                <span class="text-success">Gratis</span>
                                            </div>
                                            <div class="summary-item d-flex justify-content-between mb-2">
                                                <span>Impuestos:</span>
                                                <span>$<?= number_format($orden['impuestos'], 2) ?></span>
                                            </div>
                                            <hr>
                                            <div class="summary-item d-flex justify-content-between mb-3">
                                                <strong>Total:</strong>
                                                <strong class="text-brand">$<?= number_format($orden['total'], 2) ?></strong>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="card mt-3">
                                        <div class="card-header">
                                            <h5 class="mb-0">Próximos Pasos</h5>
                                        </div>
                                        <div class="card-body">
                                            <ol class="steps-list">
                                                <li>Recibirás un email de confirmación</li>
                                                <li>Tu orden será procesada en 24-48 horas</li>
                                                <li>Recibirás notificación cuando se envíe</li>
                                                <li>Usa el código QR para verificar la entrega</li>
                                            </ol>
                                        </div>
                                    </div>
                                    
                                    <div class="text-center mt-4">
                                        <a href="index.php" class="btn btn-primary me-2">
                                            <i class="fi-rs-shopping-bag mr-2"></i>Continuar Comprando
                                        </a>
                                        <a href="mis-ordenes.php" class="btn btn-outline-secondary">
                                            <i class="fi-rs-list mr-2"></i>Mis Órdenes
                                        </a>
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

<!-- Modal para mostrar QR -->
<div class="modal fade" id="qrModal" tabindex="-1" aria-labelledby="qrModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="qrModalLabel">Código QR del Producto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <h6 id="qrProductName" class="mb-3"></h6>
                <div id="qrCodeContainer" class="mb-3">
                    <!-- Aquí se generará el QR -->
                </div>
                <div class="qr-info">
                    <p class="text-muted mb-2">Este código QR es único para este producto</p>
                    <p class="text-muted small">Guárdalo para verificar la entrega</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="descargarQR()">
                    <i class="fi-rs-download mr-2"></i>Descargar QR
                </button>
            </div>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>

<style>
.order-confirmation {
    background: #fff;
    border-radius: 15px;
    box-shadow: 0 0 30px rgba(0,0,0,0.1);
    overflow: hidden;
}

.success-icon {
    animation: bounceIn 0.6s ease-out;
}

.order-number-badge {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    color: white;
    padding: 10px 25px;
    border-radius: 25px;
    display: inline-block;
    font-size: 1.1rem;
}

.order-info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.info-item {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    border-left: 4px solid #007bff;
}

.info-item strong {
    display: block;
    color: #495057;
    margin-bottom: 5px;
}

.order-product-item {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 10px;
    margin-bottom: 15px;
    border: 1px solid #e9ecef;
}

.order-product-item:hover {
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}

.order-summary-sidebar .card {
    border: none;
    box-shadow: 0 0 20px rgba(0,0,0,0.1);
    border-radius: 10px;
}

.summary-item {
    font-size: 0.95rem;
}

.steps-list {
    padding-left: 20px;
}

.steps-list li {
    margin-bottom: 10px;
    color: #6c757d;
}

.steps-list li:last-child {
    margin-bottom: 0;
}

@keyframes bounceIn {
    0% {
        transform: scale(0.3);
        opacity: 0;
    }
    50% {
        transform: scale(1.05);
    }
    70% {
        transform: scale(0.9);
    }
    100% {
        transform: scale(1);
        opacity: 1;
    }
}

.qr-code {
    max-width: 200px;
    margin: 0 auto;
}

.qr-info {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    margin-top: 15px;
}
</style>

<script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js"></script>
<script>
let currentQRCode = '';

function mostrarQR(codigoQR, nombreProducto) {
    currentQRCode = codigoQR;
    document.getElementById('qrProductName').textContent = nombreProducto;
    
    // Generar QR
    const qrContainer = document.getElementById('qrCodeContainer');
    qrContainer.innerHTML = '';
    
    QRCode.toCanvas(qrContainer, codigoQR, {
        width: 200,
        margin: 2,
        color: {
            dark: '#000000',
            light: '#FFFFFF'
        }
    }, function (error) {
        if (error) {
            qrContainer.innerHTML = '<p class="text-danger">Error generando QR</p>';
        }
    });
    
    // Mostrar modal
    const modal = new bootstrap.Modal(document.getElementById('qrModal'));
    modal.show();
}

function descargarQR() {
    if (!currentQRCode) return;
    
    const canvas = document.querySelector('#qrCodeContainer canvas');
    if (canvas) {
        const link = document.createElement('a');
        link.download = `QR_${currentQRCode}.png`;
        link.href = canvas.toDataURL();
        link.click();
    }
}

// Inicializar tooltips si Bootstrap está disponible
if (typeof bootstrap !== 'undefined') {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
}
</script>

<?php require_once 'foot.php'; ?>
