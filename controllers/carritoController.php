<?php

class CarritoController {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
        
        // Iniciar sesión si no está iniciada
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        // Inicializar carrito si no existe
        if (!isset($_SESSION['carrito'])) {
            $_SESSION['carrito'] = [];
        }
    }
    
    /**
     * Agregar producto al carrito
     */
    public function agregarProducto($productoId, $cantidad = 1) {
        try {
            // Verificar que el producto existe y está activo
            $stmt = $this->pdo->prepare("SELECT id, nombre, precio, stock FROM productos WHERE id = ? AND status = 'activo'");
            $stmt->execute([$productoId]);
            $producto = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$producto) {
                return ['success' => false, 'message' => 'Producto no encontrado o no disponible'];
            }
            
            // Verificar stock
            if ($producto['stock'] < $cantidad) {
                return ['success' => false, 'message' => 'Stock insuficiente'];
            }
            
            // Verificar si el producto ya está en el carrito
            $productoEnCarrito = false;
            foreach ($_SESSION['carrito'] as &$item) {
                if ($item['producto_id'] == $productoId) {
                    $item['cantidad'] += $cantidad;
                    $productoEnCarrito = true;
                    break;
                }
            }
            
            // Si no está en el carrito, agregarlo
            if (!$productoEnCarrito) {
                $_SESSION['carrito'][] = [
                    'producto_id' => $productoId,
                    'nombre' => $producto['nombre'],
                    'precio' => $producto['precio'],
                    'cantidad' => $cantidad,
                    'agregado_en' => date('Y-m-d H:i:s')
                ];
            }
            
            return ['success' => true, 'message' => 'Producto agregado al carrito'];
            
        } catch (PDOException $e) {
            error_log("Error al agregar producto al carrito: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error interno del servidor'];
        }
    }
    
    /**
     * Actualizar cantidad de un producto en el carrito
     */
    public function actualizarCantidad($productoId, $cantidad) {
        try {
            if ($cantidad <= 0) {
                return $this->eliminarProducto($productoId);
            }
            
            // Verificar stock
            $stmt = $this->pdo->prepare("SELECT stock FROM productos WHERE id = ? AND status = 'activo'");
            $stmt->execute([$productoId]);
            $producto = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$producto || $producto['stock'] < $cantidad) {
                return ['success' => false, 'message' => 'Stock insuficiente'];
            }
            
            // Actualizar cantidad en el carrito
            foreach ($_SESSION['carrito'] as &$item) {
                if ($item['producto_id'] == $productoId) {
                    $item['cantidad'] = $cantidad;
                    break;
                }
            }
            
            return ['success' => true, 'message' => 'Cantidad actualizada'];
            
        } catch (PDOException $e) {
            error_log("Error al actualizar cantidad: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error interno del servidor'];
        }
    }
    
    /**
     * Eliminar producto del carrito
     */
    public function eliminarProducto($productoId) {
        foreach ($_SESSION['carrito'] as $key => $item) {
            if ($item['producto_id'] == $productoId) {
                unset($_SESSION['carrito'][$key]);
                break;
            }
        }
        
        // Reindexar array
        $_SESSION['carrito'] = array_values($_SESSION['carrito']);
        
        return ['success' => true, 'message' => 'Producto eliminado del carrito'];
    }
    
    /**
     * Obtener contenido del carrito
     */
    public function obtenerCarrito() {
        return $_SESSION['carrito'] ?? [];
    }
    
    /**
     * Obtener total del carrito
     */
    public function obtenerTotal() {
        $total = 0;
        foreach ($_SESSION['carrito'] as $item) {
            $total += $item['precio'] * $item['cantidad'];
        }
        return $total;
    }
    
    /**
     * Obtener cantidad de productos en el carrito
     */
    public function obtenerCantidadProductos() {
        $cantidad = 0;
        foreach ($_SESSION['carrito'] as $item) {
            $cantidad += $item['cantidad'];
        }
        return $cantidad;
    }
    
    /**
     * Limpiar carrito
     */
    public function limpiarCarrito() {
        $_SESSION['carrito'] = [];
        return ['success' => true, 'message' => 'Carrito limpiado'];
    }
    
    /**
     * Verificar si el carrito está vacío
     */
    public function carritoVacio() {
        return empty($_SESSION['carrito']);
    }
    
    /**
     * Obtener información detallada del carrito con imágenes
     */
    public function obtenerCarritoDetallado() {
        if (empty($_SESSION['carrito'])) {
            return [];
        }
        
        try {
            $productoIds = array_column($_SESSION['carrito'], 'producto_id');
            $placeholders = str_repeat('?,', count($productoIds) - 1) . '?';
            
            $sql = "SELECT p.id, p.nombre, p.precio, p.stock, p.status, 
                           pi.nombre_archivo as imagen_principal
                    FROM productos p 
                    LEFT JOIN producto_imagenes pi ON p.id = pi.producto_id AND pi.principal = 1
                    WHERE p.id IN ($placeholders)";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($productoIds);
            $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Combinar información del producto con el carrito
            $carritoDetallado = [];
            foreach ($_SESSION['carrito'] as $item) {
                foreach ($productos as $producto) {
                    if ($producto['id'] == $item['producto_id']) {
                        $carritoDetallado[] = array_merge($item, $producto);
                        break;
                    }
                }
            }
            
            return $carritoDetallado;
            
        } catch (PDOException $e) {
            error_log("Error al obtener carrito detallado: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Procesar checkout del carrito
     */
    public function procesarCheckout($usuarioId, $datosEnvio) {
        try {
            if ($this->carritoVacio()) {
                return ['success' => false, 'message' => 'El carrito está vacío'];
            }
            
            // Verificar stock de todos los productos
            foreach ($_SESSION['carrito'] as $item) {
                $stmt = $this->pdo->prepare("SELECT stock FROM productos WHERE id = ?");
                $stmt->execute([$item['producto_id']]);
                $producto = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if (!$producto || $producto['stock'] < $item['cantidad']) {
                    return ['success' => false, 'message' => 'Stock insuficiente para ' . $item['nombre']];
                }
            }
            
            // Aquí implementarías la lógica para crear la orden
            // Por ahora solo retornamos éxito
            return ['success' => true, 'message' => 'Orden procesada correctamente'];
            
        } catch (PDOException $e) {
            error_log("Error al procesar checkout: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error interno del servidor'];
        }
    }
}
?>
