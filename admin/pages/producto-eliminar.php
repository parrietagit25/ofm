<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../models/Producto.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$id = $input['id'] ?? 0;

if (!$id) {
    echo json_encode(['success' => false, 'message' => 'ID de producto requerido']);
    exit;
}

try {
    $productoModel = new Producto($pdo);
    
    // Obtener información del producto antes de eliminar
    $producto = $productoModel->obtenerPorId($id);
    
    if (!$producto) {
        echo json_encode(['success' => false, 'message' => 'Producto no encontrado']);
        exit;
    }
    
    // Eliminar imagen si existe
    if (!empty($producto['imagen'])) {
        $imagePath = '../../uploads/' . $producto['imagen'];
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
    }
    
    // Eliminar producto de la base de datos
    $resultado = $productoModel->eliminar($id);
    
    if ($resultado) {
        echo json_encode(['success' => true, 'message' => 'Producto eliminado correctamente']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al eliminar el producto']);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?> 