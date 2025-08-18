<?php
require_once __DIR__ . '/../controllers/loginController.php';
require_once __DIR__ . '/../models/Usuario.php';

// Verificar que el usuario esté autenticado y sea admin
$loginController->verificarAcceso('admin');

// Obtener información del usuario actual
$usuario = $loginController->obtenerUsuarioActual();

// Verificar expiración de sesión
$loginController->verificarExpiracionSesion();

// Obtener ID del usuario a ver
$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: usuarios.php');
    exit;
}

// Obtener información del usuario
$usuarioModel = new Usuario($pdo);
$usuarioVer = $usuarioModel->obtenerPorId($id);

if (!$usuarioVer) {
    header('Location: usuarios.php');
    exit;
}

// Configurar título de la página
$pageTitle = 'Ver Usuario - Admin OFM';
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
                    <h1 class="m-0">Ver Usuario</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="dashboard.php">Inicio</a></li>
                        <li class="breadcrumb-item"><a href="usuarios.php">Usuarios</a></li>
                        <li class="breadcrumb-item active">Ver Usuario</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <!-- Header Actions -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Detalles del Usuario</h2>
                <div>
                    <a href="editar-usuario.php?id=<?= $usuarioVer['id'] ?>" class="btn btn-primary me-2">
                        <i class="fas fa-edit me-2"></i>Editar
                    </a>
                    <a href="usuarios.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Volver
                    </a>
                </div>
            </div>

            <div class="row">
                <!-- Información Principal -->
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-user me-2"></i>Información Personal
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Nombre:</label>
                                        <p class="form-control-plaintext"><?= htmlspecialchars($usuarioVer['nombre']) ?></p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Apellido:</label>
                                        <p class="form-control-plaintext"><?= htmlspecialchars($usuarioVer['apellido']) ?></p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Email:</label>
                                        <p class="form-control-plaintext">
                                            <i class="fas fa-envelope me-2"></i>
                                            <?= htmlspecialchars($usuarioVer['email']) ?>
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Teléfono:</label>
                                        <p class="form-control-plaintext">
                                            <?php if ($usuarioVer['telefono']): ?>
                                                <i class="fas fa-phone me-2"></i>
                                                <?= htmlspecialchars($usuarioVer['telefono']) ?>
                                            <?php else: ?>
                                                <span class="text-muted">No especificado</span>
                                            <?php endif; ?>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Rol:</label>
                                        <p class="form-control-plaintext">
                                            <?php
                                            $roleClass = '';
                                            $roleText = '';
                                            switch ($usuarioVer['rol']) {
                                                case 'admin': $roleClass = 'role-admin'; $roleText = 'Administrador'; break;
                                                case 'socio': $roleClass = 'role-socio'; $roleText = 'Socio'; break;
                                                case 'cliente': $roleClass = 'role-cliente'; $roleText = 'Cliente'; break;
                                            }
                                            ?>
                                            <span class="badge <?= $roleClass ?>"><?= $roleText ?></span>
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Estado:</label>
                                        <p class="form-control-plaintext">
                                            <?php if ($usuarioVer['activo']): ?>
                                                <span class="status-badge status-active">Activo</span>
                                            <?php else: ?>
                                                <span class="status-badge status-inactive">Inactivo</span>
                                            <?php endif; ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Información del Sistema -->
                    <div class="card mt-4">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-cog me-2"></i>Información del Sistema
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">ID de Usuario:</label>
                                        <p class="form-control-plaintext">#<?= $usuarioVer['id'] ?></p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Fecha de Registro:</label>
                                        <p class="form-control-plaintext">
                                            <i class="fas fa-calendar me-2"></i>
                                            <?= date('d/m/Y H:i', strtotime($usuarioVer['creado_en'])) ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Último Acceso:</label>
                                        <p class="form-control-plaintext">
                                            <?php if ($usuarioVer['ultimo_acceso']): ?>
                                                <i class="fas fa-clock me-2"></i>
                                                <?= date('d/m/Y H:i', strtotime($usuarioVer['ultimo_acceso'])) ?>
                                            <?php else: ?>
                                                <span class="text-muted">Nunca ha accedido</span>
                                            <?php endif; ?>
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Última Actualización:</label>
                                        <p class="form-control-plaintext">
                                            <i class="fas fa-edit me-2"></i>
                                            <?= date('d/m/Y H:i', strtotime($usuarioVer['actualizado_en'])) ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar con Avatar y Acciones -->
                <div class="col-md-4">
                    <!-- Avatar del Usuario -->
                    <div class="card text-center">
                        <div class="card-body">
                            <div class="user-avatar-large mb-3">
                                <?= strtoupper(substr($usuarioVer['nombre'], 0, 1)) ?>
                            </div>
                            <h5 class="card-title"><?= htmlspecialchars($usuarioVer['nombre'] . ' ' . $usuarioVer['apellido']) ?></h5>
                            <p class="card-text text-muted">ID: #<?= $usuarioVer['id'] ?></p>
                        </div>
                    </div>

                    <!-- Acciones Rápidas -->
                    <div class="card mt-3">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="fas fa-tools me-2"></i>Acciones Rápidas
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="editar-usuario.php?id=<?= $usuarioVer['id'] ?>" class="btn btn-primary btn-sm">
                                    <i class="fas fa-edit me-2"></i>Editar Usuario
                                </a>
                                
                                <?php if ($usuarioVer['activo']): ?>
                                    <button class="btn btn-warning btn-sm" onclick="toggleEstado(<?= $usuarioVer['id'] ?>, 0)">
                                        <i class="fas fa-eye-slash me-2"></i>Desactivar
                                    </button>
                                <?php else: ?>
                                    <button class="btn btn-success btn-sm" onclick="toggleEstado(<?= $usuarioVer['id'] ?>, 1)">
                                        <i class="fas fa-eye me-2"></i>Activar
                                    </button>
                                <?php endif; ?>
                                
                                <button class="btn btn-danger btn-sm" onclick="eliminarUsuario(<?= $usuarioVer['id'] ?>)">
                                    <i class="fas fa-trash me-2"></i>Eliminar
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Información Adicional -->
                    <div class="card mt-3">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="fas fa-info-circle me-2"></i>Información Adicional
                            </h6>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled small">
                                <li class="mb-2">
                                    <i class="fas fa-user-tag me-2"></i>
                                    <strong>Rol:</strong> <?= ucfirst($usuarioVer['rol']) ?>
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-calendar-check me-2"></i>
                                    <strong>Registrado:</strong> <?= date('d/m/Y', strtotime($usuarioVer['creado_en'])) ?>
                                </li>
                                <?php if ($usuarioVer['ultimo_acceso']): ?>
                                    <li>
                                        <i class="fas fa-sign-in-alt me-2"></i>
                                        <strong>Último acceso:</strong> <?= date('d/m/Y', strtotime($usuarioVer['ultimo_acceso'])) ?>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>
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
    .user-avatar-large {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: linear-gradient(45deg, #007bff, #6610f2);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        font-size: 2rem;
        margin: 0 auto;
    }
    .form-control-plaintext {
        padding: 0.375rem 0;
        margin-bottom: 0;
        color: #212529;
        background-color: transparent;
        border: solid transparent;
        border-width: 1px 0;
    }
</style>
";

$additionalJS = "
<script>
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
