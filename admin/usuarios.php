<?php
require_once __DIR__ . '/../controllers/loginController.php';
require_once __DIR__ . '/../models/Usuario.php';

// Verificar que el usuario esté autenticado y sea admin
$loginController->verificarAcceso('admin');

// Obtener información del usuario actual
$usuario = $loginController->obtenerUsuarioActual();

// Verificar expiración de sesión
$loginController->verificarExpiracionSesion();

// Procesar logout si se solicita
if (isset($_GET['logout']) && $_GET['logout'] == '1') {
    $loginController->cerrarSesion();
    header('Location: /ofm/public/evara/page-login-register.php');
    exit;
}

// Obtener todos los usuarios
$usuarioModel = new Usuario($pdo);
$usuarios = $usuarioModel->obtenerTodos();

// Obtener estadísticas
$estadisticas = $usuarioModel->obtenerEstadisticas();

// Configurar título de la página
$pageTitle = 'Gestión de Usuarios - Admin OFM';
?>

<?php require_once 'includes/header.php'; ?>
<?php require_once 'includes/navbar.php'; ?>
<?php require_once 'includes/sidebar.php'; ?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Gestión de Usuarios</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="../dashboard.php">Inicio</a></li>
                        <li class="breadcrumb-item active">Usuarios</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <!-- Stats Cards -->
            <div class="row mb-4">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3><?= $estadisticas['total_usuarios'] ?? 0 ?></h3>
                            <p>Total Usuarios</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <a href="#" class="small-box-footer">
                            Más info <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3><?= $estadisticas['usuarios_activos'] ?? 0 ?></h3>
                            <p>Usuarios Activos</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-user-check"></i>
                        </div>
                        <a href="#" class="small-box-footer">
                            Más info <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3><?= $estadisticas['total_socios'] ?? 0 ?></h3>
                            <p>Total Socios</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-store"></i>
                        </div>
                        <a href="../comercios/" class="small-box-footer">
                            Más info <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3><?= $estadisticas['total_clientes'] ?? 0 ?></h3>
                            <p>Total Clientes</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <a href="#" class="small-box-footer">
                            Más info <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Header Actions -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Lista de Usuarios</h2>
                <a href="crear-usuario.php" class="btn btn-success">
                    <i class="fas fa-plus me-2"></i>Crear Usuario
                </a>
            </div>

            <!-- Users Table -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Usuarios del Sistema</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($usuarios)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No hay usuarios registrados</h5>
                            <p class="text-muted">Comienza creando el primer usuario del sistema</p>
                            <a href="crear-usuario.php" class="btn btn-success">
                                <i class="fas fa-plus me-2"></i>Crear Primer Usuario
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover" id="usuariosTable">
                                <thead>
                                    <tr>
                                        <th>Avatar</th>
                                        <th>Información</th>
                                        <th>Contacto</th>
                                        <th>Rol</th>
                                        <th>Estado</th>
                                        <th>Fecha Registro</th>
                                        <th>Último Acceso</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($usuarios as $user): ?>
                                        <tr>
                                            <td>
                                                <div class="user-avatar">
                                                    <?= strtoupper(substr($user['nombre'], 0, 1)) ?>
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    <strong><?= htmlspecialchars($user['nombre'] . ' ' . $user['apellido']) ?></strong>
                                                    <br><small class="text-muted">ID: #<?= $user['id'] ?></small>
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    <i class="fas fa-envelope me-1"></i>
                                                    <small><?= htmlspecialchars($user['email']) ?></small>
                                                    <?php if ($user['telefono']): ?>
                                                        <br><i class="fas fa-phone me-1"></i>
                                                        <small><?= htmlspecialchars($user['telefono']) ?></small>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td>
                                                <?php
                                                $roleClass = '';
                                                $roleText = '';
                                                switch ($user['rol']) {
                                                    case 'admin': $roleClass = 'role-admin'; $roleText = 'Admin'; break;
                                                    case 'socio': $roleClass = 'role-socio'; $roleText = 'Socio'; break;
                                                    case 'cliente': $roleClass = 'role-cliente'; $roleText = 'Cliente'; break;
                                                }
                                                ?>
                                                <span class="badge <?= $roleClass ?>"><?= $roleText ?></span>
                                            </td>
                                            <td>
                                                <?php if ($user['activo']): ?>
                                                    <span class="status-badge status-active">Activo</span>
                                                <?php else: ?>
                                                    <span class="status-badge status-inactive">Inactivo</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    <?= date('d/m/Y', strtotime($user['creado_en'])) ?>
                                                </small>
                                            </td>
                                            <td>
                                                <?php if ($user['ultimo_acceso']): ?>
                                                    <small class="text-muted">
                                                        <?= date('d/m/Y H:i', strtotime($user['ultimo_acceso'])) ?>
                                                    </small>
                                                <?php else: ?>
                                                    <small class="text-muted">Nunca</small>
                                                <?php endif; ?>
                                            </td>
                                            <td class="action-buttons">
                                                <a href="ver-usuario.php?id=<?= $user['id'] ?>" 
                                                   class="btn btn-sm btn-outline-info" 
                                                   title="Ver Detalles">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="editar-usuario.php?id=<?= $user['id'] ?>" 
                                                   class="btn btn-sm btn-outline-primary" 
                                                   title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button class="btn btn-sm btn-outline-warning" 
                                                        onclick="toggleEstado(<?= $user['id'] ?>, <?= $user['activo'] ? 0 : 1 ?>)"
                                                        title="<?= $user['activo'] ? 'Desactivar' : 'Activar' ?>">
                                                    <i class="fas fa-<?= $user['activo'] ? 'eye-slash' : 'eye' ?>"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger" 
                                                        onclick="eliminarUsuario(<?= $user['id'] ?>)"
                                                        title="Eliminar">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
</div>

<?php 
$additionalCSS = "
<style>
    .status-badge {
        font-size: 0.8rem;
        padding: 6px 12px;
        border-radius: 20px;
    }
    .status-active { background-color: #d4edda; color: #155724; }
    .status-inactive { background-color: #f8d7da; color: #721c24; }
    .role-badge {
        font-size: 0.75rem;
        padding: 4px 8px;
        border-radius: 12px;
    }
    .role-admin { background-color: #dc3545; color: white; }
    .role-socio { background-color: #28a745; color: white; }
    .role-cliente { background-color: #17a2b8; color: white; }
    .user-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(45deg, #007bff, #6610f2);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
    }
    .action-buttons .btn {
        margin: 2px;
    }
</style>
";

$additionalJS = "
<script>
    $(document).ready(function() {
        // Inicializar DataTable
        $('#usuariosTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json'
            },
            order: [[5, 'desc']], // Ordenar por fecha de registro descendente
            pageLength: 15,
            responsive: true
        });
    });

    // Función para cambiar estado del usuario
    function toggleEstado(userId, nuevoEstado) {
        const estadoTexto = nuevoEstado ? 'activar' : 'desactivar';
        
        if (confirm(`¿Estás seguro de que quieres ${estadoTexto} este usuario?`)) {
            // Aquí iría la lógica AJAX para cambiar el estado
            alert('Función de cambio de estado en desarrollo');
        }
    }

    // Función para eliminar usuario
    function eliminarUsuario(userId) {
        if (confirm('¿Estás seguro de que quieres eliminar este usuario? Esta acción no se puede deshacer.')) {
            // Aquí iría la lógica AJAX para eliminar
            alert('Función de eliminación en desarrollo');
        }
    }
</script>
";
?>

<?php require_once 'includes/footer.php'; ?>
