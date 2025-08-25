<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../../../includes/config.php';
require_once __DIR__ . '/../../../includes/db.php';
require_once __DIR__ . '/../../../controllers/carritoController.php';
require_once __DIR__ . '/../../../controllers/loginController.php';

// Solo permitir método POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

try {
    // Obtener datos del POST
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        $input = $_POST; // Fallback para form data
    }
    
    $productoId = isset($input['producto_id']) ? (int)$input['producto_id'] : 0;
    $cantidad = isset($input['cantidad']) ? (int)$input['cantidad'] : 1;
    
    if (!$productoId) {
        throw new Exception('ID de producto requerido');
    }
    
    if ($cantidad <= 0) {
        throw new Exception('Cantidad debe ser mayor a 0');
    }
    
    // Verificar autenticación
    $pdo = getConnection();
    $loginController = new LoginController($pdo);
    if (!$loginController->estaAutenticado()) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Debes iniciar sesión']);
        exit;
    }
    
    // Actualizar cantidad en el carrito
    $carritoController = new CarritoController($pdo);
    $resultado = $carritoController->actualizarCantidad($productoId, $cantidad);
    
    if ($resultado['success']) {
        // Obtener información actualizada del carrito
        $cantidadProductos = $carritoController->obtenerCantidadProductos();
        $total = $carritoController->obtenerTotal();
        
        echo json_encode([
            'success' => true,
            'message' => $resultado['message'],
            'carrito' => [
                'cantidad_productos' => $cantidadProductos,
                'total' => $total
            ]
        ]);
    } else {
        http_response_code(400);
        echo json_encode($resultado);
    }
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
} catch (PDOException $e) {
    error_log("Error de base de datos: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error interno del servidor'
    ]);
}
?>
