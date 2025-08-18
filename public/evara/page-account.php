<?php
session_start();
// Si no hay usuario en sesión, redirigir a login
if (!isset($_SESSION['usuario'])) {
    header('Location: page-login-register.php');
    exit;
}

$usuario = $_SESSION['usuario'];
$rol = $usuario['rol'] ?? 'cliente';

// Redirigir según el rol
if ($rol === 'admin') {
    header('Location: /ofm/admin/dashboard.php');
    exit;
}
if ($rol === 'socio') {
    header('Location: /ofm/socio/dashboard.php');
    exit;
}
// Si es cliente, mostrar la página de cuenta
?>
<!DOCTYPE html>
<html class="no-js" lang="en">
<head>
    <meta charset="utf-8">
    <title>Evara - Mi Cuenta</title>
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" type="image/x-icon" href="assets/imgs/theme/favicon.svg">
    <link rel="stylesheet" href="assets/css/main.css?v=3.4">
</head>
<body>
    <?php include 'header.php'; ?>
    <main class="main">
        <div class="container mt-5 mb-5">
            <div class="row">
                <div class="col-lg-8 m-auto">
                    <div class="card p-4">
                        <h2 class="mb-4">Mi Cuenta</h2>
                        <p><strong>Nombre:</strong> <?= htmlspecialchars($usuario['nombre'] ?? '') ?></p>
                        <p><strong>Email:</strong> <?= htmlspecialchars($usuario['email'] ?? '') ?></p>
                        <p><strong>Rol:</strong> <?= htmlspecialchars($rol) ?></p>
                        <a href="logout.php" class="btn btn-danger mt-3">Cerrar sesión</a>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <?php include 'footer.php'; ?>
</body>
</html> 