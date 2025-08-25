<?php
/**
 * Archivo para Matar Todas las Sesiones - OFM
 * Útil para debugging y limpieza de sesiones
 */

echo "<h1>🔪 Matador de Sesiones - OFM</h1>";
echo "<p>Este archivo destruirá todas las sesiones activas.</p>";

// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

echo "<h2>📊 Estado de la Sesión ANTES:</h2>";
echo "<p><strong>Session ID:</strong> " . session_id() . "</p>";
echo "<p><strong>Usuario ID:</strong> " . ($_SESSION['usuario_id'] ?? 'No definido') . "</p>";
echo "<p><strong>Rol:</strong> " . ($_SESSION['usuario_rol'] ?? 'No definido') . "</p>";
echo "<p><strong>Nombre:</strong> " . ($_SESSION['usuario_nombre'] ?? 'No definido') . "</p>";
echo "<p><strong>Email:</strong> " . ($_SESSION['usuario_email'] ?? 'No definido') . "</p>";

// Matar la sesión actual
echo "<h2>💀 Matando Sesión Actual...</h2>";

// Limpiar todas las variables de sesión
$_SESSION = array();

// Destruir la sesión
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destruir la sesión
session_destroy();

echo "<p>✅ <strong>Sesión destruida exitosamente</strong></p>";

// Iniciar una nueva sesión limpia
session_start();

echo "<h2>📊 Estado de la Sesión DESPUÉS:</h2>";
echo "<p><strong>Session ID:</strong> " . session_id() . " (nuevo)</p>";
echo "<p><strong>Usuario ID:</strong> " . ($_SESSION['usuario_id'] ?? 'No definido') . "</p>";
echo "<p><strong>Rol:</strong> " . ($_SESSION['usuario_rol'] ?? 'No definido') . "</p>";

echo "<h2>🔗 Enlaces de Prueba:</h2>";
echo "<p><a href='/ofm/public/evara/page-login-register.php' target='_blank'>📝 Ir a Login/Register</a></p>";
echo "<p><a href='/ofm/admin/' target='_blank'>🛡️ Ir a Panel Admin</a></p>";
echo "<p><a href='/ofm/cliente/dashboard.php' target='_blank'>👤 Ir a Dashboard Cliente</a></p>";
echo "<p><a href='/ofm/socio/dashboard.php' target='_blank'>🏪 Ir a Dashboard Socio</a></p>";

echo "<h2>🧹 Limpieza Adicional:</h2>";
echo "<p>Si quieres limpiar también las cookies del navegador:</p>";
echo "<ol>";
echo "<li>Presiona <strong>F12</strong> para abrir las herramientas de desarrollador</li>";
echo "<li>Ve a la pestaña <strong>Application</strong> (Chrome) o <strong>Storage</strong> (Firefox)</li>";
echo "<li>En <strong>Cookies</strong>, elimina todas las cookies de <strong>localhost</strong></li>";
echo "<li>Recarga la página</li>";
echo "</ol>";

echo "<hr>";
echo "<p><em>🔄 Recarga esta página para verificar que la sesión esté completamente limpia.</em></p>";
echo "<p><a href='kill-sessions.php'>🔄 Recargar Página</a></p>";
?>
