<?php
require_once __DIR__ . '/../../controllers/loginController.php';

// Verificar que el usuario esté autenticado y sea socio
$loginController->verificarAcceso('socio');

// Obtener información del usuario actual
$usuario = $loginController->obtenerUsuarioActual();

// Verificar expiración de sesión
$loginController->verificarExpiracionSesion();

// Obtener comercio del socio
require_once __DIR__ . '/../../models/Comercio.php';
$comercioModel = new Comercio($pdo);
$comercios = $comercioModel->obtenerPorUsuarioSocio($usuario['id']);

if (empty($comercios)) {
    header('Location: ../dashboard.php');
    exit;
}

$comercio_id = $comercios[0]['id'];

// Verificar que se proporcione un ID de producto
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$producto_id = intval($_GET['id']);

// Obtener el producto
require_once __DIR__ . '/../../models/Producto.php';
$productoModel = new Producto($pdo);
$producto = $productoModel->obtenerPorId($producto_id);

// Verificar que el producto exista y pertenezca al socio
if (!$producto || $producto['comercio_id'] != $comercio_id) {
    header('Location: index.php');
    exit;
}

// Categorías disponibles
$categorias = ['Electrónicos', 'Ropa', 'Hogar', 'Deportes', 'Belleza', 'Juguetes', 'Libros', 'Otros'];

// Procesar formulario
$mensaje = '';
$tipoMensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $precio = floatval($_POST['precio'] ?? 0);
    $precio_anterior = floatval($_POST['precio_anterior'] ?? 0);
    $stock = intval($_POST['stock'] ?? 0);
    $categoria = trim($_POST['categoria'] ?? '');
    $status = trim($_POST['status'] ?? 'activo');
    
    // Validaciones
    if (empty($nombre)) {
        $mensaje = 'El nombre del producto es obligatorio';
        $tipoMensaje = 'danger';
    } elseif ($precio <= 0) {
        $mensaje = 'El precio debe ser mayor a 0';
        $tipoMensaje = 'danger';
    } elseif ($stock < 0) {
        $mensaje = 'El stock no puede ser negativo';
        $tipoMensaje = 'danger';
    } else {
        // Procesar imagen si se subió
        $imagen = '';
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../../uploads/productos/';
            
            // Crear directorio si no existe
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            $fileExtension = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            
            if (in_array($fileExtension, $allowedExtensions)) {
                $fileName = uniqid() . '.' . $fileExtension;
                $uploadPath = $uploadDir . $fileName;
                
                if (move_uploaded_file($_FILES['imagen']['tmp_name'], $uploadPath)) {
                    $imagen = '/ofm/uploads/productos/' . $fileName;
                }
            }
        }
        
        // Actualizar producto
        $datos = [
            'nombre' => $nombre,
            'descripcion' => $descripcion,
            'precio' => $precio,
            'precio_anterior' => $precio_anterior > 0 ? $precio_anterior : null,
            'stock' => $stock,
            'categoria' => $categoria,
            'status' => $status
        ];
        
        $resultado = $productoModel->actualizar($producto_id, $datos);
        
        if ($resultado['success']) {
            // Si hay nueva imagen, actualizarla en la tabla producto_imagenes
            if (!empty($imagen)) {
                try {
                    // Eliminar imagen anterior si existe
                    $stmt = $pdo->prepare("DELETE FROM producto_imagenes WHERE producto_id = ? AND principal = 1");
                    $stmt->execute([$producto_id]);
                    
                    // Insertar nueva imagen
                    $stmt = $pdo->prepare("
                        INSERT INTO producto_imagenes (producto_id, nombre_archivo, ruta, tipo, orden, principal) 
                        VALUES (?, ?, ?, 'imagen', 1, 1)
                    ");
                    $stmt->execute([
                        $producto_id, 
                        basename($imagen), 
                        $imagen
                    ]);
                } catch (Exception $e) {
                    error_log("Error al actualizar imagen del producto: " . $e->getMessage());
                }
            }
            
            $mensaje = 'Producto actualizado exitosamente';
            $tipoMensaje = 'success';
            
            // Recargar datos del producto
            $producto = $productoModel->obtenerPorId($producto_id);
        } else {
            $mensaje = $resultado['message'];
            $tipoMensaje = 'danger';
        }
    }
}

