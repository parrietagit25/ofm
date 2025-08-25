<?php
/**
 * Página principal de gestión de productos - Panel Administrativo OFM
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
    'status' => $_GET['status'] ?? '',
    'categoria' => $_GET['categoria'] ?? '',
    'comercio_id' => $_GET['comercio_id'] ?? '',
    'destacado' => $_GET['destacado'] ?? ''
];

// Obtener productos
require_once __DIR__ . '/../../controllers/productoController.php';
$productoController = new ProductoController($pdo);
$resultado = $productoController->obtenerTodos($pagina, 10, $filtros);

$productos = $resultado['productos'];
$total = $resultado['total'];
$paginas = $resultado['paginas'];
$paginaActual = $resultado['pagina_actual'];

// Obtener comercios para filtros
$comercios = [];
try {
    $stmt = $pdo->query("SELECT id, nombre_comercio FROM comercios ORDER BY nombre_comercio");
    $comercios = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $comercios = [];
}

// Obtener categorías para filtros
$categorias = [];
try {
    $stmt = $pdo->query("SELECT DISTINCT categoria FROM productos WHERE categoria IS NOT NULL AND categoria != '' ORDER BY categoria");
    $categorias = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (Exception $e) {
    $categorias = [];
}

// Obtener mensajes de éxito o error
$success = $_GET['success'] ?? '';
$error = $_GET['error'] ?? '';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Productos - Panel Administrativo OFM</title>
    
    <!-- AdminLTE CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/css/adminlte.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        .product-image {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 5px;
        }
        .status-badge {
            font-size: 0.75rem;
        }
        .filters-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
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
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1>Gestión de Productos</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="../dashboard.php">Inicio</a></li>
                                <li class="breadcrumb-item active">Productos</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </section>
            
            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <!-- Mensajes de éxito/error -->
                    <?php if ($success): ?>
                        <div class="alert alert-success alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <i class="fas fa-check-circle"></i> <?= htmlspecialchars($success) ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-danger alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Filtros -->
                    <div class="filters-section">
                        <form method="GET" class="row">
                            <div class="col-md-2">
                                <label for="status">Estado:</label>
                                <select name="status" id="status" class="form-control">
                                    <option value="">Todos</option>
                                    <option value="activo" <?= $filtros['status'] === 'activo' ? 'selected' : '' ?>>Activo</option>
                                    <option value="inactivo" <?= $filtros['status'] === 'inactivo' ? 'selected' : '' ?>>Inactivo</option>
                                    <option value="agotado" <?= $filtros['status'] === 'agotado' ? 'selected' : '' ?>>Agotado</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="categoria">Categoría:</label>
                                <select name="categoria" id="categoria" class="form-control">
                                    <option value="">Todas</option>
                                    <?php foreach ($categorias as $categoria): ?>
                                        <option value="<?= htmlspecialchars($categoria) ?>" <?= $filtros['categoria'] === $categoria ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($categoria) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="comercio_id">Comercio:</label>
                                <select name="comercio_id" id="comercio_id" class="form-control">
                                    <option value="">Todos</option>
                                    <option value="null" <?= $filtros['comercio_id'] === 'null' ? 'selected' : '' ?>>OFM</option>
                                    <?php foreach ($comercios as $comercio): ?>
                                        <option value="<?= $comercio['id'] ?>" <?= $filtros['comercio_id'] == $comercio['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($comercio['nombre_comercio']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="destacado">Destacado:</label>
                                <select name="destacado" id="destacado" class="form-control">
                                    <option value="">Todos</option>
                                    <option value="1" <?= $filtros['destacado'] === '1' ? 'selected' : '' ?>>Sí</option>
                                    <option value="0" <?= $filtros['destacado'] === '0' ? 'selected' : '' ?>>No</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label>&nbsp;</label>
                                <div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-filter"></i> Filtrar
                                    </button>
                                    <a href="index.php" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> Limpiar
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Botón Crear Producto -->
                    <div class="mb-3">
                        <a href="crear.php" class="btn btn-success">
                            <i class="fas fa-plus"></i> Crear Nuevo Producto
                        </a>
                    </div>
                    
                    <!-- Tabla de Productos -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Lista de Productos (<?= $total ?> total)</h3>
                        </div>
                        <div class="card-body">
                            <?php if (empty($productos)): ?>
                                <div class="text-center py-4">
                                    <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No se encontraron productos con los filtros aplicados.</p>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Imagen</th>
                                                <th>Nombre</th>
                                                <th>Precio</th>
                                                <th>Stock</th>
                                                <th>Categoría</th>
                                                <th>Comercio</th>
                                                <th>Estado</th>
                                                <th>Destacado</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($productos as $producto): ?>
                                                <tr>
                                                    <td>
                                                        <?php if (!empty($producto['imagen_principal'])): ?>
                                                            <img src="/ofm/public/uploads/productos/<?= htmlspecialchars($producto['imagen_principal']) ?>" 
                                                                 alt="Imagen" class="product-image">
                                                        <?php else: ?>
                                                            <div class="product-image bg-light d-flex align-items-center justify-content-center">
                                                                <i class="fas fa-image text-muted"></i>
                                                            </div>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <strong><?= htmlspecialchars($producto['nombre']) ?></strong>
                                                        <br>
                                                        <small class="text-muted"><?= htmlspecialchars($producto['codigo_producto'] ?? 'Sin código') ?></small>
                                                    </td>
                                                    <td>
                                                        <?php if ($producto['precio_oferta'] && $producto['precio_oferta'] < $producto['precio']): ?>
                                                            <span class="text-decoration-line-through text-muted">
                                                                $<?= number_format($producto['precio'], 2) ?>
                                                            </span>
                                                            <br>
                                                            <span class="text-danger font-weight-bold">
                                                                $<?= number_format($producto['precio_oferta'], 2) ?>
                                                            </span>
                                                        <?php else: ?>
                                                            <span class="font-weight-bold">
                                                                $<?= number_format($producto['precio'], 2) ?>
                                                            </span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <span class="badge badge-<?= $producto['stock'] > 0 ? 'success' : 'danger' ?>">
                                                            <?= $producto['stock'] ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <?= htmlspecialchars($producto['categoria'] ?? 'Sin categoría') ?>
                                                    </td>
                                                    <td>
                                                        <?= htmlspecialchars($producto['nombre_comercio'] ?? 'OFM') ?>
                                                    </td>
                                                    <td>
                                                        <?php
                                                        $statusClass = 'secondary';
                                                        $statusText = 'Desconocido';
                                                        
                                                        switch ($producto['status']) {
                                                            case 'activo':
                                                                $statusClass = 'success';
                                                                $statusText = 'Activo';
                                                                break;
                                                            case 'inactivo':
                                                                $statusClass = 'danger';
                                                                $statusText = 'Inactivo';
                                                                break;
                                                            case 'agotado':
                                                                $statusClass = 'warning';
                                                                $statusText = 'Agotado';
                                                                break;
                                                        }
                                                        ?>
                                                        <span class="badge badge-<?= $statusClass ?> status-badge">
                                                            <?= $statusText ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <button type="button" 
                                                                class="btn btn-sm btn-<?= $producto['destacado'] ? 'warning' : 'secondary' ?> toggle-destacado"
                                                                data-id="<?= $producto['id'] ?>"
                                                                data-destacado="<?= $producto['destacado'] ?>">
                                                            <i class="fas fa-star"></i>
                                                            <?= $producto['destacado'] ? 'Destacado' : 'Normal' ?>
                                                        </button>
                                                    </td>
                                                    <td>
                                                        <div class="btn-group">
                                                            <a href="ver.php?id=<?= $producto['id'] ?>" 
                                                               class="btn btn-sm btn-info" 
                                                               title="Ver">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                            <a href="editar.php?id=<?= $producto['id'] ?>" 
                                                               class="btn btn-sm btn-warning" 
                                                               title="Editar">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                            <button type="button" 
                                                                    class="btn btn-sm btn-danger eliminar-producto" 
                                                                    data-id="<?= $producto['id'] ?>"
                                                                    data-nombre="<?= htmlspecialchars($producto['nombre']) ?>"
                                                                    title="Eliminar">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                
                                <!-- Paginación -->
                                <?php if ($paginas > 1): ?>
                                    <nav aria-label="Paginación de productos">
                                        <ul class="pagination justify-content-center">
                                            <?php if ($paginaActual > 1): ?>
                                                <li class="page-item">
                                                    <a class="page-link" href="?pagina=<?= $paginaActual - 1 ?>&<?= http_build_query($filtros) ?>">
                                                        <i class="fas fa-chevron-left"></i> Anterior
                                                    </a>
                                                </li>
                                            <?php endif; ?>
                                            
                                            <?php for ($i = max(1, $paginaActual - 2); $i <= min($paginas, $paginaActual + 2); $i++): ?>
                                                <li class="page-item <?= $i == $paginaActual ? 'active' : '' ?>">
                                                    <a class="page-link" href="?pagina=<?= $i ?>&<?= http_build_query($filtros) ?>">
                                                        <?= $i ?>
                                                    </a>
                                                </li>
                                            <?php endfor; ?>
                                            
                                            <?php if ($paginaActual < $paginas): ?>
                                                <li class="page-item">
                                                    <a class="page-link" href="?pagina=<?= $paginaActual + 1 ?>&<?= http_build_query($filtros) ?>">
                                                        Siguiente <i class="fas fa-chevron-right"></i>
                                                    </a>
                                                </li>
                                            <?php endif; ?>
                                        </ul>
                                    </nav>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        
        <!-- Footer -->
        <?php include '../../admin/includes/footer.php'; ?>
    </div>
    
    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/js/adminlte.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Eliminar producto
            $('.eliminar-producto').click(function() {
                const id = $(this).data('id');
                const nombre = $(this).data('nombre');
                
                if (confirm(`¿Estás seguro de que quieres eliminar el producto "${nombre}"?`)) {
                    const form = $('<form method="POST" action="/ofm/controllers/productoController.php">');
                    form.append('<input type="hidden" name="action" value="eliminar">');
                    form.append('<input type="hidden" name="id" value="' + id + '">');
                    $('body').append(form);
                    form.submit();
                }
            });
            
            // Cambiar destacado
            $('.toggle-destacado').click(function() {
                const button = $(this);
                const id = button.data('id');
                const destacado = button.data('destacado');
                const nuevoDestacado = destacado ? 0 : 1;
                
                $.post('/ofm/controllers/productoController.php', {
                    action: 'cambiar_destacado',
                    id: id,
                    destacado: nuevoDestacado
                }, function(response) {
                    if (response.success) {
                        // Actualizar botón
                        button.data('destacado', nuevoDestacado);
                        if (nuevoDestacado) {
                            button.removeClass('btn-secondary').addClass('btn-warning');
                            button.html('<i class="fas fa-star"></i> Destacado');
                        } else {
                            button.removeClass('btn-warning').addClass('btn-secondary');
                            button.html('<i class="fas fa-star"></i> Normal');
                        }
                        
                        // Mostrar mensaje
                        alert(response.message);
                    } else {
                        alert('Error: ' + response.message);
                    }
                }, 'json').fail(function() {
                    alert('Error al procesar la solicitud');
                });
            });
        });
    </script>
</body>
</html>
