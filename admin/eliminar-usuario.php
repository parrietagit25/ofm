<?php
require_once __DIR__ . '/../controllers/loginController.php';
require_once __DIR__ . '/../models/Usuario.php';

// Verificar que el usuario esté autenticado y sea admin
$loginController->verificarAcceso('admin');

// Obtener información del usuario actual
$usuario = $loginController->obtenerUsuarioActual();

// Verificar expiración de sesión
$loginController->verificarExpiracionSesion();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode(['success' => false, 'message' => 'Datos inválidos']);
    exit;
}

$userId = intval($input['user_id'] ?? 0);

if ($userId <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID de usuario inválido']);
    exit;
}

// Verificar que no se esté eliminando a sí mismo
if ($userId == $usuario['id']) {
    echo json_encode(['success' => false, 'message' => 'No puedes eliminar tu propia cuenta']);
    exit;
}

try {
    $usuarioModel = new Usuario($pdo);
    $user = $usuarioModel->obtenerPorId($userId);
    
    if (!$user) {
        echo json_encode(['success' => false, 'message' => 'Usuario no encontrado']);
        exit;
    }
    
    // Verificar si es el último administrador
    if ($user['rol'] === 'admin') {
        $totalAdmins = count($usuarioModel->obtenerPorRol('admin'));
        if ($totalAdmins <= 1) {
            echo json_encode(['success' => false, 'message' => 'No se puede eliminar el último administrador del sistema']);
            exit;
        }
    }
    
    $resultado = $usuarioModel->eliminar($userId);
    
    if ($resultado) {
        echo json_encode([
            'success' => true, 
            'message' => 'Usuario eliminado exitosamente'
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al eliminar el usuario']);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error interno del servidor']);
}
?>
