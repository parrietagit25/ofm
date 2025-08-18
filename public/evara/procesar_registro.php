<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../models/Usuario.php';

$usuario = new Usuario($pdo);

$nombre = $_POST['username'] ?? '';
$email = $_POST['email'] ?? '';
$clave = $_POST['clave'] ?? '';

if ($usuario->emailExiste($email)) {
    header('Location: page-login-register.php?error=email');
    exit;
}
if ($usuario->usernameExiste($nombre)) {
    header('Location: page-login-register.php?error=username');
    exit;
}

if ($usuario->registrar($nombre, $email, $clave)) {
    header('Location: page-login-register.php?mensaje=registro_ok');
} else {
    header('Location: page-login-register.php?error=registro');
}
