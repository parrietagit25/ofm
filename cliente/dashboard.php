<?php
require_once __DIR__ . '/../controllers/loginController.php';
require_once __DIR__ . '/../controllers/checkoutController.php';
require_once __DIR__ . '/../includes/db.php';

// Verificar que el usuario esté autenticado y sea cliente
$loginController->verificarAcceso('cliente');

// Obtener información del usuario actual
$usuario = $loginController->obtenerUsuarioActual();

// Verificar expiración de sesión
$loginController->verificarExpiracionSesion();

// Procesar logout si se solicita
if (isset($_GET['logout']) && $_GET['logout'] == '1') {
    $loginController->cerrarSesion();
    header('Location: /ofm/public/evara/page-login-register.php');
    exit;
}

// Obtener datos reales del cliente desde la base de datos
$pdo = getConnection();
$checkoutController = new CheckoutController($pdo);

// Obtener estadísticas reales
$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM ordenes WHERE usuario_id = ?");
$stmt->execute([$usuario['id']]);
$totalCompras = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

$stmt = $pdo->prepare("SELECT SUM(total) as total FROM ordenes WHERE usuario_id = ?");
$stmt->execute([$usuario['id']]);
$totalGastado = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0.00;

// Obtener órdenes del usuario
$resultadoOrdenes = $checkoutController->obtenerOrdenesUsuario($usuario['id']);
$ordenes = $resultadoOrdenes['success'] ? $resultadoOrdenes['ordenes'] : [];

// Obtener productos vistos (simulado por ahora)
$productosVistos = count($ordenes) * 3; // Simulación
$ofertasActivas = 5; // Simulación

// Configurar título de la página
$pageTitle = 'Mi Cuenta - Cliente OFM';
?>

<!DOCTYPE html>
<html class="no-js" lang="es">
<head>
    <meta charset="utf-8">
    <title><?= $pageTitle ?></title>
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="description" content="Panel de cliente OFM - Gestiona tu cuenta, pedidos y preferencias">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="/ofm/public/evara/assets/imgs/theme/favicon.svg">
    
    <!-- Template CSS -->
    <link rel="stylesheet" href="/ofm/public/evara/assets/css/main.css?v=3.4">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    
    
         <!-- Custom CSS for Dashboard -->
     <style>
         .qr-code-display {
             min-height: 100px;
             display: flex;
             align-items: center;
             justify-content: center;
         }
         
         .dashboard-menu .nav-link {
             border-radius: 8px;
             margin-bottom: 5px;
             transition: all 0.3s ease;
         }
         
         .dashboard-menu .nav-link:hover {
             background-color: #f8f9fa;
         }
         
         .dashboard-menu .nav-link.active {
             background-color: #007bff;
             color: white;
         }
         
         /* Estilos para la tabla de QR */
         .table-qr-codes {
             table-layout: fixed;
             width: 100%;
         }
         
         .table-qr-codes th,
         .table-qr-codes td {
             vertical-align: middle;
             word-wrap: break-word;
             overflow: hidden;
         }
         
         .table-qr-codes .btn-group {
             flex-wrap: nowrap;
         }
         
         .table-qr-codes .btn-group .btn {
             padding: 0.25rem 0.5rem;
             font-size: 0.75rem;
         }
         
         /* Asegurar que las imágenes QR no se desborden */
         .table-qr-codes img {
             max-width: 100%;
             height: auto;
         }
         
         /* Responsive para pantallas pequeñas */
         @media (max-width: 768px) {
             .table-qr-codes {
                 font-size: 0.875rem;
             }
             
             .table-qr-codes .btn-group {
                 flex-direction: column;
             }
             
             .table-qr-codes .btn-group .btn {
                 margin-bottom: 2px;
             }
         }
     </style>
</head>

