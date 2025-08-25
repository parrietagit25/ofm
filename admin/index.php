<?php
/**
 * Página de inicio del Panel Administrativo OFM
 * Redirige al login o dashboard según el estado de autenticación
 */

session_start();

// Si ya está logueado como admin, redirigir al dashboard
if (isset($_SESSION['usuario_id']) && isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin') {
    header('Location: dashboard.php');
    exit;
}

// Si no está logueado, redirigir al login
header('Location: login.php');
exit;
?>
