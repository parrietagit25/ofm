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
    $mensaje = 'Debes tener un comercio registrado para editar productos. Contacta al administrador.';
    $tipoMensaje = 'warning';
    $comercio_id = null;
} else {
    $comercio_id = $comercios[0]['id'];
}

// Obtener ID del producto a editar
$producto_id = $_GET['id'] ?? null;

if (!$producto_id) {
    header('Location: index.php');
    exit;
}

// Categorías disponibles
$categorias = ['Electrónicos', 'Ropa', 'Hogar', 'Deportes', 'Belleza', 'Juguetes', 'Libros', 'Otros'];

// Procesar formulario
$mensaje = '';
$tipoMensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $comercio_id) {
    require_once __DIR__ . '/../../controllers/productoController.php';
    $productoController = new ProductoController($pdo);
    
    $nombre = trim($_POST['nombre'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $precio = floatval($_POST['precio'] ?? 0);
    $precio_original = floatval($_POST['precio_original'] ?? 0);
    $stock = intval($_POST['stock'] ?? 0);
    $categoria = trim($_POST['categoria'] ?? '');
    
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
        // Actualizar producto
        $datos = [
            'id' => $producto_id,
            'comercio_id' => $comercio_id,
            'nombre' => $nombre,
            'descripcion' => $descripcion,
            'precio' => $precio,
            'precio_anterior' => $precio_original > 0 ? $precio_original : null,
            'stock' => $stock,
            'categoria' => $categoria,
            'marca' => null,
            'codigo_producto' => null,
            'peso' => null,
            'dimensiones' => null,
            'status' => 'activo',
            'destacado' => 0
        ];
        
        // Obtener imágenes del formulario
        $imagenes = $_FILES['imagenes'] ?? [];
        
        $resultado = $productoController->actualizar($datos, $imagenes);
        
        if ($resultado['success']) {
            $mensaje = 'Producto actualizado exitosamente';
            $tipoMensaje = 'success';
        } else {
            $mensaje = 'Error al actualizar el producto: ' . ($resultado['message'] ?? 'Error desconocido');
            $tipoMensaje = 'danger';
        }
    }
}

// Obtener datos del producto
require_once __DIR__ . '/../../models/Producto.php';
$productoModel = new Producto($pdo);
$producto = $productoModel->obtenerPorId($producto_id);

if (!$producto) {
    header('Location: index.php');
    exit;
}

// Verificar que el producto pertenece al socio
if ($producto['comercio_id'] != $comercio_id) {
    header('Location: index.php');
    exit;
}

// Obtener imágenes del producto
$imagenesProducto = $productoModel->obtenerImagenes($producto_id);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Editar Producto - Socio OFM</title>

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
        .main-content {
            margin-left: 250px;
            padding: 20px;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .card-header {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            border-radius: 15px 15px 0 0 !important;
            padding: 20px;
        }
        .btn-primary {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            border: none;
            border-radius: 25px;
            padding: 12px 30px;
            font-weight: 600;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.4);
        }
        .form-control, .form-select {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 12px 15px;
            transition: all 0.3s;
        }
        .form-control:focus, .form-select:focus {
            border-color: #28a745;
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
        }
        .image-preview {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 10px;
        }
        .image-preview img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 8px;
            border: 2px solid #e9ecef;
        }
        .image-preview .remove-image {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #dc3545;
            color: white;
            border: none;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 12px;
            cursor: pointer;
        }
        .image-preview .image-container {
            position: relative;
        }
        .drag-drop-area {
            border: 2px dashed #28a745;
            border-radius: 10px;
            padding: 40px;
            text-align: center;
            background: #f8f9fa;
            transition: all 0.3s;
            cursor: pointer;
        }
        .drag-drop-area:hover {
            background: #e9ecef;
            border-color: #20c997;
        }
        .drag-drop-area.dragover {
            background: #d4edda;
            border-color: #28a745;
        }
        .existing-images {
            margin-bottom: 20px;
        }
        .existing-images img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
            margin: 5px;
            border: 2px solid #e9ecef;
        }
        .existing-images .image-item {
            position: relative;
            display: inline-block;
        }
        .existing-images .remove-existing {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #dc3545;
            color: white;
            border: none;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            font-size: 10px;
            cursor: pointer;
        }
    </style>