<body>
    <!-- Header -->
    <header class="header-area header-style-5">
        <div class="header-top header-top-ptb-1 d-none d-lg-block">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-xl-3 col-lg-4">
                        <div class="header-info">
                            <ul>
                                <li><i class="fi-rs-smartphone"></i> <a href="#">(+01) - 2345 - 6789</a></li>
                                <li><i class="fi-rs-marker"></i><a href="/ofm/public/evara/contacto.php">Nuestra ubicación</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-xl-6 col-lg-4">
                        <div class="text-center">
                            <div id="news-flash" class="d-inline-block">
                                <ul>
                                    <li>¡OFM - Tu marketplace de confianza! <a href="/ofm/public/evara/">Ver productos</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-4">
                        <div class="header-info header-info-right">
                            <ul>
                                <li><i class="fi-rs-user"></i><a href="?logout=1">Cerrar Sesión</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="header-bottom sticky-bar sticky-white-bg">
            <div class="container">
                <div class="header-wrap header-space-between position-relative">
                    <div class="logo logo-width-1">
                        <a href="/ofm/public/evara/"><img src="/ofm/public/evara/assets/imgs/theme/logo.svg" alt="OFM"></a>
                    </div>
                    <div class="main-menu main-menu-grow main-menu-padding-1 main-menu-lh-1 main-menu-mrg-1 hm3-menu-padding d-none d-lg-block hover-boder">
                        <nav>
                            <ul>
                                <li><a href="/ofm/public/evara/">Inicio</a></li>
                                <li><a href="/ofm/public/evara/">Tienda</a></li>
                                <li><a href="/ofm/public/evara/sobre-nosotros.php">Sobre Nosotros</a></li>
                                <li><a href="/ofm/public/evara/contacto.php">Contacto</a></li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="main">
        <div class="page-header breadcrumb-wrap">
            <div class="container">
                <div class="breadcrumb">
                    <a href="/ofm/public/evara/" rel="nofollow">Inicio</a>
                    <span></span> Mi Cuenta
                </div>
            </div>
        </div>
        
        <section class="pt-150 pb-150">
            <div class="container">
                <div class="row">
                    <div class="col-lg-10 m-auto">
                        <div class="row">
                            <!-- Sidebar Menu -->
                            <div class="col-md-4">
                                <div class="dashboard-menu">
                                    <ul class="nav flex-column" role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link active" id="dashboard-tab" data-bs-toggle="tab" href="#dashboard" role="tab">
                                                <i class="fi-rs-settings-sliders mr-10"></i>Dashboard
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="orders-tab" data-bs-toggle="tab" href="#orders" role="tab">
                                                <i class="fi-rs-shopping-bag mr-10"></i>Mis Pedidos
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="qr-codes-tab" data-bs-toggle="tab" href="#qr-codes" role="tab">
                                                <i class="fi-rs-qr-code mr-10"></i>Mis Códigos QR
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="?logout=1">
                                                <i class="fi-rs-sign-out mr-10"></i>Cerrar Sesión
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <!-- Tab Content -->
                            <div class="col-md-8">
                                <div class="tab-content dashboard-content">
                                    <!-- Dashboard Tab -->
                                    <div class="tab-pane fade active show" id="dashboard" role="tabpanel">
                                        <div class="card">
                                            <div class="card-header">
                                                <h5 class="mb-0">¡Hola <?= htmlspecialchars(explode(' ', $usuario['nombre'])[0]) ?>!</h5>
                                            </div>
                                            <div class="card-body">
                                                <p>Bienvenido a tu panel de cuenta OFM</p>
                                                
                                                <!-- Estadísticas del Cliente -->
                                                <div class="row mt-4">
                                                    <div class="col-md-3 text-center">
                                                        <div class="border rounded p-3">
                                                            <i class="fi-rs-shopping-bag text-primary" style="font-size: 2rem;"></i>
                                                            <h6 class="mt-2">Total Compras</h6>
                                                            <h4 class="text-primary"><?= $totalCompras ?></h4>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3 text-center">
                                                        <div class="border rounded p-3">
                                                            <i class="fi-rs-dollar text-success" style="font-size: 2rem;"></i>
                                                            <h6 class="mt-2">Total Gastado</h6>
                                                            <h4 class="text-success">$<?= number_format($totalGastado, 2) ?></h4>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Orders Tab -->
                                    <div class="tab-pane fade" id="orders" role="tabpanel">
                                        <div class="card">
                                            <div class="card-header">
                                                <h5 class="mb-0">Mis Órdenes</h5>
                                            </div>
                                            <div class="card-body">
                                                <?php if (empty($ordenes)): ?>
                                                    <div class="text-center py-4">
                                                        <i class="fi-rs-shopping-bag text-muted" style="font-size: 3rem;"></i>
                                                        <h6 class="mt-3 text-muted">No tienes órdenes aún</h6>
                                                        <p class="text-muted">¡Haz tu primera compra y verás tus órdenes aquí!</p>
                                                        <a href="/ofm/public/evara/" class="btn btn-primary">Ir a la Tienda</a>
                                                    </div>
                                                <?php else: ?>
                                                    <div class="table-responsive">
                                                        <table class="table table-hover">
                                                            <thead>
                                                                <tr>
                                                                    <th>Orden #</th>
                                                                    <th>Fecha</th>
                                                                    <th>Total</th>
                                                                    <th>Estado</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php foreach ($ordenes as $orden): ?>
                                                                <tr>
                                                                    <td><strong><?= htmlspecialchars($orden['numero_orden']) ?></strong></td>
                                                                    <td><?= date('d/m/Y H:i', strtotime($orden['fecha_orden'])) ?></td>
                                                                    <td class="text-success">$<?= number_format($orden['total'], 2) ?></td>
                                                                    <td>
                                                                        <span class="badge bg-<?= $orden['estado'] == 'pendiente' ? 'warning' : 'success' ?>">
                                                                            <?= ucfirst($orden['estado']) ?>
                                                                        </span>
                                                                    </td>
                                                                </tr>
                                                                <?php endforeach; ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>

                                                                         <!-- QR Codes Tab -->
                                     <div class="tab-pane fade" id="qr-codes" role="tabpanel">
                                         <div class="card">
                                             <div class="card-header">
                                                 <h5 class="mb-0">Mis Códigos QR</h5>
                                                 <small class="text-muted">Códigos QR únicos para cada unidad de producto comprado</small>
                                                 <div class="mt-2">
                                                     <button class="btn btn-sm btn-outline-info" onclick="generarTodosQRAltaCalidad()">
                                                         <i class="fas fa-qrcode"></i> Generar Todos en Alta Calidad
                                                     </button>
                                                 </div>
                                             </div>
                                            <div class="card-body">
                                                <?php if (empty($ordenes)): ?>
                                                    <div class="text-center py-4">
                                                        <i class="fi-rs-qr-code text-muted" style="font-size: 3rem;"></i>
                                                        <h6 class="mt-3 text-muted">No tienes códigos QR aún</h6>
                                                        <p class="text-muted">Los códigos QR se generan automáticamente al realizar compras</p>
                                                        <a href="/ofm/public/evara/" class="btn btn-primary">Ir a la Tienda</a>
                                                    </div>
                                                <?php else: ?>
                                                                                                         <!-- Tabla de Códigos QR -->
                                                     <div class="table-responsive" style="overflow-x: auto;">
                                                         <table class="table table-hover table-qr-codes" style="min-width: 900px;">
                                                            <thead class="table-light">
                                                                <tr>
                                                                    <th style="width: 200px; min-width: 200px;">QR</th>
                                                                    <th style="width: 200px; min-width: 200px;">Producto</th>
                                                                    <th style="width: 180px; min-width: 180px;">Orden #</th>
                                                                    <th style="width: 120px; min-width: 120px;">Fecha Compra</th>
                                                                    <th style="width: 100px; min-width: 100px;">Unidad</th>
                                                                    <th style="width: 100px; min-width: 100px;">Estado</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php 
                                                                // Crear array con todos los QR para ordenar por fecha
                                                                $todosLosQR = [];
                                                                foreach ($ordenes as $orden): 
                                                                    $resultadoOrden = $checkoutController->obtenerOrden($orden['id']);
                                                                    if ($resultadoOrden['success']):
                                                                        $ordenCompleta = $resultadoOrden['orden'];
                                                                        foreach ($ordenCompleta['detalles'] as $detalle):
                                                                            for ($i = 1; $i <= $detalle['cantidad']; $i++):
                                                                                $todosLosQR[] = [
                                                                                    'orden' => $orden,
                                                                                    'detalle' => $detalle,
                                                                                    'unidad' => $i,
                                                                                    'fecha' => strtotime($orden['fecha_orden'])
                                                                                ];
                                                                            endfor;
                                                                        endforeach;
                                                                    endif;
                                                                endforeach;
                                                                
                                                                // Ordenar por fecha (más nuevos arriba)
                                                                usort($todosLosQR, function($a, $b) {
                                                                    return $b['fecha'] - $a['fecha'];
                                                                });
                                                                
                                                                foreach ($todosLosQR as $qrData): 
                                                                    $orden = $qrData['orden'];
                                                                    $detalle = $qrData['detalle'];
                                                                    $unidad = $qrData['unidad'];
                                                                ?>
                                                                <tr>
                                                                                                                                         <td>
                                                                         <div class="d-flex align-items-center">
                                                                             <?php 
                                                                             // Generar URL del QR usando la API de QR Server
                                                                             $qr_url = "https://api.qrserver.com/v1/create-qr-code/?size=80x80&data=" . urlencode($detalle['codigo_qr']);
                                                                             ?>
                                                                             <img src="<?= $qr_url ?>" alt="QR Code" style="width: 60px; height: 60px; margin-right: 10px; border: 1px solid #ddd; border-radius: 4px;">
                                                                             <div>
                                                                                 <!--<small class="text-muted d-block"><?= htmlspecialchars($detalle['codigo_qr']) ?></small>-->
                                                                                 <div class="btn-group btn-group-sm mt-1" role="group" style="flex-direction: column;">
                                                                                     <button class="btn btn-outline-primary btn-sm" onclick="verQRCompleto('<?= $detalle['codigo_qr'] ?>', '<?= htmlspecialchars($detalle['nombre_producto']) ?>')" style="margin-bottom: 2px; font-size: 0.7rem; padding: 0.2rem 0.4rem;">
                                                                                         <i class="fas fa-eye"></i> Ver
                                                                                     </button>
                                                                                     <button class="btn btn-outline-success btn-sm" onclick="descargarQR('<?= $detalle['codigo_qr'] ?>', '<?= htmlspecialchars($detalle['nombre_producto']) ?>')" style="font-size: 0.7rem; padding: 0.2rem 0.4rem;">
                                                                                         <i class="fas fa-download"></i> Descargar
                                                                                     </button>
                                                                                 </div>
                                                                             </div>
                                                                         </div>
                                                                     </td>
                                                                    <td style="word-break: break-word;">
                                                                        <div class="d-flex align-items-center">
                                                                            <div>
                                                                                <strong><?= htmlspecialchars($detalle['nombre_producto']) ?></strong>
                                                                                <br>
                                                                                <small class="text-muted">Precio: $<?= number_format($detalle['precio_unitario'], 2) ?></small>
                                                                            </div>
                                                                        </div>
                                                                    </td>
                                                                    <td style="word-break: break-all;"><strong><?= htmlspecialchars($orden['numero_orden']) ?></strong></td>
                                                                    <td style="white-space: nowrap;"><?= date('d/m/Y H:i', strtotime($orden['fecha_orden'])) ?></td>
                                                                    <td><span class="badge bg-info">Unidad <?= $unidad ?></span></td>
                                                                    <td>
                                                                        <span class="badge bg-<?= $orden['estado'] === 'entregada' ? 'success' : 'warning' ?>">
                                                                            <?= ucfirst($orden['estado']) ?>
                                                                        </span>
                                                                    </td>
                                                                </tr>
                                                                <?php endforeach; ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    
                                                    <!-- Resumen de QR -->
                                                    <div class="row mt-4">
                                                        <div class="col-md-4">
                                                            <div class="card bg-light">
                                                                <div class="card-body text-center">
                                                                    <h4 class="text-primary"><?= count($todosLosQR) ?></h4>
                                                                    <p class="mb-0">Total de Códigos QR</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
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

         <!-- Modal para ver QR completo -->
     <div class="modal fade" id="qrModal" tabindex="-1" aria-labelledby="qrModalLabel" aria-hidden="true">
         <div class="modal-dialog modal-dialog-centered">
             <div class="modal-content">
                 <div class="modal-header">
                     <h5 class="modal-title" id="qrModalLabel">Código QR - <span id="qrProductName"></span></h5>
                     <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                 </div>
                 <div class="modal-body text-center">
                     <div id="qrCodeContainer">
                         <!-- El QR se cargará aquí -->
                     </div>
                     <div class="mt-3">
                         <small class="text-muted" id="qrCodeText"></small>
                     </div>
                 </div>
                 <div class="modal-footer">
                     <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                     <button type="button" class="btn btn-primary" onclick="descargarQRDesdeModal()">Descargar QR</button>
                 </div>
             </div>
         </div>
     </div>
     
     <!-- Footer -->
     <footer class="main">
        <div class="container pb-20">
            <div class="row">
                <div class="col-lg-6">
                    <p class="float-md-left font-sm text-muted mb-0">&copy; 2024, <strong class="text-brand">OFM</strong> - Tu Marketplace de Confianza</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Vendor JS-->
    <script src="/ofm/public/evara/assets/js/vendor/jquery-3.6.0.min.js"></script>
    <script src="/ofm/public/evara/assets/js/vendor/bootstrap.bundle.min.js"></script>
    
         <!-- Custom JS for tabs -->
     <script>
         // Variables globales para el modal
         let currentQRCode = '';
         let currentProductName = '';
         
         // Función para ver QR completo
         function verQRCompleto(codigoQR, nombreProducto) {
             currentQRCode = codigoQR;
             currentProductName = nombreProducto;
             
             // Actualizar modal
             document.getElementById('qrProductName').textContent = nombreProducto;
             document.getElementById('qrCodeText').textContent = codigoQR;
             
             // Generar QR en tamaño grande usando la API
             const qrUrl = `https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=${encodeURIComponent(codigoQR)}`;
             document.getElementById('qrCodeContainer').innerHTML = `<img src="${qrUrl}" alt="QR Code" class="img-fluid">`;
             
             // Mostrar modal
             const modal = new bootstrap.Modal(document.getElementById('qrModal'));
             modal.show();
         }
         
         // Función para descargar QR desde la tabla
         function descargarQR(codigoQR, nombreProducto) {
             const qrUrl = `https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=${encodeURIComponent(codigoQR)}`;
             
             // Crear enlace de descarga
             const link = document.createElement('a');
             link.href = qrUrl;
             link.download = `QR_${nombreProducto.replace(/[^a-zA-Z0-9]/g, '_')}_${codigoQR}.png`;
             link.target = '_blank';
             
             // Simular clic
             document.body.appendChild(link);
             link.click();
             document.body.removeChild(link);
         }
         
         // Función para descargar QR desde el modal
         function descargarQRDesdeModal() {
             if (currentQRCode && currentProductName) {
                 descargarQR(currentQRCode, currentProductName);
             }
         }
         
         // Función para generar todos los QR en alta calidad
         function generarTodosQRAltaCalidad() {
             const qrImages = document.querySelectorAll('img[src*="api.qrserver.com"]');
             
             qrImages.forEach((img, index) => {
                 // Cambiar a alta calidad (400x400)
                 const currentSrc = img.src;
                 const newSrc = currentSrc.replace(/size=\d+x\d+/, 'size=400x400');
                 
                 // Crear nueva imagen en alta calidad
                 const highQualityImg = new Image();
                 highQualityImg.onload = function() {
                     img.src = newSrc;
                     img.style.width = '80px';
                     img.style.height = '80px';
                 };
                 highQualityImg.src = newSrc;
                 
                 // Mostrar progreso
                 if (index === 0) {
                     alert('Generando códigos QR en alta calidad...');
                 }
             });
         }
         
         // Inicializar tabs de Bootstrap
         document.addEventListener('DOMContentLoaded', function() {
             // Los QR ya se generan automáticamente con la API, no necesitamos JavaScript adicional
             console.log('Dashboard del cliente cargado correctamente');
         });
     </script>
</body>
</html>
