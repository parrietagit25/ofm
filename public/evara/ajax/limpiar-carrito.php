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
    // Verificar autenticación
$pdo = getConnection();
$loginController = new LoginController($pdo);
    if (!$loginController->estaAutenticado()) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Debes iniciar sesión']);
        exit;
    }
    
    // Limpiar carrito
    $carritoController = new CarritoController($pdo);
    $resultado = $carritoController->limpiarCarrito();
    
    if ($resultado['success']) {
        echo json_encode([
            'success' => true,
            'message' => $resultado['message'],
            'carrito' => [
                'cantidad_productos' => 0,
                'total' => 0
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
