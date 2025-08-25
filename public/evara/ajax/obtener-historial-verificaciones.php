<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../../../includes/config.php';
require_once __DIR__ . '/../../../includes/db.php';
require_once __DIR__ . '/../../../controllers/qrVerificationController.php';
require_once __DIR__ . '/../../../controllers/loginController.php';

try {
    // Verificar autenticación
    $pdo = getConnection();
    $loginController = new LoginController($pdo);
    
    if (!$loginController->estaAutenticado()) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Usuario no autenticado']);
        exit;
    }
    
    $usuario = $loginController->obtenerUsuarioActual();
    
    // Obtener historial de verificaciones del usuario
    $sql = "SELECT qv.*, od.nombre_producto, c.nombre as nombre_comercio
            FROM qr_verificaciones qv 
            JOIN orden_detalles od ON qv.orden_detalle_id = od.id 
            JOIN comercios c ON od.comercio_id = c.id 
            WHERE qv.verificado_por = ? 
            ORDER BY qv.fecha_verificacion DESC 
            LIMIT 10";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$usuario['id']]);
    $verificaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'verificaciones' => $verificaciones
    ]);
    
} catch (Exception $e) {
    error_log("Error obteniendo historial de verificaciones: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error interno del servidor'
    ]);
} catch (PDOException $e) {
    error_log("Error de base de datos obteniendo historial: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error de base de datos'
    ]);
}
?>
