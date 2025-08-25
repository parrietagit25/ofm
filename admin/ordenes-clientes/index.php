<?php
/**
 * Página de gestión de órdenes de clientes - Panel Administrativo OFM
 * Muestra todas las órdenes con sus productos y códigos QR
 */

require_once __DIR__ . '/../../includes/session_helper.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../controllers/loginController_simple.php';

// Verificar acceso de administrador
$pdo = getConnection();
$loginController = new LoginControllerSimple($pdo);
$loginController->verificarAcceso('admin');

// Obtener parámetros de filtrado y paginación
$pagina = $_GET['pagina'] ?? 1;
$filtros = [
    'estado' => $_GET['estado'] ?? '',
    'fecha_desde' => $_GET['fecha_desde'] ?? '',
    'fecha_hasta' => $_GET['fecha_hasta'] ?? '',
    'cliente' => $_GET['cliente'] ?? ''
];

// Obtener órdenes con detalles
$ordenes = [];
$total = 0;
$paginas = 1;

try {
    // Construir consulta con filtros
    $where = [];
    $params = [];
    
    if (!empty($filtros['estado'])) {
        $where[] = "o.estado = ?";
        $params[] = $filtros['estado'];
    }
    
    if (!empty($filtros['fecha_desde'])) {
        $where[] = "DATE(o.fecha_orden) >= ?";
        $params[] = $filtros['fecha_desde'];
    }
    
    if (!empty($filtros['fecha_hasta'])) {
        $where[] = "DATE(o.fecha_orden) <= ?";
        $params[] = $filtros['fecha_hasta'];
    }
    
    if (!empty($filtros['cliente'])) {
        $where[] = "u.nombre LIKE ?";
        $params[] = '%' . $filtros['cliente'] . '%';
    }
    
    $whereClause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";
    
    // Contar total de órdenes
    $sqlCount = "SELECT COUNT(DISTINCT o.id) as total 
                  FROM ordenes o 
                  JOIN usuarios u ON o.usuario_id = u.id 
                  $whereClause";
    
    $stmtCount = $pdo->prepare($sqlCount);
    $stmtCount->execute($params);
    $total = $stmtCount->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Calcular paginación
    $porPagina = 20;
    $paginas = ceil($total / $porPagina);
    $offset = ($pagina - 1) * $porPagina;
    
    // Obtener órdenes con detalles
    $sql = "SELECT o.*, u.nombre as nombre_cliente, u.email as email_cliente,
                   COUNT(od.id) as total_productos,
                   SUM(od.cantidad) as total_unidades
            FROM ordenes o 
            JOIN usuarios u ON o.usuario_id = u.id 
            LEFT JOIN orden_detalles od ON o.id = od.orden_id 
            $whereClause
            GROUP BY o.id 
            ORDER BY o.fecha_orden DESC 
            LIMIT $porPagina OFFSET $offset";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $ordenes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Obtener detalles de cada orden
    foreach ($ordenes as &$orden) {
        $sqlDetalles = "SELECT od.*, p.nombre as nombre_producto, p.imagen,
                               c.nombre_comercio, u.nombre as nombre_socio
                        FROM orden_detalles od 
                        JOIN productos p ON od.producto_id = p.id 
                        LEFT JOIN comercios c ON od.comercio_id = c.id 
                        LEFT JOIN usuarios u ON od.socio_id = u.id 
                        WHERE od.orden_id = ? 
                        ORDER BY od.id";
        
        $stmtDetalles = $pdo->prepare($sqlDetalles);
        $stmtDetalles->execute([$orden['id']]);
        $orden['detalles'] = $stmtDetalles->fetchAll(PDO::FETCH_ASSOC);
    }
    
} catch (Exception $e) {
    $error = 'Error obteniendo órdenes: ' . $e->getMessage();
}

