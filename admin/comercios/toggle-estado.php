<?php
require_once __DIR__ . '/../../controllers/loginController.php';
require_once __DIR__ . '/../../models/Comercio.php';

// Verificar que el usuario esté autenticado y sea admin
$loginController->verificarAcceso('admin');

// Verificar expiración de sesión
$loginController->verificarExpiracionSesion();

// Verificar que sea una petición POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

// Obtener datos del JSON
$input = json_decode(file_get_contents('php://input'), true);
$comercio_id = $input['comercio_id'] ?? null;
$activo = $input['activo'] ?? null;

if (!$comercio_id || !isset($input['activo'])) {
    echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
    exit;
}

try {
    // Cambiar estado del comercio
    $comercioModel = new Comercio($pdo);
    $resultado = $comercioModel->cambiarEstado($comercio_id, $activo);
    
    if ($resultado['success']) {
        $estadoTexto = $activo ? 'activado' : 'desactivado';
        echo json_encode(['success' => true, 'message' => "Comercio {$estadoTexto} exitosamente"]);
    } else {
        echo json_encode(['success' => false, 'message' => $resultado['message']]);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error al cambiar el estado: ' . $e->getMessage()]);
}
?>
