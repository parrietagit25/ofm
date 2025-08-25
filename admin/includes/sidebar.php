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

// Obtener la página actual para marcar el menú activo
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
$currentDir = basename(dirname($_SERVER['PHP_SELF']));
?>

    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <!-- Brand Logo -->
        <a href="dashboard.php" class="brand-link">
            <span class="brand-text font-weight-light">OFM Admin</span>
        </a>

        <!-- Sidebar -->
        <div class="sidebar">
            <!-- Sidebar Menu -->
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                    <!-- Dashboard -->
                    <li class="nav-item">
                        <a href="dashboard.php" class="nav-link">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>
                    
                    <!-- Usuarios -->
                    <li class="nav-item">
                        <a href="usuarios/" class="nav-link">
                            <i class="nav-icon fas fa-users"></i>
                            <p>Usuarios</p>
                        </a>
                    </li>
                    
                    <!-- Comercios -->
                    <li class="nav-item">
                        <a href="comercios/" class="nav-link">
                            <i class="nav-icon fas fa-store"></i>
                            <p>Comercios</p>
                        </a>
                    </li>
                    
                    <!-- Productos -->
                    <li class="nav-item">
                        <a href="productos/" class="nav-link">
                            <i class="nav-icon fas fa-box"></i>
                            <p>Productos</p>
                        </a>
                    </li>
                    
                    <!-- Órdenes de Clientes -->
                    <li class="nav-item">
                        <a href="ordenes-clientes/" class="nav-link">
                            <i class="nav-icon fas fa-shopping-cart"></i>
                            <p>Órdenes de Clientes</p>
                        </a>
                    </li>
                    
                    <!-- Ventas -->
                    <li class="nav-item">
                        <a href="ventas/" class="nav-link">
                            <i class="nav-icon fas fa-chart-line"></i>
                            <p>Ventas</p>
                        </a>
                    </li>
                    
                    <!-- Reportes -->
                    <li class="nav-item">
                        <a href="reportes/" class="nav-link">
                            <i class="nav-icon fas fa-chart-bar"></i>
                            <p>Reportes</p>
                        </a>
                    </li>
                    
                    <!-- Configuración -->
                    <li class="nav-item">
                        <a href="configuracion/" class="nav-link">
                            <i class="nav-icon fas fa-cog"></i>
                            <p>Configuración</p>
                        </a>
                    </li>
                </ul>
            </nav>
            <!-- /.sidebar-menu -->
        </div>
        <!-- /.sidebar -->
    </aside>