</head>
<body>

<div class="d-flex">
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="p-3">
            <h4 class="text-white text-center mb-4">
                <i class="fas fa-store"></i> Socio OFM
            </h4>
            <nav class="nav flex-column">
                <a class="nav-link" href="../dashboard.php">
                    <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                </a>
                <a class="nav-link active" href="index.php">
                    <i class="fas fa-box me-2"></i> Productos
                </a>
                <a class="nav-link" href="../ventas/index.php">
                    <i class="fas fa-shopping-cart me-2"></i> Ventas
                </a>
                <a class="nav-link" href="../verificar-qr/index.php">
                    <i class="fas fa-qrcode me-2"></i> Verificar QR
                </a>
                <a class="nav-link" href="?logout=1">
                    <i class="fas fa-sign-out-alt me-2"></i> Cerrar Sesión
                </a>
            </nav>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="mb-0">
                                <i class="fas fa-edit me-2"></i>
                                Editar Producto: <?= htmlspecialchars($producto['nombre']) ?>
                            </h3>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($mensaje)): ?>
                                <div class="alert alert-<?= $tipoMensaje ?> alert-dismissible fade show" role="alert">
                                    <?= htmlspecialchars($mensaje) ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            <?php endif; ?>

                            <form method="POST" enctype="multipart/form-data" id="productoForm">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="mb-3">
                                            <label for="nombre" class="form-label">
                                                <i class="fas fa-tag me-2"></i>Nombre del Producto *
                                            </label>
                                            <input type="text" class="form-control" id="nombre" name="nombre" 
                                                   value="<?= htmlspecialchars($producto['nombre']) ?>" required>
                                        </div>

                                        <div class="mb-3">
                                            <label for="descripcion" class="form-label">
                                                <i class="fas fa-align-left me-2"></i>Descripción
                                            </label>
                                            <textarea class="form-control" id="descripcion" name="descripcion" rows="4"
                                                      placeholder="Describe tu producto..."><?= htmlspecialchars($producto['descripcion'] ?? '') ?></textarea>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="precio" class="form-label">
                                                        <i class="fas fa-dollar-sign me-2"></i>Precio *
                                                    </label>
                                                    <input type="number" class="form-control" id="precio" name="precio" 
                                                           value="<?= htmlspecialchars($producto['precio']) ?>" 
                                                           step="0.01" min="0" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="precio_original" class="form-label">
                                                        <i class="fas fa-tags me-2"></i>Precio Original
                                                    </label>
                                                    <input type="number" class="form-control" id="precio_original" name="precio_original" 
                                                           value="<?= htmlspecialchars($producto['precio_anterior'] ?? '') ?>" 
                                                           step="0.01" min="0" placeholder="Para mostrar descuento">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="stock" class="form-label">
                                                        <i class="fas fa-boxes me-2"></i>Stock *
                                                    </label>
                                                    <input type="number" class="form-control" id="stock" name="stock" 
                                                           value="<?= htmlspecialchars($producto['stock']) ?>" 
                                                           min="0" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="categoria" class="form-label">
                                                        <i class="fas fa-folder me-2"></i>Categoría
                                                    </label>
                                                    <select class="form-select" id="categoria" name="categoria">
                                                        <option value="">Selecciona una categoría</option>
                                                        <?php foreach ($categorias as $cat): ?>
                                                            <option value="<?= $cat ?>" <?= ($producto['categoria'] ?? '') === $cat ? 'selected' : '' ?>>
                                                                <?= $cat ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <!-- Imágenes existentes -->
                                        <?php if (!empty($imagenesProducto)): ?>
                                        <div class="mb-3">
                                            <label class="form-label">
                                                <i class="fas fa-images me-2"></i>Imágenes Actuales
                                            </label>
                                            <div class="existing-images">
                                                <?php foreach ($imagenesProducto as $imagen): ?>
                                                <div class="image-item">
                                                    <img src="<?= htmlspecialchars($imagen['ruta']) ?>" 
                                                         alt="<?= htmlspecialchars($imagen['nombre_archivo']) ?>"
                                                         title="<?= htmlspecialchars($imagen['nombre_archivo']) ?>">
                                                    <button type="button" class="remove-existing" 
                                                            onclick="removeExistingImage(<?= $imagen['id'] ?>)">×</button>
                                                </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                        <?php endif; ?>

                                        <div class="mb-3">
                                            <label class="form-label">
                                                <i class="fas fa-plus-circle me-2"></i>Agregar Nuevas Imágenes
                                            </label>
                                            <div class="drag-drop-area" id="dragDropArea">
                                                <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                                                <p class="mb-2">Arrastra y suelta imágenes aquí</p>
                                                <p class="text-muted small">o haz clic para seleccionar</p>
                                                <input type="file" id="imagenes" name="imagenes[]" multiple accept="image/*" style="display: none;">
                                            </div>
                                            <div class="image-preview" id="imagePreview"></div>
                                            <small class="text-muted">
                                                Puedes seleccionar múltiples imágenes. La primera será la imagen principal.
                                            </small>
                                        </div>
                                    </div>
                                </div>

                                <div class="text-center mt-4">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-save me-2"></i>Actualizar Producto
                                    </button>
                                    <a href="index.php" class="btn btn-secondary btn-lg ms-2">
                                        <i class="fas fa-arrow-left me-2"></i>Volver
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
let selectedFiles = [];

