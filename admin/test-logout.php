<?php
/**
 * Archivo de prueba para verificar la funcionalidad del logout
 * Este archivo se puede eliminar en producción
 */

session_start();
require_once __DIR__ . '/../controllers/loginController.php';

// Crear instancia del controlador
$loginController = new LoginController($pdo);

echo "<h1>Prueba de Logout - OFM Admin</h1>";

if ($loginController->estaAutenticado()) {
    $usuario = $loginController->obtenerUsuarioActual();
    echo "<p><strong>Usuario autenticado:</strong> " . htmlspecialchars($usuario['nombre']) . "</p>";
    echo "<p><strong>Rol:</strong> " . htmlspecialchars($usuario['rol']) . "</p>";
    echo "<p><strong>Email:</strong> " . htmlspecialchars($usuario['email']) . "</p>";
    
    echo "<hr>";
    echo "<h3>Opciones de Logout:</h3>";
    echo "<p><a href='logout.php'>1. Usar archivo logout.php dedicado</a></p>";
    echo "<p><a href='dashboard.php?logout=1'>2. Usar parámetro en dashboard</a></p>";
    echo "<p><a href='usuarios.php?logout=1'>3. Usar parámetro en usuarios</a></p>";
    echo "<p><a href='comercios/index.php?logout=1'>4. Usar parámetro en comercios</a></p>";
    echo "<p><a href='productos/index.php?logout=1'>5. Usar parámetro en productos</a></p>";
    
} else {
    echo "<p><strong>No hay usuario autenticado</strong></p>";
    echo "<p><a href='../public/evara/page-login-register.php'>Ir al login</a></p>";
}

echo "<hr>";
echo "<p><small>Este archivo es solo para pruebas. Eliminar en producción.</small></p>";
?>