// Obtener estados disponibles para filtros
$estados = ['pendiente', 'confirmada', 'en_proceso', 'enviada', 'entregada', 'cancelada'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Órdenes de Clientes - Panel Administrativo OFM</title>
    
    <!-- AdminLTE CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/css/adminlte.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        .order-card {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .order-header {
            background: #f8f9fa;
            padding: 15px;
            border-bottom: 1px solid #dee2e6;
            border-radius: 8px 8px 0 0;
        }
        
        .order-details {
            padding: 15px;
        }
        
        .product-item {
            border: 1px solid #e9ecef;
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 10px;
            background: #fff;
        }
        
        .qr-code {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 10px;
            text-align: center;
            margin: 5px;
        }
        
        .qr-code img {
            max-width: 100px;
            height: auto;
        }
        
        .status-badge {
            font-size: 0.75rem;
        }
        
        .filters-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .order-summary {
            background: #e3f2fd;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
        }
    </style>
</head>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <!-- Navbar -->
        <?php include '../../admin/includes/navbar.php'; ?>
        
        <!-- Sidebar -->
        <?php include '../../admin/includes/sidebar.php'; ?>
        
        <!-- Content Wrapper -->
        <div class="content-wrapper">
            <!-- Content Header -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Órdenes de Clientes</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="../index.php">Inicio</a></li>
                                <li class="breadcrumb-item active">Órdenes de Clientes</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main content -->
            <div class="content">
                <div class="container-fluid">
                    <!-- Filtros -->
                    <div class="filters-section">
                        <h5><i class="fas fa-filter"></i> Filtros</h5>
                        <form method="GET" class="row g-3">
                            <div class="col-md-2">
                                <label class="form-label">Estado</label>
                                <select name="estado" class="form-select">
                                    <option value="">Todos</option>
                                    <?php foreach ($estados as $estado): ?>
                                        <option value="<?= $estado ?>" <?= $filtros['estado'] === $estado ? 'selected' : '' ?>>
                                            <?= ucfirst(str_replace('_', ' ', $estado)) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Fecha Desde</label>
                                <input type="date" name="fecha_desde" class="form-control" value="<?= $filtros['fecha_desde'] ?>">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Fecha Hasta</label>
                                <input type="date" name="fecha_hasta" class="form-control" value="<?= $filtros['fecha_hasta'] ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Cliente</label>
                                <input type="text" name="cliente" class="form-control" placeholder="Nombre del cliente" value="<?= htmlspecialchars($filtros['cliente']) ?>">
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="fas fa-search"></i> Filtrar
                                </button>
                                <a href="index.php" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Limpiar
                                </a>
                            </div>
                        </form>
                    </div>

                    <!-- Estadísticas -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-info"><i class="fas fa-shopping-cart"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Órdenes</span>
                                    <span class="info-box-number"><?= $total ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-success"><i class="fas fa-check-circle"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Entregadas</span>
                                    <span class="info-box-number">
                                        <?= array_reduce($ordenes, function($carry, $orden) {
                                            return $carry + ($orden['estado'] === 'entregada' ? 1 : 0);
                                        }, 0) ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-warning"><i class="fas fa-clock"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Pendientes</span>
                                    <span class="info-box-number">
                                        <?= array_reduce($ordenes, function($carry, $orden) {
                                            return $carry + ($orden['estado'] === 'pendiente' ? 1 : 0);
                                        }, 0) ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-primary"><i class="fas fa-qrcode"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total QR</span>
                                    <span class="info-box-number">
                                        <?= array_reduce($ordenes, function($carry, $orden) {
                                            return $carry + $orden['total_unidades'];
                                        }, 0) ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Lista de Órdenes -->
                    <?php if (empty($ordenes)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-shopping-cart text-muted" style="font-size: 4rem;"></i>
                            <h4 class="mt-3 text-muted">No hay órdenes para mostrar</h4>
                            <p class="text-muted">No se encontraron órdenes con los filtros aplicados</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($ordenes as $orden): ?>
                            <div class="order-card">
                                <!-- Header de la Orden -->
                                <div class="order-header">
                                    <div class="row align-items-center">
                                        <div class="col-md-3">
                                            <h6 class="mb-0">
                                                <strong>Orden #<?= htmlspecialchars($orden['numero_orden']) ?></strong>
                                            </h6>
                                            <small class="text-muted">
                                                <?= date('d/m/Y H:i', strtotime($orden['fecha_orden'])) ?>
                                            </small>
                                        </div>
                                        <div class="col-md-3">
                                            <strong>Cliente:</strong> <?= htmlspecialchars($orden['nombre_cliente']) ?><br>
                                            <small class="text-muted"><?= htmlspecialchars($orden['email_cliente']) ?></small>
                                        </div>
                                        <div class="col-md-2">
                                            <span class="badge bg-<?= $orden['estado'] === 'entregada' ? 'success' : 
                                                                   ($orden['estado'] === 'pendiente' ? 'warning' : 'info') ?> status-badge">
                                                <?= ucfirst(str_replace('_', ' ', $orden['estado'])) ?>
                                            </span>
                                        </div>
                                        <div class="col-md-2">
                                            <strong>Total:</strong> $<?= number_format($orden['total'], 2) ?>
                                        </div>
                                        <div class="col-md-2 text-end">
                                            <button class="btn btn-sm btn-outline-primary" type="button" 
                                                    data-bs-toggle="collapse" 
                                                    data-bs-target="#orden-<?= $orden['id'] ?>">
                                                <i class="fas fa-eye"></i> Ver Detalles
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Detalles de la Orden (Colapsable) -->
                                <div class="collapse" id="orden-<?= $orden['id'] ?>">
                                    <div class="order-details">
                                        <!-- Resumen de la Orden -->
                                        <div class="order-summary">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <strong>Productos:</strong> <?= $orden['total_productos'] ?>
                                                </div>
                                                <div class="col-md-3">
                                                    <strong>Unidades:</strong> <?= $orden['total_unidades'] ?>
                                                </div>
                                                <div class="col-md-3">
                                                    <strong>Método Pago:</strong> <?= ucfirst($orden['metodo_pago']) ?>
                                                </div>
                                                <div class="col-md-3">
                                                    <strong>QR Generados:</strong> <?= $orden['total_unidades'] ?>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Lista de Productos con QR -->
                                        <h6 class="mb-3">
                                            <i class="fas fa-boxes"></i> Productos y Códigos QR
                                        </h6>
                                        
                                        <?php foreach ($orden['detalles'] as $detalle): ?>
                                            <div class="product-item">
                                                <div class="row align-items-center">
                                                    <div class="col-md-3">
                                                        <div class="d-flex align-items-center">
                                                            <?php if (!empty($detalle['imagen'])): ?>
                                                                <img src="/ofm/uploads/productos/<?= htmlspecialchars($detalle['imagen']) ?>" 
                                                                     alt="<?= htmlspecialchars($detalle['nombre_producto']) ?>" 
                                                                     style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;">
                                                            <?php else: ?>
                                                                <div style="width: 50px; height: 50px; background: #f8f9fa; border-radius: 5px; display: flex; align-items: center; justify-content: center;">
                                                                    <i class="fas fa-image text-muted"></i>
                                                                </div>
                                                            <?php endif; ?>
                                                            <div class="ms-3">
                                                                <strong><?= htmlspecialchars($detalle['nombre_producto']) ?></strong><br>
                                                                <small class="text-muted">
                                                                    Comercio: <?= htmlspecialchars($detalle['nombre_comercio'] ?? 'N/A') ?>
                                                                </small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <strong>Precio:</strong> $<?= number_format($detalle['precio_unitario'], 2) ?><br>
                                                        <small class="text-muted">
                                                            Cantidad: <?= $detalle['cantidad'] ?>
                                                        </small>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <strong>Total:</strong> $<?= number_format($detalle['precio_total'], 2) ?>
                                                    </div>
                                                    <div class="col-md-5">
                                                        <div class="row">
                                                            <?php for ($i = 1; $i <= $detalle['cantidad']; $i++): ?>
                                                                <div class="col-md-4">
                                                                    <div class="qr-code">
                                                                        <div id="qr-<?= $detalle['id'] ?>-<?= $i ?>"></div>
                                                                        <small class="text-muted">Unidad <?= $i ?></small>
                                                                        <br>
                                                                        <small class="text-muted"><?= htmlspecialchars($detalle['codigo_qr']) ?></small>
                                                                    </div>
                                                                </div>
                                                            <?php endfor; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>

                        <!-- Paginación -->
                        <?php if ($paginas > 1): ?>
                            <nav aria-label="Paginación de órdenes">
                                <ul class="pagination justify-content-center">
                                    <?php for ($i = 1; $i <= $paginas; $i++): ?>
                                        <li class="page-item <?= $i == $pagina ? 'active' : '' ?>">
                                            <a class="page-link" href="?pagina=<?= $i ?>&estado=<?= $filtros['estado'] ?>&fecha_desde=<?= $filtros['fecha_desde'] ?>&fecha_hasta=<?= $filtros['fecha_hasta'] ?>&cliente=<?= urlencode($filtros['cliente']) ?>">
                                                <?= $i ?>
                                            </a>
                                        </li>
                                    <?php endfor; ?>
                                </ul>
                            </nav>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js"></script>
    
    <script>
        // Generar QR para cada producto
        document.addEventListener('DOMContentLoaded', function() {
            const qrContainers = document.querySelectorAll('[id^="qr-"]');
            
            qrContainers.forEach(container => {
                const id = container.id;
                const codigoQR = container.nextElementSibling.nextElementSibling.textContent;
                
                // Generar QR
                QRCode.toCanvas(container, codigoQR, {
                    width: 80,
                    margin: 1,
                    color: {
                        dark: '#000000',
                        light: '#FFFFFF'
                    }
                }, function (error) {
                    if (error) {
                        container.innerHTML = '<small class="text-danger">Error QR</small>';
                    }
                });
            });
        });
    </script>
</body>
</html>
