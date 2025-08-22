<?php
require_once __DIR__ . '/../controllers/loginController.php';

// Verificar que el usuario esté autenticado y sea socio
$loginController->verificarAcceso('socio');

// Manejar logout
if (isset($_GET['logout']) && $_GET['logout'] == 1) {
    $loginController->cerrarSesion();
    header('Location: /ofm/public/evara/page-login-register.php?logout=success');
    exit;
}

// Obtener información del usuario actual
$usuario = $loginController->obtenerUsuarioActual();

// Verificar expiración de sesión
$loginController->verificarExpiracionSesion();

// Obtener comercio del socio
require_once __DIR__ . '/../models/Comercio.php';
$comercioModel = new Comercio($pdo);
$comercios = $comercioModel->obtenerPorUsuarioSocio($usuario['id']);
$comercio = !empty($comercios) ? $comercios[0] : null;

// Obtener estadísticas del socio
require_once __DIR__ . '/../models/Producto.php';
require_once __DIR__ . '/../models/Venta.php';

$productoModel = new Producto($pdo);
$ventaModel = new Venta($pdo);

// Obtener productos del socio (solo si tiene comercio)
$productosSocio = [];
$totalProductos = 0;
if ($comercio) {
    $productosSocio = $productoModel->obtenerPorSocio($usuario['id']);
    $totalProductos = count($productosSocio);
}

// Obtener ventas del socio (solo si tiene comercio)
$ventasSocio = [];
$totalVentas = 0;
if ($comercio) {
    $ventasSocio = $ventaModel->obtenerPorSocio($usuario['id']);
    $totalVentas = count($ventasSocio);
}

// Calcular ingresos del mes
$ingresosMes = 0;
foreach ($ventasSocio as $venta) {
    if (date('Y-m', strtotime($venta['creado_en'])) === date('Y-m')) {
        $ingresosMes += $venta['total'];
    }
}

