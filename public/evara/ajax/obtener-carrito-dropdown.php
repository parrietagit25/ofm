<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../../../includes/config.php';
require_once __DIR__ . '/../../../includes/db.php';
require_once __DIR__ . '/../../../controllers/carritoController.php';
require_once __DIR__ . '/../../../controllers/loginController.php';

try {
    // Verificar autenticación
    $pdo = getConnection();
    $loginController = new LoginController($pdo);
    
    if (!$loginController->estaAutenticado()) {
        echo json_encode([
            'success' => false, 
            'message' => 'Usuario no autenticado'
        ]);
        exit;
    }
    
    // Obtener información del carrito
    $carritoController = new CarritoController($pdo);
    $productos = $carritoController->obtenerCarritoDetallado();
    $total = $carritoController->obtenerTotal();
    $cantidadProductos = $carritoController->obtenerCantidadProductos();
    
    // Procesar productos para el dropdown
    $productosDropdown = [];
    foreach ($productos as $producto) {
        $productosDropdown[] = [
            'producto_id' => $producto['producto_id'],
            'nombre' => $producto['nombre'],
            'precio' => $producto['precio'],
            'cantidad' => $producto['cantidad'],
            'imagen_url' => !empty($producto['imagen_principal']) ? 
                getProductImageUrl($producto['imagen_principal']) : 
                'assets/imgs/shop/default.png'
        ];
    }
    
    echo json_encode([
        'success' => true,
        'carrito' => [
            'productos' => $productosDropdown,
            'total' => $total,
            'cantidad_productos' => $cantidadProductos
        ]
    ]);
    
} catch (Exception $e) {
    error_log("Error obteniendo carrito dropdown: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error interno del servidor'
    ]);
} catch (PDOException $e) {
    error_log("Error de base de datos en carrito dropdown: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error de base de datos'
    ]);
}
?>
