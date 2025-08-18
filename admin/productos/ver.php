<?php
/**
 * Página para ver detalles de un producto - Panel Administrativo OFM
 */

require_once __DIR__ . '/../../includes/session_helper.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../controllers/loginController_simple.php';

// Verificar acceso de administrador
$loginController = new LoginControllerSimple($pdo);
$loginController->verificarAcceso('admin');

// Obtener ID del producto
$id = $_GET['id'] ?? 0;
if (!$id) {
    header('Location: index.php?error=ID de producto no especificado');
    exit;
}

// Obtener producto
require_once __DIR__ . '/../../controllers/productoController.php';
$productoController = new ProductoController($pdo);
$producto = $productoController->obtenerPorId($id);

if (!$producto) {
    header('Location: index.php?error=Producto no encontrado');
    exit;
}

// Obtener imágenes del producto
$imagenes = [];
try {
    $stmt = $pdo->prepare("SELECT * FROM producto_imagenes WHERE producto_id = ? ORDER BY creado_en ASC");
    $stmt->execute([$id]);
    $imagenes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $imagenes = [];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Producto - Panel Administrativo OFM</title>
    
    <!-- AdminLTE CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/css/adminlte.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        .product-image {
            width: 100%;
            max-width: 400px;
            height: auto;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .image-gallery {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 15px;
        }
        .gallery-image {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 5px;
            cursor: pointer;
            transition: transform 0.2s;
        }
        .gallery-image:hover {
            transform: scale(1.05);
        }
        .status-badge {
            font-size: 0.9rem;
            padding: 8px 12px;
        }
        .info-card {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .info-label {
            font-weight: bold;
            color: #495057;
            margin-bottom: 5px;
        }
        .info-value {
            color: #212529;
            margin-bottom: 15px;
        }
        .price-section {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            margin-bottom: 20px;
        }
        .price-main {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .price-offer {
            font-size: 1.5rem;
            opacity: 0.9;
        }
        .price-original {
            text-decoration: line-through;
            opacity: 0.7;
            font-size: 1.2rem;
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
                            <h1>Ver Producto</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="../dashboard.php">Inicio</a></li>
                                <li class="breadcrumb-item"><a href="index.php">Productos</a></li>
                                <li class="breadcrumb-item active">Ver</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </section>
            
            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <!-- Botones de acción -->
                    <div class="mb-3">
                        <a href="editar.php?id=<?= $producto['id'] ?>" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Editar Producto
                        </a>
                        <a href="index.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Volver a la Lista
                        </a>
                        <button type="button" class="btn btn-danger float-right eliminar-producto" 
                                data-id="<?= $producto['id'] ?>" 
                                data-nombre="<?= htmlspecialchars($producto['nombre']) ?>">
                            <i class="fas fa-trash"></i> Eliminar Producto
                        </button>
                    </div>
                    
                    <div class="row">
                        <!-- Columna izquierda - Imagen principal -->
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body text-center">
                                    <?php if (!empty($imagenes)): ?>
                                        <img src="/ofm/public/uploads/productos/<?= htmlspecialchars($imagenes[0]['nombre_archivo']) ?>" 
                                             alt="Imagen principal" class="product-image" id="mainImage">
                                    <?php else: ?>
                                        <div class="product-image bg-light d-flex align-items-center justify-content-center">
                                            <i class="fas fa-image fa-4x text-muted"></i>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <!-- Galería de imágenes -->
                                    <?php if (count($imagenes) > 1): ?>
                                        <div class="image-gallery">
                                            <?php foreach ($imagenes as $imagen): ?>
                                                <img src="/ofm/public/uploads/productos/<?= htmlspecialchars($imagen['nombre_archivo']) ?>" 
                                                     alt="Imagen" class="gallery-image"
                                                     onclick="changeMainImage(this.src)">
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Columna derecha - Información del producto -->
                        <div class="col-md-8">
                            <!-- Nombre y estado -->
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title"><?= htmlspecialchars($producto['nombre']) ?></h3>
                                    <div class="card-tools">
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
                                        
                                        <?php if ($producto['destacado']): ?>
                                            <span class="badge badge-warning status-badge ml-2">
                                                <i class="fas fa-star"></i> Destacado
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <!-- Precios -->
                                    <div class="price-section">
                                        <?php if ($producto['precio_oferta'] && $producto['precio_oferta'] < $producto['precio']): ?>
                                            <div class="price-offer">
                                                Oferta: $<?= number_format($producto['precio_oferta'], 2) ?>
                                            </div>
                                            <div class="price-original">
                                                Precio normal: $<?= number_format($producto['precio'], 2) ?>
                                            </div>
                                        <?php else: ?>
                                            <div class="price-main">
                                                $<?= number_format($producto['precio'], 2) ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <!-- Información básica -->
                                    <div class="info-card">
                                        <h5><i class="fas fa-info-circle"></i> Información Básica</h5>
                                        
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="info-label">Código del Producto:</div>
                                                <div class="info-value">
                                                    <?= htmlspecialchars($producto['codigo_producto'] ?? 'No especificado') ?>
                                                </div>
                                                
                                                <div class="info-label">Categoría:</div>
                                                <div class="info-value">
                                                    <?= htmlspecialchars($producto['categoria'] ?? 'Sin categoría') ?>
                                                </div>
                                                
                                                <div class="info-label">Marca:</div>
                                                <div class="info-value">
                                                    <?= htmlspecialchars($producto['marca'] ?? 'No especificada') ?>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="info-label">Stock:</div>
                                                <div class="info-value">
                                                    <span class="badge badge-<?= $producto['stock'] > 0 ? 'success' : 'danger' ?>">
                                                        <?= $producto['stock'] ?>
                                                    </span>
                                                </div>
                                                
                                                <div class="info-label">Peso:</div>
                                                <div class="info-value">
                                                    <?= $producto['peso'] ? $producto['peso'] . ' kg' : 'No especificado' ?>
                                                </div>
                                                
                                                <div class="info-label">Dimensiones:</div>
                                                <div class="info-value">
                                                    <?= htmlspecialchars($producto['dimensiones'] ?? 'No especificadas') ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Descripción -->
                                    <div class="info-card">
                                        <h5><i class="fas fa-align-left"></i> Descripción</h5>
                                        <div class="info-value">
                                            <?= nl2br(htmlspecialchars($producto['descripcion'] ?? 'Sin descripción')) ?>
                                        </div>
                                    </div>
                                    
                                    <!-- Información del comercio -->
                                    <div class="info-card">
                                        <h5><i class="fas fa-store"></i> Información del Comercio</h5>
                                        <?php if ($producto['comercio_id'] && $producto['comercio']): ?>
                                            <div class="info-label">Comercio:</div>
                                            <div class="info-value">
                                                <strong><?= htmlspecialchars($producto['comercio']['nombre_comercio']) ?></strong>
                                            </div>
                                            <div class="info-label">Dirección:</div>
                                            <div class="info-value">
                                                <?= htmlspecialchars($producto['comercio']['direccion'] ?? 'No especificada') ?>
                                            </div>
                                        <?php else: ?>
                                            <div class="info-value">
                                                <span class="badge badge-primary">Producto OFM</span>
                                                <small class="text-muted ml-2">Producto propio de la plataforma</small>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <!-- Información del sistema -->
                                    <div class="info-card">
                                        <h5><i class="fas fa-cog"></i> Información del Sistema</h5>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="info-label">ID del Producto:</div>
                                                <div class="info-value">#<?= $producto['id'] ?></div>
                                                
                                                <div class="info-label">Fecha de Creación:</div>
                                                <div class="info-value">
                                                    <?= date('d/m/Y H:i', strtotime($producto['creado_en'])) ?>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="info-label">Última Actualización:</div>
                                                <div class="info-value">
                                                    <?= $producto['actualizado_en'] ? date('d/m/Y H:i', strtotime($producto['actualizado_en'])) : 'No actualizado' ?>
                                                </div>
                                                
                                                <div class="info-label">Estado:</div>
                                                <div class="info-value">
                                                    <span class="badge badge-<?= $statusClass ?>">
                                                        <?= $statusText ?>
                                                    </span>
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
        });
        
        // Cambiar imagen principal
        function changeMainImage(src) {
            document.getElementById('mainImage').src = src;
        }
    </script>
</body>
</html>
