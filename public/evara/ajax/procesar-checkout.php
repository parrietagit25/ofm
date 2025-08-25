<?php
// Habilitar reporte de errores para debugging
error_reporting(E_ALL);
ini_set('display_errors', 1); // Mostrar errores para debugging
ini_set('log_errors', 1);

// Mostrar errores en el navegador para debugging
ini_set('display_startup_errors', 1);

// Headers para JSON
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Log del inicio de la petición
error_log("DEBUG: Iniciando procesar-checkout.php - Método: " . $_SERVER['REQUEST_METHOD']);

try {
    // Solo permitir método POST
    error_log("DEBUG: Método recibido: '" . $_SERVER['REQUEST_METHOD'] . "'");
    error_log("DEBUG: Método esperado: 'POST'");
    error_log("DEBUG: Comparación: " . ($_SERVER['REQUEST_METHOD'] === 'POST' ? 'TRUE' : 'FALSE'));
    
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        error_log("DEBUG: Método no permitido: " . $_SERVER['REQUEST_METHOD']);
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Método no permitido']);
        exit;
    }

    // Cargar includes
    error_log("DEBUG: Cargando includes...");
    require_once __DIR__ . '/../../../includes/config.php';
    require_once __DIR__ . '/../../../includes/db.php';
    require_once __DIR__ . '/../../../controllers/carritoController.php';
    require_once __DIR__ . '/../../../controllers/loginController.php';
    require_once __DIR__ . '/../../../controllers/checkoutController.php';
    error_log("DEBUG: Includes cargados correctamente");

    // Obtener datos del POST
    error_log("DEBUG: Obteniendo datos del POST...");
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        error_log("DEBUG: JSON decode falló, intentando con POST");
        $input = $_POST; // Fallback para form data
    }
    
    error_log("DEBUG: Datos recibidos: " . json_encode($input));
    
    // Validar datos requeridos
    $camposRequeridos = ['nombre', 'email', 'telefono', 'metodo_pago', 'total'];
    foreach ($camposRequeridos as $campo) {
        if (empty($input[$campo])) {
            error_log("DEBUG: Campo requerido faltante: $campo");
            throw new Exception("Campo requerido faltante: $campo");
        }
    }
    error_log("DEBUG: Validación de datos exitosa");

    // Procesar checkout
    error_log("DEBUG: Obteniendo conexión BD...");
    $pdo = getConnection();
    error_log("DEBUG: Conexión BD obtenida");
    
    error_log("DEBUG: Creando CheckoutController...");
    $checkoutController = new CheckoutController($pdo);
    error_log("DEBUG: CheckoutController creado");
    
    error_log("DEBUG: Llamando a procesarCheckout...");
    $resultado = $checkoutController->procesarCheckout($input);
    error_log("DEBUG: Resultado del checkout: " . json_encode($resultado));
    
    if ($resultado['success']) {
        error_log("DEBUG: Checkout exitoso, enviando respuesta");
        echo json_encode($resultado);
    } else {
        error_log("DEBUG: Checkout falló, enviando error 400");
        http_response_code(400);
        echo json_encode($resultado);
    }
    
} catch (Exception $e) {
    error_log("ERROR: Excepción en checkout AJAX: " . $e->getMessage());
    error_log("ERROR: Archivo: " . $e->getFile() . " Línea: " . $e->getLine());
    error_log("ERROR: Stack trace: " . $e->getTraceAsString());
    
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Error al procesar el checkout: ' . $e->getMessage()
    ]);
    
} catch (PDOException $e) {
    error_log("ERROR: PDOException en checkout AJAX: " . $e->getMessage());
    error_log("ERROR: Archivo: " . $e->getFile() . " Línea: " . $e->getLine());
    error_log("ERROR: Stack trace: " . $e->getTraceAsString());
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error interno del servidor'
    ]);
    
} catch (Error $e) {
    error_log("ERROR: Error fatal en checkout AJAX: " . $e->getMessage());
    error_log("ERROR: Archivo: " . $e->getFile() . " Línea: " . $e->getLine());
    error_log("ERROR: Stack trace: " . $e->getTraceAsString());
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error interno del servidor'
    ]);
    
} catch (Throwable $e) {
    error_log("ERROR: Throwable en checkout AJAX: " . $e->getMessage());
    error_log("ERROR: Archivo: " . $e->getFile() . " Línea: " . $e->getLine());
    error_log("ERROR: Stack trace: " . $e->getTraceAsString());
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error interno del servidor'
    ]);
}

error_log("DEBUG: Finalizando procesar-checkout.php");
?>
