<?php
require_once __DIR__ . '/../../controllers/loginController.php';
require_once __DIR__ . '/../../models/Producto.php';

// Verificar que el usuario esté autenticado y sea socio
$loginController->verificarAcceso('socio');

// Verificar que sea una petición POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

// Obtener datos JSON del body
$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['producto_id'])) {
    echo json_encode(['success' => false, 'message' => 'ID de producto no proporcionado']);
    exit;
}

$producto_id = intval($input['producto_id']);

// Obtener información del usuario actual
$usuario = $loginController->obtenerUsuarioActual();

// Obtener comercio del socio
require_once __DIR__ . '/../../models/Comercio.php';
$comercioModel = new Comercio($pdo);
$comercios = $comercioModel->obtenerPorUsuarioSocio($usuario['id']);

if (empty($comercios)) {
    echo json_encode(['success' => false, 'message' => 'No tienes un comercio asignado']);
    exit;
}

$comercio_id = $comercios[0]['id'];

// Verificar que el producto exista y pertenezca al socio
$productoModel = new Producto($pdo);
$producto = $productoModel->obtenerPorId($producto_id);

if (!$producto || $producto['comercio_id'] != $comercio_id) {
    echo json_encode(['success' => false, 'message' => 'Producto no encontrado o no tienes permisos']);
    exit;
}

// Eliminar el producto (soft delete - cambiar status a 'eliminado')
$datos = ['status' => 'eliminado'];
$resultado = $productoModel->actualizar($producto_id, $datos);

if ($resultado['success']) {
    echo json_encode([
        'success' => true, 
        'message' => 'Producto eliminado correctamente'
    ]);
} else {
    echo json_encode([
        'success' => false, 
        'message' => 'Error al eliminar el producto: ' . $resultado['message']
    ]);
}
?>
