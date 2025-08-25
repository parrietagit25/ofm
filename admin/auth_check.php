<?php
/**
 * Verificación de autenticación para el panel administrativo
 */

// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar si el usuario está autenticado como admin
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'admin') {
    header('Location: /ofm/public/evara/page-login-register.php?redirect=admin');
    exit;
}

// Verificar expiración de sesión (8 horas)
if (isset($_SESSION['login_time'])) {
    $tiempoActual = time();
    $tiempoLogin = $_SESSION['login_time'];
    $tiempoExpiracion = 8 * 60 * 60; // 8 horas en segundos

    if (($tiempoActual - $tiempoLogin) > $tiempoExpiracion) {
        // Limpiar sesión
        session_unset();
        session_destroy();
        header('Location: /ofm/public/evara/page-login-register.php?redirect=admin&expired=1');
        exit;
    }
}
?>
