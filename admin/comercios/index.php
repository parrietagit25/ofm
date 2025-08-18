<?php
require_once __DIR__ . '/../../controllers/loginController.php';

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

// Obtener todos los comercios usando el modelo Comercio
require_once __DIR__ . '/../../models/Comercio.php';
$comercioModel = new Comercio($pdo);
$comercios = $comercioModel->obtenerTodos();

// Obtener estadísticas de comercios
$estadisticasComercios = $comercioModel->obtenerEstadisticas();

// Obtener estadísticas generales de usuarios
require_once __DIR__ . '/../../models/Usuario.php';
$usuarioModel = new Usuario($pdo);
$estadisticasUsuarios = $usuarioModel->obtenerEstadisticas();
?>

<?php $pageTitle = 'Gestión de Comercios - Admin OFM'; ?>
<?php require_once '../includes/header.php'; ?>
<?php require_once '../includes/navbar.php'; ?>
<?php require_once '../includes/sidebar.php'; ?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Gestión de Comercios</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="../dashboard.php">Inicio</a></li>
                        <li class="breadcrumb-item active">Comercios</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">

        <!-- Main Content -->
        <div class="main-content flex-grow-1">
            <div class="p-4">
                <!-- Top Navbar -->
                <nav class="navbar navbar-expand-lg navbar-light bg-white mb-4 rounded shadow-sm">
                    <div class="container-fluid">
                        <span class="navbar-brand">Gestión de Comercios</span>
                        <div class="navbar-nav ms-auto">
                            <div class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-user-circle me-2"></i><?= htmlspecialchars($usuario['nombre']) ?>
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="../perfil/">Mi Perfil</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="../dashboard.php?logout=1">Cerrar Sesión</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </nav>

                <!-- Stats Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card stats-card">
                            <div class="card-body text-center">
                                <i class="fas fa-store fa-2x mb-2"></i>
                                <h5>Total Comercios</h5>
                                <h3><?= $estadisticasComercios['total_comercios'] ?? 0 ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stats-card">
                            <div class="card-body text-center">
                                <i class="fas fa-store fa-2x mb-2"></i>
                                <h5>Comercios Activos</h5>
                                <h3><?= $estadisticasComercios['comercios_activos'] ?? 0 ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stats-card">
                            <div class="card-body text-center">
                                <i class="fas fa-users fa-2x mb-2"></i>
                                <h5>Usuarios Socio</h5>
                                <h3><?= count($usuarioModel->obtenerPorRol('socio')) ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stats-card">
                            <div class="card-body text-center">
                                <i class="fas fa-chart-line fa-2x mb-2"></i>
                                <h5>Comercios Inactivos</h5>
                                <h3><?= $estadisticasComercios['comercios_inactivos'] ?? 0 ?></h3>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Header Actions -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Gestión de Comercios</h2>
                    <a href="crear.php" class="btn btn-success">
                        <i class="fas fa-plus me-2"></i>Crear Comercio
                    </a>
                </div>

                <!-- Comercios Table -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Lista de Comercios</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($comercios)): ?>
                            <div class="text-center py-5">
                                <i class="fas fa-store fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No hay comercios registrados</h5>
                                <p class="text-muted">Comienza creando el primer comercio del sistema</p>
                                <a href="crear.php" class="btn btn-success">
                                    <i class="fas fa-plus me-2"></i>Crear Primer Comercio
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover" id="comerciosTable">
                                    <thead>
                                        <tr>
                                            <th>Avatar</th>
                                            <th>Información del Comercio</th>
                                            <th>Usuario Socio</th>
                                            <th>Contacto</th>
                                            <th>Estado</th>
                                            <th>Fecha Registro</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($comercios as $comercio): ?>
                                            <tr>
                                                <td>
                                                    <div class="comercio-avatar">
                                                        <?= strtoupper(substr($comercio['nombre_comercio'], 0, 1)) ?>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div>
                                                        <strong><?= htmlspecialchars($comercio['nombre_comercio']) ?></strong>
                                                        <?php if ($comercio['descripcion']): ?>
                                                            <br><small class="text-muted"><?= htmlspecialchars($comercio['descripcion']) ?></small>
                                                        <?php endif; ?>
                                                        <br><small class="text-muted">ID: #<?= $comercio['id'] ?></small>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div>
                                                        <strong><?= htmlspecialchars($comercio['nombre'] . ' ' . $comercio['apellido']) ?></strong>
                                                        <br><small class="text-muted"><?= htmlspecialchars($comercio['email_usuario']) ?></small>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div>
                                                        <?php if ($comercio['direccion']): ?>
                                                            <i class="fas fa-map-marker-alt me-1"></i>
                                                            <small><?= htmlspecialchars($comercio['direccion']) ?></small>
                                                        <?php endif; ?>
                                                        <?php if ($comercio['telefono_comercio']): ?>
                                                            <br><i class="fas fa-phone me-1"></i>
                                                            <small><?= htmlspecialchars($comercio['telefono_comercio']) ?></small>
                                                        <?php endif; ?>
                                                        <?php if ($comercio['email_comercio']): ?>
                                                            <br><i class="fas fa-envelope me-1"></i>
                                                            <small><?= htmlspecialchars($comercio['email_comercio']) ?></small>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                                <td>
                                                    <?php if ($comercio['activo']): ?>
                                                        <span class="status-badge status-active">Activo</span>
                                                    <?php else: ?>
                                                        <span class="status-badge status-inactive">Inactivo</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <small class="text-muted">
                                                        <?= date('d/m/Y', strtotime($comercio['creado_en'])) ?>
                                                    </small>
                                                </td>
                                                <td class="action-buttons">
                                                    <a href="ver.php?id=<?= $comercio['id'] ?>" 
                                                       class="btn btn-sm btn-outline-info" 
                                                       title="Ver Detalles">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="editar.php?id=<?= $comercio['id'] ?>" 
                                                       class="btn btn-sm btn-outline-primary" 
                                                       title="Editar">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button class="btn btn-sm btn-outline-warning" 
                                                            onclick="toggleEstado(<?= $comercio['id'] ?>, <?= $comercio['activo'] ? 0 : 1 ?>)"
                                                            title="<?= $comercio['activo'] ? 'Desactivar' : 'Activar' ?>">
                                                        <i class="fas fa-<?= $comercio['activo'] ? 'eye-slash' : 'eye' ?>"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-danger" 
                                                            onclick="eliminarComercio(<?= $comercio['id'] ?>)"
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
    .comercio-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(45deg, #28a745, #20c997);
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
        $('#comerciosTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json'
            },
            order: [[5, 'desc']], // Ordenar por fecha de registro descendente
            pageLength: 15,
            responsive: true
        });
    });

    // Función para cambiar estado del comercio
    function toggleEstado(comercioId, nuevoEstado) {
        const estadoTexto = nuevoEstado ? 'activar' : 'desactivar';
        
        if (confirm(`¿Estás seguro de que quieres ${estadoTexto} este comercio?`)) {
            fetch('toggle-estado.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    comercio_id: comercioId,
                    activo: nuevoEstado
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al cambiar el estado del comercio');
            });
        }
    }

    // Función para eliminar comercio
    function eliminarComercio(comercioId) {
        if (confirm('¿Estás seguro de que quieres eliminar este comercio? Esta acción no se puede deshacer.')) {
            fetch('eliminar.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    comercio_id: comercioId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al eliminar el comercio');
            });
        }
    }
</script>
";
?>

<?php require_once '../includes/footer.php'; ?>
