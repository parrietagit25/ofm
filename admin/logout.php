<?php
/**
 * Archivo de logout para el sistema administrativo OFM
 * Maneja el cierre de sesión de forma segura
 */

// Incluir helper de sesiones
require_once __DIR__ . '/../includes/session_helper.php';

// Iniciar sesión de forma segura
iniciarSesionSegura();

require_once __DIR__ . '/../controllers/loginController_simple.php';

// Crear instancia del controlador
$loginController = new LoginControllerSimple($pdo);

// Cerrar la sesión
$resultado = $loginController->cerrarSesion();

// Redirigir a la página de login
header('Location: /ofm/public/evara/page-login-register.php?logout=success');
exit;
?>
