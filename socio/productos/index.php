<?php
require_once __DIR__ . '/../../controllers/loginController.php';

// Verificar que el usuario esté autenticado y sea socio
$loginController->verificarAcceso('socio');

// Obtener información del usuario actual
$usuario = $loginController->obtenerUsuarioActual();

// Verificar expiración de sesión
$loginController->verificarExpiracionSesion();

// Obtener productos del socio
require_once __DIR__ . '/../../models/Producto.php';
$productoModel = new Producto($pdo);
$productos = $productoModel->obtenerPorSocio($usuario['id']);

// Obtener imágenes para cada producto
foreach ($productos as &$producto) {
    try {
        $stmt = $pdo->prepare("
            SELECT ruta FROM producto_imagenes 
            WHERE producto_id = ? AND principal = 1 
            ORDER BY orden ASC 
            LIMIT 1
        ");
        $stmt->execute([$producto['id']]);
        $imagen = $stmt->fetch(PDO::FETCH_ASSOC);
        $producto['imagen'] = $imagen ? $imagen['ruta'] : null;
    } catch (Exception $e) {
        $producto['imagen'] = null;
    }
}
unset($producto);

// Obtener categorías disponibles
$categorias = ['Electrónicos', 'Ropa', 'Hogar', 'Deportes', 'Belleza', 'Juguetes', 'Libros', 'Otros'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mis Productos - OFM Socio</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
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
        .product-status {
            font-size: 0.8rem;
            padding: 4px 8px;
            border-radius: 12px;
        }
        .status-active { background-color: #d4edda; color: #155724; }
        .status-inactive { background-color: #f8d7da; color: #721c24; }
        .status-low-stock { background-color: #fff3cd; color: #856404; }
        .product-image {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 8px;
        }
        .action-buttons .btn {
            margin: 2px;
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
                <a class="nav-link active" href="index.php">
                    <i class="fas fa-box"></i> Mis Productos
                </a>
                <a class="nav-link" href="../ventas/">
                    <i class="fas fa-chart-line"></i> Ventas
                </a>
                <a class="nav-link" href="../verificar-qr/">
                    <i class="fas fa-qrcode"></i> Verificar QR
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
                        <span class="navbar-brand">Mis Productos</span>
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

                <!-- Header Actions -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Gestión de Productos</h2>
                    <a href="agregar.php" class="btn btn-success">
                        <i class="fas fa-plus me-2"></i>Agregar Producto
                    </a>
                </div>

                <!-- Products Table -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Lista de Productos</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($productos)): ?>
                            <div class="text-center py-5">
                                <i class="fas fa-box fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No tienes productos aún</h5>
                                <p class="text-muted">Comienza agregando tu primer producto para vender</p>
                                <a href="agregar.php" class="btn btn-success">
                                    <i class="fas fa-plus me-2"></i>Agregar Primer Producto
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover" id="productosTable">
                                    <thead>
                                        <tr>
                                            <th>Imagen</th>
                                            <th>Nombre</th>
                                            <th>Categoría</th>
                                            <th>Precio</th>
                                            <th>Stock</th>
                                            <th>Estado</th>
                                            <th>Fecha</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($productos as $producto): ?>
                                            <tr>
                                                <td>
                                                    <?php if ($producto['imagen']): ?>
                                                        <img src="<?= htmlspecialchars($producto['imagen']) ?>" 
                                                             class="product-image" alt="<?= htmlspecialchars($producto['nombre']) ?>">
                                                    <?php else: ?>
                                                        <div class="product-image bg-light d-flex align-items-center justify-content-center">
                                                            <i class="fas fa-image text-muted"></i>
                                                        </div>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <strong><?= htmlspecialchars($producto['nombre']) ?></strong>
                                                    <?php if ($producto['descripcion']): ?>
                                                        <br><small class="text-muted"><?= htmlspecialchars(substr($producto['descripcion'], 0, 50)) ?>...</small>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <span class="badge bg-secondary"><?= htmlspecialchars($producto['categoria']) ?></span>
                                                </td>
                                                <td>
                                                    <strong class="text-success">$<?= number_format($producto['precio'], 2) ?></strong>
                                                    <?php if ($producto['precio_anterior'] && $producto['precio_anterior'] > $producto['precio']): ?>
                                                        <br><small class="text-muted text-decoration-line-through">$<?= number_format($producto['precio_anterior'], 2) ?></small>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if ($producto['stock'] > 5): ?>
                                                        <span class="badge bg-success"><?= $producto['stock'] ?></span>
                                                    <?php elseif ($producto['stock'] > 0): ?>
                                                        <span class="badge bg-warning"><?= $producto['stock'] ?></span>
                                                    <?php else: ?>
                                                        <span class="badge bg-danger">0</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if ($producto['status'] === 'activo' && $producto['stock'] > 0): ?>
                                                        <span class="product-status status-active">Activo</span>
                                                    <?php elseif ($producto['stock'] == 0): ?>
                                                        <span class="product-status status-inactive">Sin Stock</span>
                                                    <?php else: ?>
                                                        <span class="product-status status-inactive">Inactivo</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <small class="text-muted">
                                                        <?= date('d/m/Y', strtotime($producto['creado_en'])) ?>
                                                    </small>
                                                </td>
                                                <td class="action-buttons">
                                                    <a href="editar.php?id=<?= $producto['id'] ?>" 
                                                       class="btn btn-sm btn-outline-primary" 
                                                       title="Editar">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button class="btn btn-sm btn-outline-warning" 
                                                            onclick="toggleEstado(<?= $producto['id'] ?>, '<?= $producto['status'] === 'activo' ? 'inactivo' : 'activo' ?>')"
                                                            title="<?= $producto['status'] === 'activo' ? 'Desactivar' : 'Activar' ?>">
                                                        <i class="fas fa-<?= $producto['status'] === 'activo' ? 'eye-slash' : 'eye' ?>"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-danger" 
                                                            onclick="eliminarProducto(<?= $producto['id'] ?>)"
                                                            title="Eliminar">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
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
    
    <script>
        $(document).ready(function() {
            // Inicializar DataTable
            $('#productosTable').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json'
                },
                order: [[6, 'desc']], // Ordenar por fecha descendente
                pageLength: 10,
                responsive: true
            });
        });

        // Función para cambiar estado del producto
        function toggleEstado(productoId, nuevoEstado) {
            if (confirm('¿Estás seguro de que quieres cambiar el estado de este producto?')) {
                fetch('toggle-estado.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        producto_id: productoId,
                        status: nuevoEstado
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
                    alert('Error al cambiar el estado del producto');
                });
            }
        }

        // Función para eliminar producto
        function eliminarProducto(productoId) {
            if (confirm('¿Estás seguro de que quieres eliminar este producto? Esta acción no se puede deshacer.')) {
                fetch('eliminar.php', {
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
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al eliminar el producto');
                });
            }
        }
    </script>
</body>
</html>
