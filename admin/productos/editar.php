<?php
/**
 * Página para editar productos - Panel Administrativo OFM
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

// Obtener comercios para el formulario
$comercios = [];
try {
    $stmt = $pdo->query("SELECT id, nombre_comercio FROM comercios ORDER BY nombre_comercio");
    $comercios = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $comercios = [];
}

// Obtener categorías existentes
$categorias = [];
try {
    $stmt = $pdo->query("SELECT DISTINCT categoria FROM productos WHERE categoria IS NOT NULL AND categoria != '' ORDER BY categoria");
    $categorias = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (Exception $e) {
    $categorias = [];
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

// Obtener mensajes de error si existen
$error = $_GET['error'] ?? '';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Producto - Panel Administrativo OFM</title>
    
    <!-- AdminLTE CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/css/adminlte.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        .image-preview {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 8px;
            border: 2px dashed #dee2e6;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f8f9fa;
            margin: 10px;
        }
        .image-preview img {
            max-width: 100%;
            max-height: 100%;
            border-radius: 6px;
        }
        .image-preview.empty {
            border-color: #007bff;
            color: #007bff;
        }
        .image-preview.empty:hover {
            border-color: #0056b3;
            background: #e7f3ff;
        }
        .remove-image {
            position: absolute;
            top: 5px;
            right: 5px;
            background: rgba(220, 53, 69, 0.9);
            color: white;
            border: none;
            border-radius: 50%;
            width: 25px;
            height: 25px;
            font-size: 12px;
            cursor: pointer;
        }
        .image-container {
            position: relative;
            display: inline-block;
        }
        .existing-images {
            margin-bottom: 20px;
        }
        .existing-image {
            position: relative;
            display: inline-block;
            margin: 10px;
        }
        .existing-image img {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 8px;
            border: 2px solid #dee2e6;
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
                            <h1>Editar Producto</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="../dashboard.php">Inicio</a></li>
                                <li class="breadcrumb-item"><a href="index.php">Productos</a></li>
                                <li class="breadcrumb-item active">Editar</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </section>
            
            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <!-- Mensajes de error -->
                    <?php if ($error): ?>
                        <div class="alert alert-danger alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Editar: <?= htmlspecialchars($producto['nombre']) ?></h3>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="/ofm/controllers/productoController.php" enctype="multipart/form-data" id="productoForm">
                                <input type="hidden" name="action" value="actualizar">
                                <input type="hidden" name="id" value="<?= $producto['id'] ?>">
                                
                                <div class="row">
                                    <!-- Columna izquierda -->
                                    <div class="col-md-8">
                                        <!-- Información básica -->
                                        <div class="form-group">
                                            <label for="nombre">Nombre del Producto *</label>
                                            <input type="text" class="form-control" id="nombre" name="nombre" required 
                                                   value="<?= htmlspecialchars($producto['nombre']) ?>"
                                                   placeholder="Ej: Smartphone Samsung Galaxy S21">
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="descripcion">Descripción *</label>
                                            <textarea class="form-control" id="descripcion" name="descripcion" rows="4" required
                                                      placeholder="Describe las características principales del producto..."><?= htmlspecialchars($producto['descripcion'] ?? '') ?></textarea>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="precio">Precio *</label>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">$</span>
                                                        </div>
                                                        <input type="number" class="form-control" id="precio" name="precio" 
                                                               step="0.01" min="0" required 
                                                               value="<?= $producto['precio'] ?>"
                                                               placeholder="0.00">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="precio_oferta">Precio de Oferta</label>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">$</span>
                                                        </div>
                                                        <input type="number" class="form-control" id="precio_oferta" name="precio_oferta" 
                                                               step="0.01" min="0" 
                                                               value="<?= $producto['precio_oferta'] ?? '' ?>"
                                                               placeholder="0.00">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="stock">Stock</label>
                                                    <input type="number" class="form-control" id="stock" name="stock" 
                                                           min="0" value="<?= $producto['stock'] ?? 0 ?>" placeholder="0">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="categoria">Categoría</label>
                                                    <input type="text" class="form-control" id="categoria" name="categoria" 
                                                           list="categorias" 
                                                           value="<?= htmlspecialchars($producto['categoria'] ?? '') ?>"
                                                           placeholder="Ej: Electrónicos">
                                                    <datalist id="categorias">
                                                        <?php foreach ($categorias as $categoria): ?>
                                                            <option value="<?= htmlspecialchars($categoria) ?>">
                                                        <?php endforeach; ?>
                                                    </datalist>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="marca">Marca</label>
                                                    <input type="text" class="form-control" id="marca" name="marca" 
                                                           value="<?= htmlspecialchars($producto['marca'] ?? '') ?>"
                                                           placeholder="Ej: Samsung">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="codigo_producto">Código del Producto</label>
                                                    <input type="text" class="form-control" id="codigo_producto" name="codigo_producto" 
                                                           value="<?= htmlspecialchars($producto['codigo_producto'] ?? '') ?>"
                                                           placeholder="Ej: SM-G991B">
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="peso">Peso (kg)</label>
                                                    <input type="number" class="form-control" id="peso" name="peso" 
                                                           step="0.01" min="0" 
                                                           value="<?= $producto['peso'] ?? '' ?>"
                                                           placeholder="0.00">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="dimensiones">Dimensiones</label>
                                                    <input type="text" class="form-control" id="dimensiones" name="dimensiones" 
                                                           value="<?= htmlspecialchars($producto['dimensiones'] ?? '') ?>"
                                                           placeholder="Ej: 15.6 x 10.2 x 0.8 cm">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Columna derecha -->
                                    <div class="col-md-4">
                                        <!-- Comercio -->
                                        <div class="form-group">
                                            <label for="comercio_id">Comercio</label>
                                            <select class="form-control" id="comercio_id" name="comercio_id">
                                                <option value="">Producto OFM</option>
                                                <?php foreach ($comercios as $comercio): ?>
                                                    <option value="<?= $comercio['id'] ?>" 
                                                            <?= $producto['comercio_id'] == $comercio['id'] ? 'selected' : '' ?>>
                                                        <?= htmlspecialchars($comercio['nombre_comercio']) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        
                                        <!-- Estado -->
                                        <div class="form-group">
                                            <label for="status">Estado</label>
                                            <select class="form-control" id="status" name="status">
                                                <option value="activo" <?= $producto['status'] === 'activo' ? 'selected' : '' ?>>Activo</option>
                                                <option value="inactivo" <?= $producto['status'] === 'inactivo' ? 'selected' : '' ?>>Inactivo</option>
                                                <option value="agotado" <?= $producto['status'] === 'agotado' ? 'selected' : '' ?>>Agotado</option>
                                            </select>
                                        </div>
                                        
                                        <!-- Destacado -->
                                        <div class="form-group">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input" id="destacado" name="destacado" value="1"
                                                       <?= $producto['destacado'] ? 'checked' : '' ?>>
                                                <label class="custom-control-label" for="destacado">
                                                    Producto Destacado
                                                </label>
                                            </div>
                                        </div>
                                        
                                        <!-- Imágenes existentes -->
                                        <?php if (!empty($imagenes)): ?>
                                            <div class="form-group">
                                                <label>Imágenes Existentes</label>
                                                <div class="existing-images">
                                                    <?php foreach ($imagenes as $imagen): ?>
                                                        <div class="existing-image">
                                                            <img src="/ofm/public/uploads/productos/<?= htmlspecialchars($imagen['nombre_archivo']) ?>" 
                                                                 alt="Imagen existente">
                                                            <button type="button" class="remove-image" 
                                                                    onclick="eliminarImagen(<?= $imagen['id'] ?>)"
                                                                    title="Eliminar imagen">×</button>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <!-- Agregar nuevas imágenes -->
                                        <div class="form-group">
                                            <label>Agregar Nuevas Imágenes</label>
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input" id="imagenes" name="imagenes[]" 
                                                       multiple accept="image/*">
                                                <label class="custom-file-label" for="imagenes">Seleccionar imágenes...</label>
                                            </div>
                                            <small class="form-text text-muted">
                                                Puedes seleccionar múltiples imágenes. Formatos: JPG, PNG, GIF. Máximo 5 imágenes.
                                            </small>
                                        </div>
                                        
                                        <!-- Vista previa de nuevas imágenes -->
                                        <div id="imagePreviewContainer" class="mt-3">
                                            <div class="image-preview empty" id="imagePreviewTemplate">
                                                <i class="fas fa-plus fa-2x"></i>
                                                <br>
                                                <small>Agregar imagen</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Botones de acción -->
                                <div class="form-group text-center mt-4">
                                    <button type="submit" class="btn btn-warning btn-lg">
                                        <i class="fas fa-save"></i> Actualizar Producto
                                    </button>
                                    <a href="ver.php?id=<?= $producto['id'] ?>" class="btn btn-info btn-lg ml-2">
                                        <i class="fas fa-eye"></i> Ver Producto
                                    </a>
                                    <a href="index.php" class="btn btn-secondary btn-lg ml-2">
                                        <i class="fas fa-times"></i> Cancelar
                                    </a>
                                </div>
                            </form>
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
            // Manejo de archivos de imagen
            $('#imagenes').change(function() {
                const files = this.files;
                const container = $('#imagePreviewContainer');
                
                // Limpiar contenedor
                container.empty();
                
                if (files.length > 0) {
                    // Mostrar vista previa de cada imagen
                    for (let i = 0; i < Math.min(files.length, 5); i++) {
                        const file = files[i];
                        const reader = new FileReader();
                        
                        reader.onload = function(e) {
                            const imageContainer = $('<div class="image-container">');
                            const imagePreview = $('<div class="image-preview">');
                            const img = $('<img src="' + e.target.result + '" alt="Vista previa">');
                            const removeBtn = $('<button type="button" class="remove-image" title="Eliminar imagen">×</button>');
                            
                            imagePreview.append(img);
                            imageContainer.append(imagePreview);
                            imageContainer.append(removeBtn);
                            container.append(imageContainer);
                            
                            // Evento para eliminar imagen
                            removeBtn.click(function() {
                                imageContainer.remove();
                                updateFileInput();
                            });
                        };
                        
                        reader.readAsDataURL(file);
                    }
                } else {
                    // Mostrar template vacío
                    container.append($('#imagePreviewTemplate').clone());
                }
            });
            
            // Actualizar input de archivos cuando se eliminan imágenes
            function updateFileInput() {
                // Esta función se puede implementar para sincronizar el input de archivos
                // con las imágenes mostradas en la vista previa
            }
            
            // Validación del formulario
            $('#productoForm').submit(function(e) {
                const nombre = $('#nombre').val().trim();
                const descripcion = $('#descripcion').val().trim();
                const precio = parseFloat($('#precio').val());
                const precioOferta = parseFloat($('#precio_oferta').val()) || 0;
                
                if (nombre.length < 3) {
                    alert('El nombre del producto debe tener al menos 3 caracteres');
                    e.preventDefault();
                    return false;
                }
                
                if (descripcion.length < 10) {
                    alert('La descripción debe tener al menos 10 caracteres');
                    e.preventDefault();
                    return false;
                }
                
                if (precio <= 0) {
                    alert('El precio debe ser mayor a 0');
                    e.preventDefault();
                    return false;
                }
                
                if (precioOferta > 0 && precioOferta >= precio) {
                    alert('El precio de oferta debe ser menor al precio normal');
                    e.preventDefault();
                    return false;
                }
                
                // Mostrar indicador de carga
                $(this).find('button[type="submit"]').html('<i class="fas fa-spinner fa-spin"></i> Actualizando...').prop('disabled', true);
            });
            
            // Actualizar etiqueta del input de archivos
            $('.custom-file-input').on('change', function() {
                const fileName = $(this).val().split('\\').pop();
                $(this).next('.custom-file-label').html(fileName || 'Seleccionar imágenes...');
            });
        });
        
        // Eliminar imagen existente
        function eliminarImagen(imagenId) {
            if (confirm('¿Estás seguro de que quieres eliminar esta imagen?')) {
                $.post('/ofm/controllers/productoController.php', {
                    action: 'eliminar_imagen',
                    imagen_id: imagenId
                }, function(response) {
                    if (response.success) {
                        // Recargar la página para mostrar los cambios
                        location.reload();
                    } else {
                        alert('Error: ' + response.message);
                    }
                }, 'json').fail(function() {
                    alert('Error al eliminar la imagen');
                });
            }
        }
    </script>
</body>
</html>
