<?php
// Archivo de prueba para verificar el sistema de login
echo "<h1>Test de Login</h1>";
echo "<p>Este es un archivo de prueba para verificar que PHP funciona correctamente.</p>";

// Verificar si hay sesión activa
session_start();
echo "<h2>Estado de la Sesión:</h2>";
echo "<p>Session ID: " . session_id() . "</p>";
echo "<p>Usuario ID: " . ($_SESSION['usuario_id'] ?? 'No definido') . "</p>";
echo "<p>Rol: " . ($_SESSION['usuario_rol'] ?? 'No definido') . "</p>";

// Enlaces de prueba
echo "<h2>Enlaces de Prueba:</h2>";
echo "<p><a href='page-login-register.php'>Ir a Login/Register</a></p>";
echo "<p><a href='index.php'>Ir a Inicio</a></p>";

// Verificar archivos
echo "<h2>Verificación de Archivos:</h2>";
$files = [
    'page-login-register.php' => file_exists('page-login-register.php'),
    'header.php' => file_exists('header.php'),
    'controllers/loginController_simple.php' => file_exists('../../controllers/loginController_simple.php')
];

foreach ($files as $file => $exists) {
    echo "<p>$file: " . ($exists ? '✅ Existe' : '❌ No existe') . "</p>";
}
?>
