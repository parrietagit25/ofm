<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../models/Usuario.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$id = $input['id'] ?? 0;

if (!$id) {
    echo json_encode(['success' => false, 'message' => 'ID de usuario requerido']);
    exit;
}

// No permitir eliminar el usuario ID 1 (admin principal)
if ($id == 1) {
    echo json_encode(['success' => false, 'message' => 'No se puede eliminar el administrador principal']);
    exit;
}

try {
    $usuarioModel = new Usuario($pdo);
    
    // Obtener información del usuario antes de eliminar
    $usuario = $usuarioModel->obtenerPorId($id);
    
    if (!$usuario) {
        echo json_encode(['success' => false, 'message' => 'Usuario no encontrado']);
        exit;
    }
    
    // Eliminar usuario de la base de datos
    $resultado = $usuarioModel->eliminar($id);
    
    if ($resultado) {
        echo json_encode(['success' => true, 'message' => 'Usuario eliminado correctamente']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al eliminar el usuario']);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?> 