// Contar productos con stock bajo
$productosStockBajo = 0;
foreach ($productosSocio as $producto) {
    if ($producto['stock'] <= 2 && $producto['stock'] > 0) {
        $productosStockBajo++;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard Socio - OFM</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Chart.js -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js@3.7.0/dist/chart.min.css">
    <!-- Custom CSS -->
    <style>
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            position: fixed;
            width: 250px;
            z-index: 1000;
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 12px 20px;
            margin: 2px 0;
            border-radius: 8px;
            transition: all 0.3s;
            text-decoration: none;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: white;
            background: rgba(255,255,255,0.1);
            transform: translateX(5px);
        }
        .sidebar .nav-link i {
            margin-right: 10px;
            width: 20px;
        }
        .main-content {
            margin-left: 250px;
            background-color: #f8f9fa;
            min-height: 100vh;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            transition: transform 0.2s;
        }
        .card:hover {
            transform: translateY(-2px);
        }
        .stats-card {
            background: linear-gradient(45deg, #28a745, #20c997);
            color: white;
        }
        .navbar-brand {
            font-weight: bold;
            color: #28a745;
        }
        .user-info {
            background: linear-gradient(45deg, #28a745, #20c997);
            color: white;
            border-radius: 15px;
            padding: 20px;
        }
        .product-status {
            font-size: 0.8rem;
            padding: 4px 8px;
            border-radius: 12px;
        }
        .status-active { background-color: #d4edda; color: #155724; }
        .status-inactive { background-color: #f8d7da; color: #721c24; }
        .status-low-stock { background-color: #fff3cd; color: #856404; }
        .quick-actions {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .quick-action-btn {
            display: flex;
            align-items: center;
            padding: 15px;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            text-decoration: none;
            color: #495057;
            transition: all 0.3s;
            margin-bottom: 10px;
        }
        .quick-action-btn:hover {
            border-color: #28a745;
            background-color: #f8f9fa;
            color: #28a745;
            text-decoration: none;
        }
        .quick-action-btn i {
            font-size: 24px;
            margin-right: 15px;
            color: #28a745;
        }
    </style>
</head>
<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <div class="sidebar p-3">
            <div class="text-center mb-4">
                <h4 class="text-white">OFM Socio</h4>
                <small class="text-white-50">Panel de Negocio</small>
            </div>
            
            <nav class="nav flex-column">
                <a class="nav-link active" href="dashboard.php">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
                <a class="nav-link" href="productos/">
                    <i class="fas fa-box"></i> Mis Productos
                </a>
                <a class="nav-link" href="ventas/">
                    <i class="fas fa-chart-line"></i> Ventas
                </a>
                <a class="nav-link" href="verificar-qr/">
                    <i class="fas fa-qrcode"></i> Verificar QR
                </a>
                <a class="nav-link" href="perfil/">
                    <i class="fas fa-user"></i> Mi Perfil
                </a>
                <a class="nav-link" href="reportes/">
                    <i class="fas fa-chart-bar"></i> Reportes
                </a>
                <a class="nav-link" href="?logout=1">
                    <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                </a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="main-content flex-grow-1">
            <div class="p-4">
                <!-- Top Navbar -->
                <nav class="navbar navbar-expand-lg navbar-light bg-white mb-4 rounded shadow-sm">
                    <div class="container-fluid">
                        <span class="navbar-brand">Dashboard Socio</span>
                        <div class="navbar-nav ms-auto">
                            <div class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-user-circle me-2"></i><?= htmlspecialchars($usuario['nombre']) ?>
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="perfil/">Mi Perfil</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="?logout=1">Cerrar Sesión</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </nav>

                <!-- Quick Actions -->
                <div class="quick-actions">
                    <h5 class="mb-3">Acciones Rápidas</h5>
                    <?php if ($comercio): ?>
                        <div class="row">
                            <div class="col-md-3">
                                <a href="productos/agregar.php" class="quick-action-btn">
                                    <i class="fas fa-plus"></i>
                                    <div>
                                        <strong>Agregar Producto</strong>
                                        <small class="d-block text-muted">Crear nuevo producto</small>
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="inventario/" class="quick-action-btn">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <div>
                                        <strong>Stock Bajo</strong>
                                        <small class="d-block text-muted"><?= $productosStockBajo ?> productos</small>
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="ventas/" class="quick-action-btn">
                                    <i class="fas fa-shopping-cart"></i>
                                    <div>
                                        <strong>Nuevas Ventas</strong>
                                        <small class="d-block text-muted">Ver pedidos</small>
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="reportes/" class="quick-action-btn">
                                    <i class="fas fa-chart-bar"></i>
                                    <div>
                                        <strong>Reportes</strong>
                                        <small class="d-block text-muted">Análisis de ventas</small>
                                    </div>
                                </a>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info text-center">
                            <i class="fas fa-info-circle fa-2x mb-3"></i>
                            <h5>Comercio Requerido</h5>
                            <p class="mb-3">Para acceder a las funcionalidades de productos y ventas, primero necesitas tener un comercio asignado.</p>
                            <p class="mb-0"><strong>Contacta al administrador del sistema para registrar tu comercio.</strong></p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Stats Cards -->
                <?php if ($comercio): ?>
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card stats-card">
                                <div class="card-body text-center">
                                    <i class="fas fa-box fa-2x mb-2"></i>
                                    <h5>Total Productos</h5>
                                    <h3><?= $totalProductos ?></h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card stats-card">
                                <div class="card-body text-center">
                                    <i class="fas fa-shopping-cart fa-2x mb-2"></i>
                                    <h5>Ventas del Mes</h5>
                                    <h3><?= $totalVentas ?></h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card stats-card">
                                <div class="card-body text-center">
                                    <i class="fas fa-dollar-sign fa-2x mb-2"></i>
                                    <h5>Ingresos del Mes</h5>
                                    <h3>$<?= number_format($ingresosMes, 2) ?></h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card stats-card">
                                <div class="card-body text-center">
                                    <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                                    <h5>Stock Bajo</h5>
                                    <h3><?= $productosStockBajo ?></h3>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <i class="fas fa-store fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">Sin Comercio Asignado</h5>
                                    <p class="text-muted mb-0">Las estadísticas estarán disponibles una vez que tengas un comercio asignado.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Main Content Row -->
                <?php if ($comercio): ?>
                    <div class="row">
                        <div class="col-md-8">
                            <!-- Recent Sales -->
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">Ventas Recientes</h5>
                                    <a href="ventas/" class="btn btn-sm btn-outline-primary">Ver Todas</a>
                                </div>
                                <div class="card-body">
                                    <?php if (empty($ventasSocio)): ?>
                                        <p class="text-muted text-center">No hay ventas recientes</p>
                                    <?php else: ?>
                                        <?php foreach (array_slice($ventasSocio, 0, 5) as $venta): ?>
                                            <div class="d-flex align-items-center mb-3">
                                                <div class="bg-success rounded-circle p-2 me-3">
                                                    <i class="fas fa-check text-white"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">Venta #<?= $venta['id'] ?></h6>
                                                    <small class="text-muted">Total: $<?= number_format($venta['total'], 2) ?></small>
                                                </div>
                                                <small class="text-muted ms-auto">
                                                    <?= date('d/m/Y', strtotime($venta['creado_en'])) ?>
                                                </small>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <div class="col-md-4">
                        <!-- User Info -->
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Información del Socio</h5>
                            </div>
                            <div class="card-body">
                                <div class="user-info">
                                    <div class="text-center mb-3">
                                        <i class="fas fa-store fa-3x"></i>
                                    </div>
                                    <h6 class="text-center"><?= htmlspecialchars($usuario['nombre']) ?></h6>
                                    <p class="text-center mb-2"><?= htmlspecialchars($usuario['email']) ?></p>
                                    <div class="text-center mb-3">
                                        <span class="badge bg-light text-dark">Socio</span>
                                    </div>
                                    
                                    <?php if ($comercio): ?>
                                        <!-- Información del comercio -->
                                        <div class="border-top pt-3">
                                            <h6 class="text-center mb-2">
                                                <i class="fas fa-building me-2"></i>Comercio Asignado
                                            </h6>
                                            <h6 class="text-center text-white"><?= htmlspecialchars($comercio['nombre_comercio']) ?></h6>
                                            <p class="text-center mb-1 small">
                                                <i class="fas fa-map-marker-alt me-1"></i>
                                                <?= htmlspecialchars($comercio['direccion']) ?>
                                            </p>
                                            <p class="text-center mb-0 small">
                                                <i class="fas fa-phone me-1"></i>
                                                <?= htmlspecialchars($comercio['telefono_comercio']) ?>
                                            </p>
                                        </div>
                                    <?php else: ?>
                                        <!-- Sin comercio asignado -->
                                        <div class="border-top pt-3">
                                            <div class="alert alert-warning mb-0 text-center">
                                                <i class="fas fa-exclamation-triangle me-2"></i>
                                                <strong>Sin Comercio Asignado</strong>
                                                <br>
                                                <small>Contacta al administrador para registrar tu comercio</small>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Stats -->
                        <div class="card mt-3">
                            <div class="card-header">
                                <h6 class="mb-0">Estadísticas Rápidas</h6>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Productos Activos:</span>
                                    <strong><?= $totalProductos ?></strong>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Ventas del Mes:</span>
                                    <strong><?= $totalVentas ?></strong>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span>Stock Bajo:</span>
                                    <strong class="text-warning"><?= $productosStockBajo ?></strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php else: ?>
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body text-center">
                                <i class="fas fa-store fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">Sin Comercio Asignado</h5>
                                <p class="text-muted mb-0">Para ver ventas recientes y estadísticas, primero necesitas tener un comercio asignado.</p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script>
        // Activar navegación
        document.addEventListener('DOMContentLoaded', function() {
            const currentPath = window.location.pathname;
            const navLinks = document.querySelectorAll('.sidebar .nav-link');
            
            navLinks.forEach(link => {
                if (link.getAttribute('href') === currentPath.split('/').pop() || 
                    (currentPath.includes('dashboard.php') && link.textContent.includes('Dashboard'))) {
                    link.classList.add('active');
                }
            });
        });
    </script>
</body>
</html>