// Obtener imagen actual del producto
$imagen_actual = null;
try {
    $stmt = $pdo->prepare("
        SELECT ruta FROM producto_imagenes 
        WHERE producto_id = ? AND principal = 1 
        ORDER BY orden ASC 
        LIMIT 1
    ");
    $stmt->execute([$producto_id]);
    $imagen_data = $stmt->fetch(PDO::FETCH_ASSOC);
    $imagen_actual = $imagen_data ? $imagen_data['ruta'] : null;
} catch (Exception $e) {
    $imagen_actual = null;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Editar Producto - OFM Socio</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
        .form-control:focus {
            border-color: #28a745;
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
        }
        .btn-success {
            background-color: #28a745;
            border-color: #28a745;
        }
        .btn-success:hover {
            background-color: #218838;
            border-color: #1e7e34;
        }
        .image-preview {
            max-width: 200px;
            max-height: 200px;
            border-radius: 8px;
            border: 2px dashed #dee2e6;
            padding: 10px;
            text-align: center;
        }
        .image-preview img {
            max-width: 100%;
            max-height: 100%;
            border-radius: 4px;
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
                        <span class="navbar-brand">Editar Producto</span>
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

                <!-- Breadcrumb -->
                <nav aria-label="breadcrumb" class="mb-4">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="index.php">Mis Productos</a></li>
                        <li class="breadcrumb-item active">Editar Producto</li>
                    </ol>
                </nav>

                <!-- Mensaje de resultado -->
                <?php if ($mensaje): ?>
                    <div class="alert alert-<?= $tipoMensaje ?> alert-dismissible fade show" role="alert">
                        <?= htmlspecialchars($mensaje) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Formulario -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-edit me-2"></i>Editar Producto: <?= htmlspecialchars($producto['nombre']) ?>
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data" id="productoForm">
                            <div class="row">
                                <div class="col-md-8">
                                    <!-- Información básica -->
                                    <div class="mb-3">
                                        <label for="nombre" class="form-label">Nombre del Producto *</label>
                                        <input type="text" class="form-control" id="nombre" name="nombre" 
                                               value="<?= htmlspecialchars($producto['nombre']) ?>" required>
                                        <div class="form-text">Nombre descriptivo del producto</div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="descripcion" class="form-label">Descripción</label>
                                        <textarea class="form-control" id="descripcion" name="descripcion" rows="4"
                                                  placeholder="Describe las características del producto..."><?= htmlspecialchars($producto['descripcion']) ?></textarea>
                                        <div class="form-text">Descripción detallada del producto</div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="categoria" class="form-label">Categoría *</label>
                                                <select class="form-select" id="categoria" name="categoria" required>
                                                    <option value="">Seleccionar categoría</option>
                                                    <?php foreach ($categorias as $cat): ?>
                                                        <option value="<?= $cat ?>" <?= $producto['categoria'] === $cat ? 'selected' : '' ?>>
                                                            <?= $cat ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="stock" class="form-label">Stock *</label>
                                                <input type="number" class="form-control" id="stock" name="stock" 
                                                       value="<?= htmlspecialchars($producto['stock']) ?>" min="0" required>
                                                <div class="form-text">Cantidad disponible</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="precio" class="form-label">Precio de Venta *</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">$</span>
                                                    <input type="number" class="form-control" id="precio" name="precio" 
                                                           value="<?= htmlspecialchars($producto['precio']) ?>" 
                                                           step="0.01" min="0" required>
                                                </div>
                                                <div class="form-text">Precio actual de venta</div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="precio_anterior" class="form-label">Precio Original</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">$</span>
                                                    <input type="number" class="form-control" id="precio_anterior" name="precio_anterior" 
                                                           value="<?= htmlspecialchars($producto['precio_anterior'] ?? '') ?>" 
                                                           step="0.01" min="0">
                                                </div>
                                                <div class="form-text">Precio antes del descuento (opcional)</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="status" class="form-label">Estado del Producto</label>
                                        <select class="form-select" id="status" name="status">
                                            <option value="activo" <?= $producto['status'] === 'activo' ? 'selected' : '' ?>>Activo</option>
                                            <option value="inactivo" <?= $producto['status'] === 'inactivo' ? 'selected' : '' ?>>Inactivo</option>
                                            <option value="agotado" <?= $producto['status'] === 'agotado' ? 'selected' : '' ?>>Agotado</option>
                                            <option value="en_oferta" <?= $producto['status'] === 'en_oferta' ? 'selected' : '' ?>>En Oferta</option>
                                        </select>
                                        <div class="form-text">Estado actual del producto</div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <!-- Imagen del producto -->
                                    <div class="mb-3">
                                        <label for="imagen" class="form-label">Nueva Imagen del Producto</label>
                                        <input type="file" class="form-control" id="imagen" name="imagen" 
                                               accept="image/*" onchange="previewImage(this)">
                                        <div class="form-text">Formatos: JPG, PNG, GIF, WebP</div>
                                    </div>

                                    <!-- Vista previa de la imagen -->
                                    <div class="image-preview" id="imagePreview">
                                        <?php if ($imagen_actual): ?>
                                            <img src="<?= htmlspecialchars($imagen_actual) ?>" alt="Imagen actual del producto">
                                        <?php else: ?>
                                            <i class="fas fa-image fa-3x text-muted"></i>
                                            <p class="text-muted mt-2">Sin imagen</p>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Información del producto -->
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h6 class="card-title">
                                                <i class="fas fa-info-circle me-2"></i>Información del Producto
                                            </h6>
                                            <ul class="list-unstyled small">
                                                <li class="mb-2">
                                                    <strong>ID:</strong> <?= $producto['id'] ?>
                                                </li>
                                                <li class="mb-2">
                                                    <strong>Creado:</strong> <?= date('d/m/Y', strtotime($producto['creado_en'])) ?>
                                                </li>
                                                <li class="mb-2">
                                                    <strong>Última actualización:</strong> 
                                                    <?= $producto['actualizado_en'] ? date('d/m/Y', strtotime($producto['actualizado_en'])) : 'Nunca' ?>
                                                </li>
                                                <li>
                                                    <strong>Comercio ID:</strong> <?= $producto['comercio_id'] ?>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Botones de acción -->
                            <div class="d-flex justify-content-between mt-4">
                                <a href="index.php" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-2"></i>Volver
                                </a>
                                <div>
                                    <button type="reset" class="btn btn-outline-secondary me-2">
                                        <i class="fas fa-undo me-2"></i>Restaurar
                                    </button>
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-save me-2"></i>Actualizar Producto
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Vista previa de imagen
        function previewImage(input) {
            const preview = document.getElementById('imagePreview');
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    preview.innerHTML = `<img src="${e.target.result}" alt="Vista previa">`;
                };
                
                reader.readAsDataURL(input.files[0]);
            } else {
                // Restaurar imagen original si existe
                <?php if ($imagen_actual): ?>
                    preview.innerHTML = `<img src="<?= htmlspecialchars($imagen_actual) ?>" alt="Imagen actual del producto">`;
                <?php else: ?>
                    preview.innerHTML = `
                        <i class="fas fa-image fa-3x text-muted"></i>
                        <p class="text-muted mt-2">Sin imagen</p>
                    `;
                <?php endif; ?>
            }
        }

        // Validación del formulario
        document.getElementById('productoForm').addEventListener('submit', function(e) {
            const precio = parseFloat(document.getElementById('precio').value);
            const precioAnterior = parseFloat(document.getElementById('precio_anterior').value);
            
            if (precioAnterior > 0 && precio >= precioAnterior) {
                e.preventDefault();
                alert('El precio de venta debe ser menor al precio original para aplicar descuento');
                return false;
            }
        });
    </script>
</body>
</html>
