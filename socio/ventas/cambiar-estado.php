<?php
require_once __DIR__ . '/../../controllers/loginController.php';
require_once __DIR__ . '/../../models/Venta.php';

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

$ventaId = intval($input['venta_id'] ?? 0);
$nuevoEstado = trim($input['estado'] ?? '');

if ($ventaId <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID de venta inválido']);
    exit;
}

// Estados válidos
$estadosValidos = ['pendiente', 'pagado', 'enviado', 'entregado', 'cancelado'];

if (!in_array($nuevoEstado, $estadosValidos)) {
    echo json_encode(['success' => false, 'message' => 'Estado inválido']);
    exit;
}

try {
    $ventaModel = new Venta($pdo);
    
    // Verificar que la venta pertenezca al socio
    $venta = $ventaModel->obtenerPorId($ventaId);
    
    if (!$venta) {
        echo json_encode(['success' => false, 'message' => 'Venta no encontrada']);
        exit;
    }
    
    // Verificar que la venta pertenezca al socio actual
    if ($venta['socio_id'] != $usuario['id']) {
        echo json_encode(['success' => false, 'message' => 'No tienes permisos para modificar esta venta']);
        exit;
    }
    
    // Cambiar estado de la venta
    $resultado = $ventaModel->actualizarEstado($ventaId, $nuevoEstado);
    
    if ($resultado) {
        echo json_encode([
            'success' => true, 
            'message' => "Estado de la venta actualizado a: " . ucfirst($nuevoEstado),
            'nuevo_estado' => $nuevoEstado
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al actualizar el estado de la venta']);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error interno del servidor']);
}
?>
