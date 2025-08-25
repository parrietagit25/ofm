<?php
/**
 * Página para crear nuevos productos - Panel Administrativo OFM
 */

require_once __DIR__ . '/../../includes/session_helper.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../controllers/loginController_simple.php';
require_once __DIR__ . '/../../controllers/productoController.php';

// Verificar acceso de administrador
$loginController = new LoginControllerSimple($pdo);
$loginController->verificarAcceso('admin');

// Procesar formulario cuando se envía
$mensaje = '';
$tipoMensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Debugging: Log de lo que se recibe
    error_log("DEBUG: Formulario POST recibido en crear.php");
    error_log("DEBUG: POST data: " . print_r($_POST, true));
    error_log("DEBUG: FILES data: " . print_r($_FILES, true));
    
    try {
        // Validar datos del formulario
        $nombre = trim($_POST['nombre'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');
        $precio = floatval($_POST['precio'] ?? 0);
        $precioOferta = !empty($_POST['precio_oferta']) ? floatval($_POST['precio_oferta']) : null;
        $stock = intval($_POST['stock'] ?? 0);
        $categoria = trim($_POST['categoria'] ?? '');
        $marca = trim($_POST['marca'] ?? '');
        $comercioId = intval($_POST['comercio_id'] ?? 0);
        $codigoProducto = trim($_POST['codigo_producto'] ?? '');
        $peso = !empty($_POST['peso']) ? floatval($_POST['peso']) : null;
        $dimensiones = trim($_POST['dimensiones'] ?? '');
        $destacado = isset($_POST['destacado']) ? 1 : 0;
        
        // Validaciones básicas
        if (empty($nombre) || empty($descripcion) || $precio <= 0) {
            throw new Exception('Nombre, descripción y precio son obligatorios');
        }
        
        if ($precioOferta && $precioOferta >= $precio) {
            throw new Exception('El precio de oferta debe ser menor al precio normal');
        }
        
        // Preparar datos del producto
        $datosProducto = [
            'nombre' => $nombre,
            'descripcion' => $descripcion,
            'precio' => $precio,
            'precio_oferta' => $precioOferta,
            'stock' => $stock,
            'categoria' => $categoria,
            'marca' => $marca,
            'comercio_id' => $comercioId,
            'codigo_producto' => $codigoProducto,
            'peso' => $peso,
            'dimensiones' => $dimensiones,
            'destacado' => $destacado,
            'status' => 'activo'
        ];
        
        // Crear producto
        $productoController = new ProductoController($pdo);
        $resultado = $productoController->crear($datosProducto, $_FILES['imagenes'] ?? []);
        
        if ($resultado['success']) {
            $mensaje = 'Producto creado exitosamente';
            $tipoMensaje = 'success';
            
            // Limpiar formulario
            $_POST = [];
        } else {
            throw new Exception($resultado['message']);
        }
        
    } catch (Exception $e) {
        $mensaje = 'Error: ' . $e->getMessage();
        $tipoMensaje = 'danger';
    }
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

// Obtener mensajes de error si existen
$error = $_GET['error'] ?? '';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Producto - Panel Administrativo OFM</title>
    
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
                            <h1>Crear Nuevo Producto</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="../dashboard.php">Inicio</a></li>
                                <li class="breadcrumb-item"><a href="index.php">Productos</a></li>
                                <li class="breadcrumb-item active">Crear</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </section>
            
            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <!-- Mensajes de éxito/error -->
                    <?php if ($mensaje): ?>
                        <div class="alert alert-<?= $tipoMensaje ?> alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <i class="fas fa-<?= $tipoMensaje === 'success' ? 'check-circle' : 'exclamation-circle' ?>"></i> 
                            <?= htmlspecialchars($mensaje) ?>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Mensajes de error -->
                    <?php if ($error): ?>
                        <div class="alert alert-danger alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Información del Producto</h3>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="/ofm/admin/productos/crear.php" enctype="multipart/form-data" id="productoForm">
                                <input type="hidden" name="action" value="crear">
                                
                                <div class="row">
                                    <!-- Columna izquierda -->
                                    <div class="col-md-8">
                                        <!-- Información básica -->
                                        <div class="form-group">
                                            <label for="nombre">Nombre del Producto *</label>
                                            <input type="text" class="form-control" id="nombre" name="nombre" required 
                                                   placeholder="Ej: Smartphone Samsung Galaxy S21"
                                                   value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>">
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="descripcion">Descripción *</label>
                                            <textarea class="form-control" id="descripcion" name="descripcion" rows="4" required
                                                      placeholder="Describe las características principales del producto..."><?= htmlspecialchars($_POST['descripcion'] ?? '') ?></textarea>
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
                                                               step="0.01" min="0" required placeholder="0.00"
                                                               value="<?= htmlspecialchars($_POST['precio'] ?? '') ?>">
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
                                                               step="0.01" min="0" placeholder="0.00"
                                                               value="<?= htmlspecialchars($_POST['precio_oferta'] ?? '') ?>">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="stock">Stock</label>
                                                    <input type="number" class="form-control" id="stock" name="stock" 
                                                           min="0" value="0" placeholder="0">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="categoria">Categoría</label>
                                                    <input type="text" class="form-control" id="categoria" name="categoria" 
                                                           list="categorias" placeholder="Ej: Electrónicos">
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
                                                           placeholder="Ej: Samsung">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="codigo_producto">Código del Producto</label>
                                                    <input type="text" class="form-control" id="codigo_producto" name="codigo_producto" 
                                                           placeholder="Ej: SM-G991B">
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="peso">Peso (kg)</label>
                                                    <input type="number" class="form-control" id="peso" name="peso" 
                                                           step="0.01" min="0" placeholder="0.00">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="dimensiones">Dimensiones</label>
                                                    <input type="text" class="form-control" id="dimensiones" name="dimensiones" 
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
                                                    <option value="<?= $comercio['id'] ?>">
                                                        <?= htmlspecialchars($comercio['nombre_comercio']) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        
                                        <!-- Estado -->
                                        <div class="form-group">
                                            <label for="status">Estado</label>
                                            <select class="form-control" id="status" name="status">
                                                <option value="activo">Activo</option>
                                                <option value="inactivo">Inactivo</option>
                                                <option value="agotado">Agotado</option>
                                            </select>
                                        </div>
                                        
                                        <!-- Destacado -->
                                        <div class="form-group">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input" id="destacado" name="destacado" value="1">
                                                <label class="custom-control-label" for="destacado">
                                                    Producto Destacado
                                                </label>
                                            </div>
                                        </div>
                                        
                                        <!-- Imágenes -->
                                        <div class="form-group">
                                            <label>Imágenes del Producto *</label>
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input" id="imagenes" name="imagenes[]" 
                                                       multiple accept="image/*" required>
                                                <label class="custom-file-label" for="imagenes">Seleccionar imágenes...</label>
                                            </div>
                                            <small class="form-text text-muted">
                                                Selecciona al menos 1 imagen. Formatos: JPG, PNG, GIF. Máximo 5 imágenes.
                                            </small>
                                        </div>
                                        
                                        <!-- Vista previa de imágenes -->
                                        <div id="imagePreviewContainer" class="mt-3">
                                            <div class="image-preview empty" id="imagePreviewTemplate">
                                                <i class="fas fa-plus fa-2x"></i>
                                                <br>
                                                <small>Agregar imagen</small>
                                            </div>
                                        </div>
                                        
                                        <!-- Controles de imagen -->
                                        <div id="imageControls" class="mt-3" style="display: none;">
                                            <div class="alert alert-info">
                                                <i class="fas fa-info-circle"></i>
                                                <strong>Gestión de imágenes:</strong>
                                                <ul class="mb-0 mt-2">
                                                    <li>Arrastra las imágenes para cambiar el orden</li>
                                                    <li>La primera imagen será la imagen principal</li>
                                                    <li>Haz clic en × para eliminar una imagen</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Botones de acción -->
                                <div class="form-group text-center mt-4">
                                    <button type="submit" class="btn btn-success btn-lg">
                                        <i class="fas fa-save"></i> Crear Producto
                                    </button>
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
            let selectedFiles = [];
            
            // Manejo de archivos de imagen
            $('#imagenes').change(function() {
                const files = this.files;
                console.log('Archivos seleccionados:', files);
                selectedFiles = Array.from(files);
                console.log('selectedFiles actualizado:', selectedFiles);
                updateImagePreview();
            });
            
            // Función para actualizar vista previa
            function updateImagePreview() {
                const container = $('#imagePreviewContainer');
                const controls = $('#imageControls');
                
                // Limpiar contenedor
                container.empty();
                
                if (selectedFiles.length > 0) {
                    // Mostrar controles
                    controls.show();
                    
                    // Mostrar vista previa de cada imagen
                    selectedFiles.forEach((file, index) => {
                        const reader = new FileReader();
                        
                        reader.onload = function(e) {
                            const imageContainer = $('<div class="image-container" data-index="' + index + '">');
                            const imagePreview = $('<div class="image-preview">');
                            const img = $('<img src="' + e.target.result + '" alt="Vista previa">');
                            const removeBtn = $('<button type="button" class="remove-image" title="Eliminar imagen">×</button>');
                            const orderBadge = $('<span class="badge badge-primary" style="position: absolute; top: 5px; left: 5px;">' + (index + 1) + '</span>');
                            const principalBadge = index === 0 ? $('<span class="badge badge-success" style="position: absolute; bottom: 5px; left: 5px;">Principal</span>') : $('');
                            
                            imagePreview.append(img);
                            imagePreview.append(orderBadge);
                            if (index === 0) {
                                imagePreview.append(principalBadge);
                            }
                            imageContainer.append(imagePreview);
                            imageContainer.append(removeBtn);
                            container.append(imageContainer);
                            
                            // Evento para eliminar imagen
                            removeBtn.click(function() {
                                const indexToRemove = parseInt(imageContainer.data('index'));
                                selectedFiles.splice(indexToRemove, 1);
                                updateImagePreview();
                                updateFileInput();
                            });
                        };
                        
                        reader.readAsDataURL(file);
                    });
                    
                    // Hacer las imágenes arrastrables
                    makeImagesDraggable();
                } else {
                    // Ocultar controles y mostrar template vacío
                    controls.hide();
                    container.append($('#imagePreviewTemplate').clone());
                }
            }
            
            // Función para hacer las imágenes arrastrables
            function makeImagesDraggable() {
                $('.image-container').each(function() {
                    $(this).attr('draggable', true);
                    
                    $(this).on('dragstart', function(e) {
                        e.originalEvent.dataTransfer.setData('text/plain', $(this).data('index'));
                    });
                    
                    $(this).on('dragover', function(e) {
                        e.preventDefault();
                        $(this).addClass('drag-over');
                    });
                    
                    $(this).on('dragleave', function() {
                        $(this).removeClass('drag-over');
                    });
                    
                    $(this).on('drop', function(e) {
                        e.preventDefault();
                        $(this).removeClass('drag-over');
                        
                        const fromIndex = parseInt(e.originalEvent.dataTransfer.getData('text/plain'));
                        const toIndex = parseInt($(this).data('index'));
                        
                        if (fromIndex !== toIndex) {
                            // Reordenar archivos
                            const file = selectedFiles[fromIndex];
                            selectedFiles.splice(fromIndex, 1);
                            selectedFiles.splice(toIndex, 0, file);
                            
                            updateImagePreview();
                        }
                    });
                });
            }
            
            // Función para actualizar el input de archivos
            function updateFileInput() {
                console.log('updateFileInput llamado con selectedFiles:', selectedFiles);
                
                // Crear un nuevo FileList con los archivos seleccionados
                const dt = new DataTransfer();
                selectedFiles.forEach(file => dt.items.add(file));
                
                console.log('DataTransfer creado con', dt.files.length, 'archivos');
                
                // Actualizar el input
                $('#imagenes')[0].files = dt.files;
                
                console.log('Input actualizado. Archivos en input:', $('#imagenes')[0].files);
                
                // Actualizar la etiqueta
                const fileName = selectedFiles.length > 0 ? 
                    selectedFiles.length + ' imagen(es) seleccionada(s)' : 
                    'Seleccionar imágenes...';
                $('#imagenes').next('.custom-file-label').html(fileName);
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
                
                if (selectedFiles.length === 0) {
                    alert('Debes seleccionar al menos una imagen para el producto');
                    e.preventDefault();
                    return false;
                }
                
                // CRÍTICO: Sincronizar archivos antes del envío
                updateFileInput();
                
                // Mostrar indicador de carga
                $(this).find('button[type="submit"]').html('<i class="fas fa-spinner fa-spin"></i> Creando...').prop('disabled', true);
            });
            
            // Actualizar etiqueta del input de archivos
            $('.custom-file-input').on('change', function() {
                const files = this.files;
                const fileName = files.length > 0 ? 
                    files.length + ' imagen(es) seleccionada(s)' : 
                    'Seleccionar imágenes...';
                $(this).next('.custom-file-label').html(fileName);
            });
            
            // Agregar estilos CSS para drag & drop
            $('<style>')
                .prop('type', 'text/css')
                .html(`
                    .image-container.drag-over {
                        transform: scale(1.05);
                        box-shadow: 0 0 20px rgba(0,123,255,0.5);
                        transition: all 0.2s ease;
                    }
                    .image-container {
                        cursor: move;
                        transition: all 0.2s ease;
                    }
                    .image-container:hover {
                        transform: scale(1.02);
                    }
                    .badge {
                        font-size: 10px;
                        padding: 3px 6px;
                    }
                `)
                .appendTo('head');
        });
    </script>
</body>
</html>
