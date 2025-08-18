<?php
session_start();
require_once __DIR__ . '/SocialAuthController.php';
require_once __DIR__ . '/../../../includes/db.php';

$socialAuth = new SocialAuthController($pdo);

$code = $_GET['code'] ?? '';
$error = $_GET['error'] ?? '';

if ($error) {
    header('Location: ../page-login-register.php?error=social_auth&provider=facebook');
    exit;
}

$result = $socialAuth->processFacebookCallback($code);

if ($result['success']) {
    $user = $result['user'];
    
    // Redirigir según el tipo de usuario
    switch ($user['rol'] ?? 'usuario') {
        case 'admin':
            header('Location: /ofm/admin/dashboard.php');
            break;
        case 'socio':
            header('Location: /ofm/socio/dashboard.php');
            break;
        default:
            header('Location: ../index.php');
            break;
    }
} else {
    header('Location: ../page-login-register.php?error=social_auth&message=' . urlencode($result['message']));
}
exit; 