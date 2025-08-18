<?php
require_once __DIR__ . '/../../controllers/loginController.php';

// Verificar que el usuario esté autenticado y sea socio
$loginController->verificarAcceso('socio');

// Obtener información del usuario actual
$usuario = $loginController->obtenerUsuarioActual();

// Verificar expiración de sesión
$loginController->verificarExpiracionSesion();

// Obtener ventas del socio
require_once __DIR__ . '/../../models/Venta.php';
$ventaModel = new Venta($pdo);

// Filtros
$estado = $_GET['estado'] ?? '';
$fechaInicio = $_GET['fecha_inicio'] ?? '';
$fechaFin = $_GET['fecha_fin'] ?? '';

// Obtener ventas según filtros
if ($estado) {
    $ventas = $ventaModel->obtenerPorEstado($usuario['id'], $estado);
} elseif ($fechaInicio && $fechaFin) {
    $ventas = $ventaModel->obtenerPorPeriodo($usuario['id'], $fechaInicio, $fechaFin);
} else {
    $ventas = $ventaModel->obtenerPorSocio($usuario['id']);
}

// Obtener estadísticas
$estadisticas = $ventaModel->obtenerEstadisticasSocio($usuario['id'], 'mes');

// Estados disponibles
$estados = ['pendiente', 'pagado', 'enviado', 'entregado', 'cancelado'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mis Ventas - OFM Socio</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    <!-- DatePicker CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
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
        .status-badge {
            font-size: 0.8rem;
            padding: 6px 12px;
            border-radius: 20px;
        }
        .status-pendiente { background-color: #fff3cd; color: #856404; }
        .status-pagado { background-color: #d1ecf1; color: #0c5460; }
        .status-enviado { background-color: #d4edda; color: #155724; }
        .status-entregado { background-color: #d4edda; color: #155724; }
        .status-cancelado { background-color: #f8d7da; color: #721c24; }
        .filters-section {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
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
                <a class="nav-link" href="../dashboard.php">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
                <a class="nav-link" href="../productos/">
                    <i class="fas fa-box"></i> Mis Productos
                </a>
                <a class="nav-link active" href="index.php">
                    <i class="fas fa-chart-line"></i> Ventas
                </a>
                <a class="nav-link" href="../inventario/">
                    <i class="fas fa-warehouse"></i> Inventario
                </a>
                <a class="nav-link" href="../perfil/">
                    <i class="fas fa-user"></i> Mi Perfil
                </a>
                <a class="nav-link" href="../reportes/">
                    <i class="fas fa-chart-bar"></i> Reportes
                </a>
                <a class="nav-link" href="../dashboard.php?logout=1">
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
                        <span class="navbar-brand">Mis Ventas</span>
                        <div class="navbar-nav ms-auto">
                            <div class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-user-circle me-2"></i><?= htmlspecialchars($usuario['nombre']) ?>
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="../perfil/">Mi Perfil</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="../dashboard.php?logout=1">Cerrar Sesión</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </nav>

                <!-- Stats Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card stats-card">
                            <div class="card-body text-center">
                                <i class="fas fa-shopping-cart fa-2x mb-2"></i>
                                <h5>Total Ventas</h5>
                                <h3><?= $estadisticas['total_ventas'] ?? 0 ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stats-card">
                            <div class="card-body text-center">
                                <i class="fas fa-dollar-sign fa-2x mb-2"></i>
                                <h5>Ingresos Totales</h5>
                                <h3>$<?= number_format($estadisticas['ingresos_totales'] ?? 0, 2) ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stats-card">
                            <div class="card-body text-center">
                                <i class="fas fa-chart-line fa-2x mb-2"></i>
                                <h5>Promedio Venta</h5>
                                <h3>$<?= number_format($estadisticas['promedio_venta'] ?? 0, 2) ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stats-card">
                            <div class="card-body text-center">
                                <i class="fas fa-check-circle fa-2x mb-2"></i>
                                <h5>Completadas</h5>
                                <h3><?= $estadisticas['ventas_completadas'] ?? 0 ?></h3>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filtros -->
                <div class="filters-section">
                    <h5 class="mb-3">
                        <i class="fas fa-filter me-2"></i>Filtros
                    </h5>
                    <form method="GET" class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Estado</label>
                            <select name="estado" class="form-select">
                                <option value="">Todos los estados</option>
                                <?php foreach ($estados as $est): ?>
                                    <option value="<?= $est ?>" <?= $estado === $est ? 'selected' : '' ?>>
                                        <?= ucfirst($est) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Fecha Inicio</label>
                            <input type="text" name="fecha_inicio" class="form-control datepicker" 
                                   value="<?= htmlspecialchars($fechaInicio) ?>" placeholder="Seleccionar fecha">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Fecha Fin</label>
                            <input type="text" name="fecha_fin" class="form-control datepicker" 
                                   value="<?= htmlspecialchars($fechaFin) ?>" placeholder="Seleccionar fecha">
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="fas fa-search me-2"></i>Filtrar
                            </button>
                            <a href="index.php" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>Limpiar
                            </a>
                        </div>
                    </form>
                </div>

                <!-- Ventas Table -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Lista de Ventas</h5>
                        <div class="btn-group">
                            <button type="button" class="btn btn-outline-success btn-sm" onclick="exportarVentas()">
                                <i class="fas fa-download me-2"></i>Exportar
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if (empty($ventas)): ?>
                            <div class="text-center py-5">
                                <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No hay ventas registradas</h5>
                                <p class="text-muted">Las ventas aparecerán aquí cuando los clientes compren tus productos</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover" id="ventasTable">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Cliente</th>
                                            <th>Total</th>
                                            <th>Estado</th>
                                            <th>Método Pago</th>
                                            <th>Fecha</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($ventas as $venta): ?>
                                            <tr>
                                                <td>
                                                    <strong>#<?= $venta['id'] ?></strong>
                                                </td>
                                                <td>
                                                    <div>
                                                        <strong><?= htmlspecialchars($venta['cliente_nombre'] ?? 'Cliente') ?></strong>
                                                        <?php if ($venta['cliente_email']): ?>
                                                            <br><small class="text-muted"><?= htmlspecialchars($venta['cliente_email']) ?></small>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                                <td>
                                                    <strong class="text-success">$<?= number_format($venta['total'], 2) ?></strong>
                                                </td>
                                                <td>
                                                    <span class="status-badge status-<?= $venta['estado'] ?>">
                                                        <?= ucfirst($venta['estado']) ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php if ($venta['metodo_pago']): ?>
                                                        <span class="badge bg-info"><?= htmlspecialchars($venta['metodo_pago']) ?></span>
                                                    <?php else: ?>
                                                        <span class="text-muted">No especificado</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <small class="text-muted">
                                                        <?= date('d/m/Y H:i', strtotime($venta['creado_en'])) ?>
                                                    </small>
                                                </td>
                                                <td>
                                                    <div class="btn-group">
                                                        <button class="btn btn-sm btn-outline-primary" 
                                                                onclick="verDetalles(<?= $venta['id'] ?>)"
                                                                title="Ver Detalles">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                        <?php if ($venta['estado'] === 'pendiente'): ?>
                                                            <button class="btn btn-sm btn-outline-success" 
                                                                    onclick="cambiarEstado(<?= $venta['id'] ?>, 'pagado')"
                                                                    title="Marcar como Pagado">
                                                                <i class="fas fa-check"></i>
                                                            </button>
                                                        <?php elseif ($venta['estado'] === 'pagado'): ?>
                                                            <button class="btn btn-sm btn-outline-info" 
                                                                    onclick="cambiarEstado(<?= $venta['id'] ?>, 'enviado')"
                                                                    title="Marcar como Enviado">
                                                                <i class="fas fa-shipping-fast"></i>
                                                            </button>
                                                        <?php elseif ($venta['estado'] === 'enviado'): ?>
                                                            <button class="btn btn-sm btn-outline-success" 
                                                                    onclick="cambiarEstado(<?= $venta['id'] ?>, 'entregado')"
                                                                    title="Marcar como Entregado">
                                                                <i class="fas fa-box-open"></i>
                                                            </button>
                                                        <?php endif; ?>
                                                    </div>
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
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <!-- DatePicker JS -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>
    
    <script>
        $(document).ready(function() {
            // Inicializar DataTable
            $('#ventasTable').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json'
                },
                order: [[5, 'desc']], // Ordenar por fecha descendente
                pageLength: 15,
                responsive: true
            });

            // Inicializar DatePicker
            flatpickr(".datepicker", {
                locale: "es",
                dateFormat: "Y-m-d",
                allowInput: true
            });
        });

        // Función para cambiar estado de la venta
        function cambiarEstado(ventaId, nuevoEstado) {
            const estados = {
                'pagado': 'pagado',
                'enviado': 'enviado',
                'entregado': 'entregado'
            };
            
            const estadoTexto = estados[nuevoEstado] || nuevoEstado;
            
            if (confirm(`¿Estás seguro de que quieres marcar esta venta como ${estadoTexto}?`)) {
                fetch('cambiar-estado.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        venta_id: ventaId,
                        estado: nuevoEstado
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al cambiar el estado de la venta');
                });
            }
        }

        // Función para ver detalles de la venta
        function verDetalles(ventaId) {
            // Aquí puedes implementar un modal o redirigir a una página de detalles
            alert('Funcionalidad de detalles en desarrollo');
        }

        // Función para exportar ventas
        function exportarVentas() {
            // Aquí puedes implementar la exportación a CSV o Excel
            alert('Funcionalidad de exportación en desarrollo');
        }
    </script>
</body>
</html>
