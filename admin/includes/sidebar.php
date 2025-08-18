<?php
// Verificar que el usuario esté autenticado y sea admin
if (!isset($loginController) || !$loginController->estaAutenticado() || !$loginController->tieneRol('admin')) {
    header('Location: ../controllers/loginController.php');
    exit;
}

// Obtener información del usuario actual
$usuario = $loginController->obtenerUsuarioActual();

// Obtener la página actual para marcar el menú activo
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
$currentDir = basename(dirname($_SERVER['PHP_SELF']));
?>

<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="<?= (strpos($_SERVER['PHP_SELF'], '/admin/comercios/') !== false || strpos($_SERVER['PHP_SELF'], '/admin/productos/') !== false) ? '../dashboard.php' : 'dashboard.php' ?>" class="brand-link">
        <img src="<?= (strpos($_SERVER['PHP_SELF'], '/admin/comercios/') !== false || strpos($_SERVER['PHP_SELF'], '/admin/productos/') !== false) ? '../dist/img/AdminLTELogo.png' : 'dist/img/AdminLTELogo.png' ?>" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light">OFM Admin</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="<?= (strpos($_SERVER['PHP_SELF'], '/admin/comercios/') !== false || strpos($_SERVER['PHP_SELF'], '/admin/productos/') !== false) ? '../dist/img/user2-160x160.jpg' : 'dist/img/user2-160x160.jpg' ?>" class="img-circle elevation-2" alt="User Image">
            </div>
            <div class="info">
                <a href="#" class="d-block"><?= htmlspecialchars($usuario['nombre']) ?></a>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <!-- Dashboard -->
                <li class="nav-item <?= ($currentPage === 'dashboard' && $currentDir === 'admin') ? 'menu-open' : '' ?>">
                                         <a href="<?= (strpos($_SERVER['PHP_SELF'], '/admin/comercios/') !== false || strpos($_SERVER['PHP_SELF'], '/admin/productos/') !== false) ? '../dashboard.php' : 'dashboard.php' ?>" class="nav-link <?= ($currentPage === 'dashboard' && $currentDir === 'admin') ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <!-- Gestión de Usuarios -->
                <li class="nav-item <?= (in_array($currentPage, ['usuarios', 'crear-usuario', 'ver-usuario', 'editar-usuario']) && $currentDir === 'admin') ? 'menu-open' : '' ?>">
                    <a href="#" class="nav-link <?= (in_array($currentPage, ['usuarios', 'crear-usuario', 'ver-usuario', 'editar-usuario']) && $currentDir === 'admin') ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-users"></i>
                        <p>
                            Gestión de Usuarios
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                                                         <a href="<?= (strpos($_SERVER['PHP_SELF'], '/admin/comercios/') !== false || strpos($_SERVER['PHP_SELF'], '/admin/productos/') !== false) ? '../usuarios.php' : 'usuarios.php' ?>" class="nav-link <?= $currentPage === 'usuarios' ? 'active' : '' ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Ver Usuarios</p>
                            </a>
                        </li>
                        <li class="nav-item">
                                                         <a href="<?= (strpos($_SERVER['PHP_SELF'], '/admin/comercios/') !== false || strpos($_SERVER['PHP_SELF'], '/admin/productos/') !== false) ? '../crear-usuario.php' : 'crear-usuario.php' ?>" class="nav-link <?= $currentPage === 'crear-usuario' ? 'active' : '' ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Crear Usuario</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Gestión de Comercios -->
                <li class="nav-item <?= strpos($_SERVER['PHP_SELF'], '/admin/comercios/') !== false ? 'menu-open' : '' ?>">
                    <a href="#" class="nav-link <?= strpos($_SERVER['PHP_SELF'], '/admin/comercios/') !== false ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-store"></i>
                        <p>
                            Gestión de Comercios
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                                                         <a href="<?= (strpos($_SERVER['PHP_SELF'], '/admin/comercios/') !== false || strpos($_SERVER['PHP_SELF'], '/admin/productos/') !== false) ? 'index.php' : 'comercios/index.php' ?>" class="nav-link <?= strpos($_SERVER['PHP_SELF'], '/admin/comercios/index.php') !== false ? 'active' : '' ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Listar Comercios</p>
                            </a>
                        </li>
                        <li class="nav-item">
                                                         <a href="<?= (strpos($_SERVER['PHP_SELF'], '/admin/comercios/') !== false || strpos($_SERVER['PHP_SELF'], '/admin/productos/') !== false) ? 'crear.php' : 'comercios/crear.php' ?>" class="nav-link <?= strpos($_SERVER['PHP_SELF'], '/admin/comercios/crear.php') !== false ? 'active' : '' ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Crear Comercio</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Gestión de Productos -->
                <li class="nav-item <?= strpos($_SERVER['PHP_SELF'], '/admin/productos/') !== false ? 'menu-open' : '' ?>">
                    <a href="#" class="nav-link <?= strpos($_SERVER['PHP_SELF'], '/admin/productos/') !== false ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-box"></i>
                        <p>
                            Gestión de Productos
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="<?= strpos($_SERVER['PHP_SELF'], '/admin/productos/') !== false ? 'index.php' : '../admin/productos/index.php' ?>" class="nav-link <?= strpos($_SERVER['PHP_SELF'], '/admin/productos/index.php') !== false ? 'active' : '' ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Listar Productos</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= strpos($_SERVER['PHP_SELF'], '/admin/productos/') !== false ? 'crear.php' : '../admin/productos/crear.php' ?>" class="nav-link <?= strpos($_SERVER['PHP_SELF'], '/admin/productos/crear.php') !== false ? 'active' : '' ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Crear Producto</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Ventas -->
                <li class="nav-item <?= (in_array($currentPage, ['ventas', 'crear-venta', 'ver-venta', 'editar-venta']) && $currentDir === 'admin') ? 'menu-open' : '' ?>">
                    <a href="#" class="nav-link <?= (in_array($currentPage, ['ventas', 'crear-venta', 'ver-venta', 'editar-venta']) && $currentDir === 'admin') ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-shopping-cart"></i>
                        <p>
                            Ventas
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="<?= (strpos($_SERVER['PHP_SELF'], '/admin/comercios/') !== false || strpos($_SERVER['PHP_SELF'], '/admin/productos/') !== false) ? '../ventas.php' : 'ventas.php' ?>" class="nav-link <?= $currentPage === 'ventas' ? 'active' : '' ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Ver Ventas</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= (strpos($_SERVER['PHP_SELF'], '/admin/comercios/') !== false || strpos($_SERVER['PHP_SELF'], '/admin/productos/') !== false) ? '../crear-venta.php' : 'crear-venta.php' ?>" class="nav-link <?= $currentPage === 'crear-venta' ? 'active' : '' ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Crear Venta</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Reportes -->
                <li class="nav-item <?= $currentPage === 'reportes-ventas' ? 'menu-open' : '' ?>">
                    <a href="<?= (strpos($_SERVER['PHP_SELF'], '/admin/comercios/') !== false || strpos($_SERVER['PHP_SELF'], '/admin/productos/') !== false) ? '../reportes-ventas.php' : 'reportes-ventas.php' ?>" class="nav-link <?= $currentPage === 'reportes-ventas' ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-chart-bar"></i>
                        <p>Reportes</p>
                    </a>
                </li>

                <!-- Configuración -->
                <li class="nav-item <?= (in_array($currentPage, ['configuracion', 'categorias']) && $currentDir === 'admin') ? 'menu-open' : '' ?>">
                    <a href="#" class="nav-link <?= (in_array($currentPage, ['configuracion', 'categorias']) && $currentDir === 'admin') ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-cog"></i>
                        <p>
                            Configuración
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="<?= (strpos($_SERVER['PHP_SELF'], '/admin/comercios/') !== false || strpos($_SERVER['PHP_SELF'], '/admin/productos/') !== false) ? '../configuracion.php' : 'configuracion.php' ?>" class="nav-link <?= $currentPage === 'configuracion' ? 'active' : '' ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>General</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= (strpos($_SERVER['PHP_SELF'], '/admin/comercios/') !== false || strpos($_SERVER['PHP_SELF'], '/admin/productos/') !== false) ? '../categorias.php' : 'categorias.php' ?>" class="nav-link <?= $currentPage === 'categorias' ? 'active' : '' ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Categorías</p>
                            </a>
                        </li>
                    </ul>
                </li>
                
                <!-- Separador -->
                <li class="nav-header">SISTEMA</li>
                
                <!-- Logout -->
                <li class="nav-item">
                    <a href="<?= (strpos($_SERVER['PHP_SELF'], '/admin/comercios/') !== false || strpos($_SERVER['PHP_SELF'], '/admin/productos/') !== false) ? '../logout.php' : 'logout.php' ?>" class="nav-link text-danger">
                        <i class="nav-icon fas fa-sign-out-alt"></i>
                        <p>Salir del Sistema</p>
                    </a>
                </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
