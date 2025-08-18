<?php
session_start();
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../models/Usuario.php';

$usuario = new Usuario($pdo);

$email = $_POST['email'] ?? '';
$clave = $_POST['clave'] ?? '';

// Buscar usuario por email
$datosUsuario = $usuario->obtenerPorEmail($email);
if ($datosUsuario) {
    // Usuario existe, verificar contraseña
    if (password_verify($clave, $datosUsuario['clave'])) {
        $_SESSION['usuario'] = $datosUsuario;
        echo "<pre>";
        print_r($datosUsuario);
        echo "</pre>";
        exit;
        // Redirige según tipo
        switch ($datosUsuario['tipo'] ?? $datosUsuario['rol'] ?? 'cliente') {
            case 'admin':
                header('Location: /ofm/admin/dashboard.php');
                break;
            case 'socio':
                header('Location: /ofm/socio/dashboard.php');
                break;
            default: // cliente
                header('Location: index.php');
                break;
        }
        exit;
    } else {
        // Usuario existe pero contraseña incorrecta
        header('Location: page-login-register.php?error=pass');
        exit;
    }
} else {
    // Usuario no existe
    header('Location: page-login-register.php?error=login');
    exit;
}
