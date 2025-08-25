<?php
/**
 * Controlador de Productos para OFM
 * Maneja todas las operaciones CRUD de productos
 */

require_once __DIR__ . '/../includes/session_helper.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../models/Producto.php';
require_once __DIR__ . '/../models/Comercio.php';

class ProductoController {
    private $pdo;
    private $producto;
    private $comercio;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->producto = new Producto($pdo);
        $this->comercio = new Comercio($pdo);
    }

    // Crear nuevo producto
    public function crear($datos, $imagenes = []) {
        try {
            error_log("DEBUG: ProductoController->crear llamado");
            error_log("DEBUG: datos recibidos: " . print_r($datos, true));
            error_log("DEBUG: imágenes recibidas: " . print_r($imagenes, true));
            error_log("DEBUG: count de imágenes: " . count($imagenes));
            
            // Validaciones básicas
            if (empty($datos['nombre']) || empty($datos['descripcion']) || empty($datos['precio'])) {
                return ['success' => false, 'message' => 'Nombre, descripción y precio son obligatorios'];
            }

            if (!is_numeric($datos['precio']) || $datos['precio'] <= 0) {
                return ['success' => false, 'message' => 'El precio debe ser un número válido mayor a 0'];
            }

            // Crear producto
            $resultado = $this->producto->crear($datos);
            error_log("DEBUG: resultado de crear producto: " . print_r($resultado, true));
            
            if ($resultado['success'] && !empty($imagenes)) {
                error_log("DEBUG: llamando a procesarImagenes");
                // Reorganizar array de imágenes si es necesario
                $imagenesReorganizadas = $this->reorganizarArrayImagenes($imagenes);
                error_log("DEBUG: imágenes reorganizadas: " . print_r($imagenesReorganizadas, true));
                // Procesar imágenes
                $this->procesarImagenes($resultado['id'], $imagenesReorganizadas);
            } else {
                error_log("DEBUG: NO se procesaron imágenes. success: " . ($resultado['success'] ? 'true' : 'false') . ", imágenes vacías: " . (empty($imagenes) ? 'true' : 'false'));
            }

            return $resultado;
        } catch (Exception $e) {
            error_log("ERROR en crear producto: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error al crear producto: ' . $e->getMessage()];
        }
    }

    // Actualizar producto
    public function actualizar($id, $datos, $imagenes = []) {
        try {
            // Validaciones básicas
            if (empty($datos['nombre']) || empty($datos['descripcion']) || empty($datos['precio'])) {
                return ['success' => false, 'message' => 'Nombre, descripción y precio son obligatorios'];
            }

            if (!is_numeric($datos['precio']) || $datos['precio'] <= 0) {
                return ['success' => false, 'message' => 'El precio debe ser un número válido mayor a 0'];
            }

            // Actualizar producto
            $resultado = $this->producto->actualizar($id, $datos);
            
            if ($resultado['success'] && !empty($imagenes)) {
                // Reorganizar array de imágenes si es necesario
                $imagenesReorganizadas = $this->reorganizarArrayImagenes($imagenes);
                // Procesar nuevas imágenes
                $this->procesarImagenes($id, $imagenesReorganizadas);
            }

            return $resultado;
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error al actualizar producto: ' . $e->getMessage()];
        }
    }

    // Eliminar producto (cambiar a inactivo)
    public function eliminar($id) {
        try {
            $resultado = $this->producto->eliminar($id);
            if ($resultado) {
                return ['success' => true, 'message' => 'Producto eliminado correctamente'];
            }
            return ['success' => false, 'message' => 'Error al eliminar el producto'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error al eliminar producto: ' . $e->getMessage()];
        }
    }

    // Obtener producto por ID con imágenes
    public function obtenerPorId($id) {
        try {
            $producto = $this->producto->obtenerPorId($id);
            if ($producto) {
                $producto['imagenes'] = $this->obtenerImagenes($id);
                $producto['comercio'] = $this->comercio->obtenerPorId($producto['comercio_id']);
            }
            return $producto;
        } catch (Exception $e) {
            return false;
        }
    }

    // Obtener todos los productos con paginación
    public function obtenerTodos($pagina = 1, $porPagina = 10, $filtros = []) {
        try {
            $offset = ($pagina - 1) * $porPagina;
            $productos = $this->producto->obtenerTodos($offset, $porPagina, $filtros);
            $total = $this->producto->obtenerTotal($filtros);
            
            return [
                'productos' => $productos,
                'total' => $total,
                'paginas' => ceil($total / $porPagina),
                'pagina_actual' => $pagina
            ];
        } catch (Exception $e) {
            return ['productos' => [], 'total' => 0, 'paginas' => 0, 'pagina_actual' => 1];
        }
    }

    // Obtener productos simples (sin paginación)
    public function obtenerProductos($filtros = []) {
        try {
            $productos = $this->producto->obtenerTodos(0, 1000, $filtros);
            
            // Agregar imagen principal a cada producto
            foreach ($productos as &$producto) {
                $producto['imagen_principal'] = $this->producto->obtenerImagenPrincipal($producto['id']);
            }
            unset($producto);
            
            return $productos;
        } catch (Exception $e) {
            return [];
        }
    }

    // Procesar imágenes subidas
    private function procesarImagenes($productoId, $imagenes) {
        error_log("DEBUG: procesarImagenes iniciado para producto ID: $productoId");
        error_log("DEBUG: total de imágenes a procesar: " . count($imagenes));
        
        if (empty($imagenes)) {
            error_log("DEBUG: no hay imágenes para procesar");
            return;
        }
        
        $directorioDestino = __DIR__ . '/../public/uploads/productos/';
        error_log("DEBUG: directorio destino: $directorioDestino");
        
        // Verificar que el directorio existe y es escribible
        if (!is_dir($directorioDestino)) {
            error_log("DEBUG: creando directorio: $directorioDestino");
            if (!mkdir($directorioDestino, 0777, true)) {
                error_log("ERROR: no se pudo crear el directorio: $directorioDestino");
                return;
            }
        }
        
        if (!is_writable($directorioDestino)) {
            error_log("ERROR: directorio no es escribible: $directorioDestino");
            return;
        }
        
        error_log("DEBUG: directorio verificado y es escribible");
        
        $orden = 1;
        $principal = true; // La primera imagen será la principal
        
        foreach ($imagenes as $imagen) {
            error_log("DEBUG: procesando imagen: " . print_r($imagen, true));
            
            if ($imagen['error'] !== UPLOAD_ERR_OK) {
                error_log("DEBUG: error en imagen: " . $imagen['error']);
                continue;
            }
            
            if (!file_exists($imagen['tmp_name'])) {
                error_log("ERROR: archivo temporal no existe: " . $imagen['tmp_name']);
                continue;
            }
            
            // Generar nombre único para el archivo
            $extension = pathinfo($imagen['name'], PATHINFO_EXTENSION);
            $nombreArchivo = uniqid() . '_' . time() . '.' . $extension;
            $rutaCompleta = $directorioDestino . $nombreArchivo;
            
            error_log("DEBUG: intentando mover archivo a: $rutaCompleta");
            
            // Intentar move_uploaded_file primero
            $archivoMovido = false;
            if (move_uploaded_file($imagen['tmp_name'], $rutaCompleta)) {
                error_log("DEBUG: move_uploaded_file exitoso");
                $archivoMovido = true;
            } else {
                error_log("DEBUG: move_uploaded_file falló, intentando copy");
                
                // Fallback: usar copy si move_uploaded_file falla
                if (copy($imagen['tmp_name'], $rutaCompleta)) {
                    error_log("DEBUG: copy exitoso como fallback");
                    $archivoMovido = true;
                } else {
                    error_log("ERROR: tanto move_uploaded_file como copy fallaron");
                    $error = error_get_last();
                    if ($error) {
                        error_log("ERROR: " . $error['message']);
                    }
                    continue;
                }
            }
            
            if ($archivoMovido) {
                // Verificar que el archivo se creó correctamente
                if (file_exists($rutaCompleta)) {
                    error_log("DEBUG: archivo creado exitosamente: $rutaCompleta");
                    error_log("DEBUG: tamaño del archivo: " . filesize($rutaCompleta) . " bytes");
                    
                    // Guardar información en la base de datos
                    $datosImagen = [
                        'producto_id' => $productoId,
                        'nombre_archivo' => $nombreArchivo,
                        'ruta' => $nombreArchivo, // Solo el nombre del archivo, no la ruta completa
                        'tipo' => $imagen['type'],
                        'orden' => $orden,
                        'principal' => $principal
                    ];
                    
                    $resultado = $this->guardarImagen($datosImagen);
                    if ($resultado) {
                        error_log("DEBUG: imagen guardada en BD con ID: " . $resultado);
                    } else {
                        error_log("ERROR: no se pudo guardar imagen en BD");
                    }
                    
                    $orden++;
                    $principal = false; // Solo la primera es principal
                } else {
                    error_log("ERROR: archivo no existe después de mover: $rutaCompleta");
                }
            }
        }
        
        error_log("DEBUG: procesarImagenes completado");
    }

    // Generar nombre único para archivo
    private function generarNombreArchivo($nombreOriginal) {
        $extension = pathinfo($nombreOriginal, PATHINFO_EXTENSION);
        $nombreBase = pathinfo($nombreOriginal, PATHINFO_FILENAME);
        $timestamp = time();
        $random = uniqid();
        
        return $nombreBase . '_' . $timestamp . '_' . $random . '.' . $extension;
    }

    // Guardar imagen en base de datos
    private function guardarImagen($datosImagen) {
        try {
            error_log("DEBUG: guardarImagen - productoId: " . $datosImagen['producto_id'] . ", archivo: " . $datosImagen['nombre_archivo'] . ", principal: " . ($datosImagen['principal'] ? 'SÍ' : 'NO') . ", orden: " . $datosImagen['orden']);
            
            $sql = "INSERT INTO producto_imagenes (producto_id, nombre_archivo, ruta, tipo, orden, principal, creado_en) VALUES (?, ?, ?, ?, ?, ?, NOW())";
            $stmt = $this->pdo->prepare($sql);
            $resultado = $stmt->execute([
                $datosImagen['producto_id'],
                $datosImagen['nombre_archivo'],
                $datosImagen['ruta'],
                $datosImagen['tipo'],
                $datosImagen['orden'],
                $datosImagen['principal'] ? 1 : 0
            ]);
            
            if ($resultado) {
                return $this->pdo->lastInsertId();
            } else {
                error_log("ERROR: fallo al ejecutar INSERT en BD");
                return false;
            }
        } catch (PDOException $e) {
            error_log("ERROR al guardar imagen: " . $e->getMessage());
            return false;
        }
    }

    // Obtener imágenes de un producto
    public function obtenerImagenes($productoId) {
        try {
            // Usar el modelo para obtener las imágenes procesadas
            return $this->producto->obtenerImagenes($productoId);
        } catch (PDOException $e) {
            return [];
        }
    }

    // Obtener todas las imágenes de un producto (alias para compatibilidad)
    public function obtenerTodasImagenes($productoId) {
        return $this->obtenerImagenes($productoId);
    }

    // Eliminar imagen
    public function eliminarImagen($imagenId) {
        try {
            // Obtener información de la imagen
            $sql = "SELECT * FROM producto_imagenes WHERE id = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$imagenId]);
            $imagen = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($imagen) {
                // Eliminar archivo físico
                $rutaArchivo = __DIR__ . '/../public/uploads/productos/' . $imagen['nombre_archivo'];
                if (file_exists($rutaArchivo)) {
                    unlink($rutaArchivo);
                }

                // Eliminar de base de datos
                $sql = "DELETE FROM producto_imagenes WHERE id = ?";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([$imagenId]);

                return ['success' => true, 'message' => 'Imagen eliminada correctamente'];
            }

            return ['success' => false, 'message' => 'Imagen no encontrada'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error al eliminar imagen: ' . $e->getMessage()];
        }
    }

    // Cambiar estado del producto
    public function cambiarEstado($id, $estado) {
        try {
            $resultado = $this->producto->cambiarEstado($id, $estado);
            if ($resultado) {
                return ['success' => true, 'message' => 'Estado del producto actualizado correctamente'];
            }
            return ['success' => false, 'message' => 'Error al actualizar el estado'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error al cambiar estado: ' . $e->getMessage()];
        }
    }

    // Marcar/desmarcar como destacado
    public function cambiarDestacado($id, $destacado) {
        try {
            $resultado = $this->producto->cambiarDestacado($id, $destacado);
            if ($resultado) {
                $mensaje = $destacado ? 'Producto marcado como destacado' : 'Producto desmarcado como destacado';
                return ['success' => true, 'message' => $mensaje];
            }
            return ['success' => false, 'message' => 'Error al actualizar destacado'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error al cambiar destacado: ' . $e->getMessage()];
        }
    }

    // Buscar productos
    public function buscar($termino, $filtros = []) {
        try {
            $productos = $this->producto->buscar($termino, $filtros);
            return ['success' => true, 'productos' => $productos];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error en búsqueda: ' . $e->getMessage()];
        }
    }

    // Obtener estadísticas de productos
    public function obtenerEstadisticas() {
        try {
            return $this->producto->obtenerEstadisticas();
        } catch (Exception $e) {
            return [
                'total_productos' => 0,
                'productos_activos' => 0,
                'productos_inactivos' => 0,
                'productos_destacados' => 0
            ];
        }
    }
    
    // Reorganizar array de imágenes de $_FILES a formato estándar
    private function reorganizarArrayImagenes($imagenes) {
        // Si ya está en el formato correcto, retornar tal como está
        if (empty($imagenes) || !isset($imagenes['name']) || !is_array($imagenes['name'])) {
            return $imagenes;
        }
        
        $imagenesReorganizadas = [];
        $totalImagenes = count($imagenes['name']);
        
        for ($i = 0; $i < $totalImagenes; $i++) {
            // Solo procesar si no hay errores de upload
            if ($imagenes['error'][$i] === UPLOAD_ERR_OK) {
                $imagenesReorganizadas[] = [
                    'name' => $imagenes['name'][$i],
                    'type' => $imagenes['type'][$i],
                    'tmp_name' => $imagenes['tmp_name'][$i],
                    'error' => $imagenes['error'][$i],
                    'size' => $imagenes['size'][$i]
                ];
            }
        }
        
        error_log("DEBUG: reorganizarArrayImagenes - entrada: " . count($imagenes['name']) . ", salida: " . count($imagenesReorganizadas));
        return $imagenesReorganizadas;
    }
}

// Instanciar controlador y procesar acciones POST
$productoController = new ProductoController($pdo);

// Procesar acciones si se reciben por POST
if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'crear':
            $datos = [
                'nombre' => $_POST['nombre'] ?? '',
                'descripcion' => $_POST['descripcion'] ?? '',
                'precio' => $_POST['precio'] ?? 0,
                'precio_oferta' => $_POST['precio_oferta'] ?? null,
                'categoria_id' => $_POST['categoria_id'] ?? null,
                'comercio_id' => $_POST['comercio_id'] ?? null,
                'stock' => $_POST['stock'] ?? 0,
                'destacado' => isset($_POST['destacado']) ? 1 : 0,
                'status' => 'activo'
            ];
            
            $imagenes = $_FILES['imagenes'] ?? [];
            $resultado = $productoController->crear($datos, $imagenes);
            
            if ($resultado['success']) {
                header('Location: /ofm/admin/productos/index.php?success=' . urlencode($resultado['message']));
            } else {
                header('Location: /ofm/admin/productos/crear.php?error=' . urlencode($resultado['message']));
            }
            exit;
            break;
            
        case 'actualizar':
            $id = $_POST['id'] ?? 0;
            $datos = [
                'nombre' => $_POST['nombre'] ?? '',
                'descripcion' => $_POST['descripcion'] ?? '',
                'precio' => $_POST['precio'] ?? 0,
                'precio_oferta' => $_POST['precio_oferta'] ?? null,
                'categoria_id' => $_POST['categoria_id'] ?? null,
                'comercio_id' => $_POST['comercio_id'] ?? null,
                'stock' => $_POST['stock'] ?? 0,
                'destacado' => isset($_POST['destacado']) ? 1 : 0
            ];
            
            $imagenes = $_FILES['imagenes'] ?? [];
            $resultado = $productoController->actualizar($id, $datos, $imagenes);
            
            if ($resultado['success']) {
                header('Location: /ofm/admin/productos/index.php?success=' . urlencode($resultado['message']));
            } else {
                header('Location: /ofm/admin/productos/editar.php?id=' . $id . '&error=' . urlencode($resultado['message']));
            }
            exit;
            break;
            
        case 'eliminar':
            $id = $_POST['id'] ?? 0;
            $resultado = $productoController->eliminar($id);
            
            if ($resultado['success']) {
                header('Location: /ofm/admin/productos/index.php?success=' . urlencode($resultado['message']));
            } else {
                header('Location: /ofm/admin/productos/index.php?error=' . urlencode($resultado['message']));
            }
            exit;
            break;
            
        case 'eliminar_imagen':
            $imagenId = $_POST['imagen_id'] ?? 0;
            $resultado = $productoController->eliminarImagen($imagenId);
            
            // Devolver respuesta JSON para AJAX
            header('Content-Type: application/json');
            echo json_encode($resultado);
            exit;
            break;
            
        case 'cambiar_estado':
            $id = $_POST['id'] ?? 0;
            $estado = $_POST['estado'] ?? 'activo';
            $resultado = $productoController->cambiarEstado($id, $estado);
            
            header('Content-Type: application/json');
            echo json_encode($resultado);
            exit;
            break;
            
        case 'cambiar_destacado':
            $id = $_POST['id'] ?? 0;
            $destacado = $_POST['destacado'] ?? 0;
            $resultado = $productoController->cambiarDestacado($id, $destacado);
            
            header('Content-Type: application/json');
            echo json_encode($resultado);
            exit;
            break;
    }
}
?>
