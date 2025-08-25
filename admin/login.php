<?php
/**
 * Redirección al Login Principal del Panel Administrativo OFM
 */

session_start();

// Si ya está logueado como admin, redirigir al dashboard
if (isset($_SESSION['usuario_id']) && isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin') {
    header('Location: dashboard.php');
    exit;
}

// Si no está logueado, redirigir al login principal con parámetro de redirección
header('Location: /ofm/public/evara/page-login-register.php?redirect=admin');
exit;
?>
