<?php
require_once __DIR__ . '/../../controllers/loginController.php';
require_once __DIR__ . '/../../models/Producto.php';

// Verificar que el usuario esté autenticado y sea socio
$loginController->verificarAcceso('socio');

// Obtener información del usuario actual
$usuario = $loginController->obtenerUsuarioActual();

// Verificar expiración de sesión
$loginController->verificarExpiracionSesion();

// Configurar headers para JSON
header('Content-Type: application/json');

// Verificar que sea una petición POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

// Obtener datos del JSON
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode(['success' => false, 'message' => 'Datos inválidos']);
    exit;
}

$productoId = intval($input['producto_id'] ?? 0);
$nuevoEstado = intval($input['activo'] ?? 0);

if ($productoId <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID de producto inválido']);
    exit;
}

try {
    $productoModel = new Producto($pdo);
    
    // Verificar que el producto pertenezca al socio
    $producto = $productoModel->obtenerPorId($productoId);
    
    if (!$producto) {
        echo json_encode(['success' => false, 'message' => 'Producto no encontrado']);
        exit;
    }
    
    // Verificar que el producto pertenezca al socio actual
    if ($producto['socio_id'] != $usuario['id']) {
        echo json_encode(['success' => false, 'message' => 'No tienes permisos para modificar este producto']);
        exit;
    }
    
    // Cambiar estado del producto
    $resultado = $productoModel->cambiarEstado($productoId, $nuevoEstado);
    
    if ($resultado) {
        $estadoTexto = $nuevoEstado ? 'activado' : 'desactivado';
        echo json_encode([
            'success' => true, 
            'message' => "Producto $estadoTexto exitosamente",
            'nuevo_estado' => $nuevoEstado
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al cambiar el estado del producto']);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error interno del servidor']);
}
?>
