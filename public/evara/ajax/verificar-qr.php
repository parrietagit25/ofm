<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../../../includes/config.php';
require_once __DIR__ . '/../../../includes/db.php';
require_once __DIR__ . '/../../../controllers/qrVerificationController.php';

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
    
    // Validar datos requeridos
    if (empty($input['codigo_qr'])) {
        throw new Exception('Código QR es requerido');
    }
    
    // Procesar verificación
    $pdo = getConnection();
    $qrController = new QRVerificationController($pdo);
    
    $resultado = $qrController->verificarQR($input['codigo_qr'], $input);
    
    if ($resultado['success']) {
        echo json_encode($resultado);
    } else {
        http_response_code(400);
        echo json_encode($resultado);
    }
    
} catch (Exception $e) {
    error_log("Error en verificación QR AJAX: " . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Error al verificar el QR: ' . $e->getMessage()
    ]);
} catch (PDOException $e) {
    error_log("Error de base de datos en verificación QR: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error interno del servidor'
    ]);
}
?>
