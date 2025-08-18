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
            // Validaciones básicas
            if (empty($datos['nombre']) || empty($datos['descripcion']) || empty($datos['precio'])) {
                return ['success' => false, 'message' => 'Nombre, descripción y precio son obligatorios'];
            }

            if (!is_numeric($datos['precio']) || $datos['precio'] <= 0) {
                return ['success' => false, 'message' => 'El precio debe ser un número válido mayor a 0'];
            }

            // Crear producto
            $resultado = $this->producto->crear($datos);
            
            if ($resultado['success'] && !empty($imagenes)) {
                // Procesar imágenes
                $this->procesarImagenes($resultado['id'], $imagenes);
            }

            return $resultado;
        } catch (Exception $e) {
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
                // Procesar nuevas imágenes
                $this->procesarImagenes($id, $imagenes);
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

    // Procesar imágenes subidas
    private function procesarImagenes($productoId, $imagenes) {
        $directorioDestino = __DIR__ . '/../public/uploads/productos/';
        
        // Crear directorio si no existe
        if (!is_dir($directorioDestino)) {
            mkdir($directorioDestino, 0755, true);
        }

        foreach ($imagenes as $imagen) {
            if ($imagen['error'] === UPLOAD_ERR_OK) {
                $nombreArchivo = $this->generarNombreArchivo($imagen['name']);
                $rutaCompleta = $directorioDestino . $nombreArchivo;

                if (move_uploaded_file($imagen['tmp_name'], $rutaCompleta)) {
                    // Guardar en base de datos
                    $this->guardarImagen($productoId, $nombreArchivo);
                }
            }
        }
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
    private function guardarImagen($productoId, $nombreArchivo) {
        try {
            $sql = "INSERT INTO producto_imagenes (producto_id, nombre_archivo, creado_en) VALUES (?, ?, NOW())";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$productoId, $nombreArchivo]);
        } catch (PDOException $e) {
            error_log("Error al guardar imagen: " . $e->getMessage());
        }
    }

    // Obtener imágenes de un producto
    private function obtenerImagenes($productoId) {
        try {
            $sql = "SELECT * FROM producto_imagenes WHERE producto_id = ? ORDER BY creado_en ASC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$productoId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
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
