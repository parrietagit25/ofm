<?php
/**
 * Dashboard Principal - Panel Administrativo OFM
 */

// Verificar autenticación primero
require_once __DIR__ . '/auth_check.php';

// Incluir archivos necesarios
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../controllers/loginController_simple.php';

// Inicializar controlador de login para los includes
$pdo = getConnection();
$loginController = new LoginControllerSimple($pdo);

// Obtener conexión a la base de datos
$pdo = getConnection();

// Obtener estadísticas
$stats = [];
try {
    // Total usuarios
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM usuarios");
    $stats['usuarios'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Total productos
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM productos");
    $stats['productos'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Total órdenes
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM ordenes");
    $stats['ordenes'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Total comercios
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM comercios");
    $stats['comercios'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Ventas del día
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM ordenes WHERE DATE(fecha_orden) = CURDATE()");
    $stats['ventas_hoy'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Total ventas
    $stmt = $pdo->query("SELECT SUM(total) as total FROM ordenes WHERE estado = 'entregada'");
    $stats['total_ventas'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
    
} catch (PDOException $e) {
    error_log("Error obteniendo estadísticas: " . $e->getMessage());
    $stats = [
        'usuarios' => 0,
        'productos' => 0,
        'ordenes' => 0,
        'comercios' => 0,
        'ventas_hoy' => 0,
        'total_ventas' => 0
    ];
}

$pageTitle = 'Dashboard - Panel Administrativo OFM';
?>

<?php include __DIR__ . '/includes/header.php'; ?>
<?php include __DIR__ . '/includes/sidebar.php'; ?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Dashboard</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Inicio</a></li>
                        <li class="breadcrumb-item active">Dashboard</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <!-- Info boxes -->
            <div class="row">
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="info-box">
                        <span class="info-box-icon bg-info"><i class="fas fa-users"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Usuarios</span>
                            <span class="info-box-number"><?= $stats['usuarios'] ?></span>
                        </div>
                    </div>
                </div>
                
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="info-box">
                        <span class="info-box-icon bg-success"><i class="fas fa-store"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Comercios</span>
                            <span class="info-box-number"><?= $stats['comercios'] ?></span>
                        </div>
                    </div>
                </div>
                
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="info-box">
                        <span class="info-box-icon bg-warning"><i class="fas fa-box"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Productos</span>
                            <span class="info-box-number"><?= $stats['productos'] ?></span>
                        </div>
                    </div>
                </div>
                
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="info-box">
                        <span class="info-box-icon bg-danger"><i class="fas fa-shopping-cart"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Órdenes</span>
                            <span class="info-box-number"><?= $stats['ordenes'] ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Ventas del Día</h3>
                        </div>
                        <div class="card-body">
                            <h2 class="text-center text-success"><?= $stats['ventas_hoy'] ?></h2>
                            <p class="text-center">Órdenes realizadas hoy</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Total Ventas</h3>
                        </div>
                        <div class="card-body">
                            <h2 class="text-center text-primary">$<?= number_format($stats['total_ventas'], 2) ?></h2>
                            <p class="text-center">Ventas totales entregadas</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Acciones Rápidas</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3 mb-2">
                                    <a href="usuarios/" class="btn btn-primary btn-block">
                                        <i class="fas fa-users"></i> Gestionar Usuarios
                                    </a>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <a href="comercios/" class="btn btn-success btn-block">
                                        <i class="fas fa-store"></i> Gestionar Comercios
                                    </a>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <a href="productos/" class="btn btn-warning btn-block">
                                        <i class="fas fa-box"></i> Gestionar Productos
                                    </a>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <a href="ordenes-clientes/" class="btn btn-info btn-block">
                                        <i class="fas fa-shopping-cart"></i> Ver Órdenes
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
