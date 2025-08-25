<?php
// La autenticación ya se verificó en auth_check.php
// Solo obtener información del usuario si está disponible
$usuario = null;
if (isset($_SESSION['usuario_id']) && $_SESSION['usuario_rol'] === 'admin') {
    $usuario = [
        'id' => $_SESSION['usuario_id'],
        'nombre' => $_SESSION['usuario_nombre'] ?? 'Administrador',
        'email' => $_SESSION['usuario_email'] ?? '',
        'rol' => $_SESSION['usuario_rol']
    ];
}
?>

<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="<?= (strpos($_SERVER['PHP_SELF'], '/admin/comercios/') !== false || strpos($_SERVER['PHP_SELF'], '/admin/productos/') !== false) ? '../dashboard.php' : 'dashboard.php' ?>" class="nav-link">Dashboard</a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="<?= (strpos($_SERVER['PHP_SELF'], '/admin/comercios/') !== false || strpos($_SERVER['PHP_SELF'], '/admin/productos/') !== false) ? '../usuarios.php' : 'usuarios.php' ?>" class="nav-link">Usuarios</a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="<?= (strpos($_SERVER['PHP_SELF'], '/admin/comercios/') !== false || strpos($_SERVER['PHP_SELF'], '/admin/productos/') !== false) ? '../comercios/' : 'comercios/' ?>" class="nav-link">Comercios</a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="<?= (strpos($_SERVER['PHP_SELF'], '/admin/comercios/') !== false || strpos($_SERVER['PHP_SELF'], '/admin/productos/') !== false) ? '../productos/' : 'productos/' ?>" class="nav-link">Productos</a>
        </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
        <!-- Notifications Dropdown Menu -->
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="far fa-bell"></i>
                <span class="badge badge-warning navbar-badge">3</span>
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <span class="dropdown-item dropdown-header">3 Notificaciones</span>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item">
                    <i class="fas fa-envelope mr-2"></i> Nuevo usuario registrado
                    <span class="float-right text-muted text-sm">3 mins</span>
                </a>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item">
                    <i class="fas fa-store mr-2"></i> Nuevo comercio creado
                    <span class="float-right text-muted text-sm">12 hours</span>
                </a>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item">
                    <i class="fas fa-box mr-2"></i> Producto sin stock
                    <span class="float-right text-muted text-sm">2 days</span>
                </a>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item dropdown-footer">Ver todas las notificaciones</a>
            </div>
        </li>
        
        <!-- Fullscreen Toggle -->
        <li class="nav-item">
            <a class="nav-link" data-widget="fullscreen" href="#" role="button">
                <i class="fas fa-expand-arrows-alt"></i>
            </a>
        </li>
        
        <!-- User Dropdown Menu -->
        <li class="nav-item dropdown user-menu">
            <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">
                <img src="<?= (strpos($_SERVER['PHP_SELF'], '/admin/comercios/') !== false || strpos($_SERVER['PHP_SELF'], '/admin/productos/') !== false) ? '../dist/img/user2-160x160.jpg' : 'dist/img/user2-160x160.jpg' ?>" class="user-image img-circle elevation-2" alt="User Image">
                <span class="d-none d-md-inline"><?= htmlspecialchars($usuario['nombre']) ?></span>
            </a>
            <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <!-- User image -->
                <li class="user-header bg-primary">
                    <img src="<?= (strpos($_SERVER['PHP_SELF'], '/admin/comercios/') !== false || strpos($_SERVER['PHP_SELF'], '/admin/productos/') !== false) ? '../dist/img/user2-160x160.jpg' : 'dist/img/user2-160x160.jpg' ?>" class="img-circle elevation-2" alt="User Image">
                    <p>
                        <?= htmlspecialchars($usuario['nombre']) ?>
                        <small>Administrador del Sistema</small>
                    </p>
                </li>
                <!-- Menu Footer-->
                <li class="user-footer">
                    <a href="#" class="btn btn-default btn-flat">Perfil</a>
                    <a href="<?= (strpos($_SERVER['PHP_SELF'], '/admin/comercios/') !== false || strpos($_SERVER['PHP_SELF'], '/admin/productos/') !== false) ? '../logout.php' : 'logout.php' ?>" class="btn btn-default btn-flat float-right">
                        <i class="fas fa-sign-out-alt mr-1"></i>Salir
                    </a>
                </li>
            </ul>
        </li>
    </ul>
</nav>
<!-- /.navbar -->