// Drag and Drop
const dragDropArea = document.getElementById('dragDropArea');
const imagePreview = document.getElementById('imagePreview');
const fileInput = document.getElementById('imagenes');

dragDropArea.addEventListener('click', () => fileInput.click());

dragDropArea.addEventListener('dragover', (e) => {
    e.preventDefault();
    dragDropArea.classList.add('dragover');
});

dragDropArea.addEventListener('dragleave', () => {
    dragDropArea.classList.remove('dragover');
});

dragDropArea.addEventListener('drop', (e) => {
    e.preventDefault();
    dragDropArea.classList.remove('dragover');
    
    const files = Array.from(e.dataTransfer.files);
    handleFiles(files);
});

fileInput.addEventListener('change', (e) => {
    const files = Array.from(e.target.files);
    handleFiles(files);
});

function handleFiles(files) {
    files.forEach(file => {
        if (file.type.startsWith('image/')) {
            selectedFiles.push(file);
        }
    });
    updateImagePreview();
    updateFileInput();
}

function updateImagePreview() {
    imagePreview.innerHTML = '';
    
    selectedFiles.forEach((file, index) => {
        const reader = new FileReader();
        reader.onload = (e) => {
            const imageContainer = document.createElement('div');
            imageContainer.className = 'image-container';
            
            const img = document.createElement('img');
            img.src = e.target.result;
            img.alt = file.name;
            
            const removeBtn = document.createElement('button');
            removeBtn.className = 'remove-image';
            removeBtn.innerHTML = '×';
            removeBtn.onclick = () => removeImage(index);
            
            imageContainer.appendChild(img);
            imageContainer.appendChild(removeBtn);
            imagePreview.appendChild(imageContainer);
        };
        reader.readAsDataURL(file);
    });
}

function removeImage(index) {
    selectedFiles.splice(index, 1);
    updateImagePreview();
    updateFileInput();
}

function updateFileInput() {
    const dt = new DataTransfer();
    selectedFiles.forEach(file => dt.items.add(file));
    fileInput.files = dt.files;
}

function removeExistingImage(imageId) {
    if (confirm('¿Estás seguro de que quieres eliminar esta imagen?')) {
        // Aquí podrías implementar la eliminación de imágenes existentes
        // Por ahora solo ocultamos la imagen
        const imageItem = event.target.closest('.image-item');
        if (imageItem) {
            imageItem.style.display = 'none';
        }
    }
}

// Form submission
$('#productoForm').submit(function(e) {
    updateFileInput(); // Sincronizar antes de enviar
});
</script>

</body>
</html>
