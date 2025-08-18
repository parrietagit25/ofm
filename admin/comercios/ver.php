<?php
require_once __DIR__ . '/../../controllers/loginController.php';
require_once __DIR__ . '/../../models/Comercio.php';

// Verificar que el usuario esté autenticado y sea admin
$loginController->verificarAcceso('admin');

// Obtener información del usuario actual
$usuario = $loginController->obtenerUsuarioActual();

// Verificar expiración de sesión
$loginController->verificarExpiracionSesion();

// Obtener ID del comercio a ver
$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: index.php');
    exit;
}

// Obtener información del comercio
$comercioModel = new Comercio($pdo);
$comercio = $comercioModel->obtenerPorId($id);

if (!$comercio) {
    header('Location: index.php');
    exit;
}

// Configurar título de la página
$pageTitle = 'Ver Comercio - Admin OFM';
?>

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
                    <h1 class="m-0">Ver Comercio</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="../dashboard.php">Inicio</a></li>
                        <li class="breadcrumb-item"><a href="index.php">Comercios</a></li>
                        <li class="breadcrumb-item active">Ver Comercio</li>
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
                <h2>Detalles del Comercio</h2>
                <div>
                    <a href="editar.php?id=<?= $comercio['id'] ?>" class="btn btn-primary me-2">
                        <i class="fas fa-edit me-2"></i>Editar
                    </a>
                    <a href="index.php" class="btn btn-secondary">
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
                                <i class="fas fa-store me-2"></i>Información del Comercio
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Nombre del Comercio:</label>
                                        <p class="form-control-plaintext"><?= htmlspecialchars($comercio['nombre_comercio']) ?></p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Descripción:</label>
                                        <p class="form-control-plaintext">
                                            <?= $comercio['descripcion'] ? htmlspecialchars($comercio['descripcion']) : '<span class="text-muted">Sin descripción</span>' ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Dirección:</label>
                                        <p class="form-control-plaintext">
                                            <i class="fas fa-map-marker-alt me-2"></i>
                                            <?= htmlspecialchars($comercio['direccion']) ?>
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Teléfono del Comercio:</label>
                                        <p class="form-control-plaintext">
                                            <?php if ($comercio['telefono_comercio']): ?>
                                                <i class="fas fa-phone me-2"></i>
                                                <?= htmlspecialchars($comercio['telefono_comercio']) ?>
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
                                        <label class="form-label fw-bold">Email del Comercio:</label>
                                        <p class="form-control-plaintext">
                                            <?php if ($comercio['email_comercio']): ?>
                                                <i class="fas fa-envelope me-2"></i>
                                                <?= htmlspecialchars($comercio['email_comercio']) ?>
                                            <?php else: ?>
                                                <span class="text-muted">No especificado</span>
                                            <?php endif; ?>
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Estado:</label>
                                        <p class="form-control-plaintext">
                                            <?php if ($comercio['activo']): ?>
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

                    <!-- Información del Usuario Socio -->
                    <div class="card mt-4">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-user me-2"></i>Usuario Socio Asociado
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Nombre Completo:</label>
                                        <p class="form-control-plaintext">
                                            <?= htmlspecialchars($comercio['nombre'] . ' ' . $comercio['apellido']) ?>
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Email del Usuario:</label>
                                        <p class="form-control-plaintext">
                                            <i class="fas fa-envelope me-2"></i>
                                            <?= htmlspecialchars($comercio['email_usuario']) ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Teléfono del Usuario:</label>
                                        <p class="form-control-plaintext">
                                            <?php if ($comercio['telefono_usuario']): ?>
                                                <i class="fas fa-phone me-2"></i>
                                                <?= htmlspecialchars($comercio['telefono_usuario']) ?>
                                            <?php else: ?>
                                                <span class="text-muted">No especificado</span>
                                            <?php endif; ?>
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Rol:</label>
                                        <p class="form-control-plaintext">
                                            <span class="badge role-socio">Socio/Comercio</span>
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
                                        <label class="form-label fw-bold">ID del Comercio:</label>
                                        <p class="form-control-plaintext">#<?= $comercio['id'] ?></p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Fecha de Registro:</label>
                                        <p class="form-control-plaintext">
                                            <i class="fas fa-calendar me-2"></i>
                                            <?= date('d/m/Y H:i', strtotime($comercio['creado_en'])) ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Última Actualización:</label>
                                        <p class="form-control-plaintext">
                                            <i class="fas fa-edit me-2"></i>
                                            <?= date('d/m/Y H:i', strtotime($comercio['actualizado_en'])) ?>
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Usuario Socio ID:</label>
                                        <p class="form-control-plaintext">#<?= $comercio['usuario_socio_id'] ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar con Avatar y Acciones -->
                <div class="col-md-4">
                    <!-- Avatar del Comercio -->
                    <div class="card text-center">
                        <div class="card-body">
                            <div class="comercio-avatar-large mb-3">
                                <?= strtoupper(substr($comercio['nombre_comercio'], 0, 1)) ?>
                            </div>
                            <h5 class="card-title"><?= htmlspecialchars($comercio['nombre_comercio']) ?></h5>
                            <p class="card-text text-muted">ID: #<?= $comercio['id'] ?></p>
                            <p class="card-text">
                                <span class="badge role-socio">Comercio</span>
                            </p>
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
                                <a href="editar.php?id=<?= $comercio['id'] ?>" class="btn btn-primary btn-sm">
                                    <i class="fas fa-edit me-2"></i>Editar Comercio
                                </a>
                                
                                <?php if ($comercio['activo']): ?>
                                    <button class="btn btn-warning btn-sm" onclick="toggleEstado(<?= $comercio['id'] ?>, 0)">
                                        <i class="fas fa-eye-slash me-2"></i>Desactivar
                                    </button>
                                <?php else: ?>
                                    <button class="btn btn-success btn-sm" onclick="toggleEstado(<?= $comercio['id'] ?>, 1)">
                                        <i class="fas fa-eye me-2"></i>Activar
                                    </button>
                                <?php endif; ?>
                                
                                <button class="btn btn-danger btn-sm" onclick="eliminarComercio(<?= $comercio['id'] ?>)">
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
                                    <i class="fas fa-store me-2"></i>
                                    <strong>Tipo:</strong> Comercio
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-user me-2"></i>
                                    <strong>Propietario:</strong> <?= htmlspecialchars($comercio['nombre'] . ' ' . $comercio['apellido']) ?>
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-calendar-check me-2"></i>
                                    <strong>Registrado:</strong> <?= date('d/m/Y', strtotime($comercio['creado_en'])) ?>
                                </li>
                                <li>
                                    <i class="fas fa-map-marker-alt me-2"></i>
                                    <strong>Ubicación:</strong> <?= htmlspecialchars($comercio['direccion']) ?>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Características del Comercio -->
                    <div class="card mt-3">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="fas fa-star me-2"></i>Características
                            </h6>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled small">
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-2"></i>
                                    Acceso al panel de comercio
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-2"></i>
                                    Gestión de productos
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-2"></i>
                                    Control de ventas
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-2"></i>
                                    Reportes de negocio
                                </li>
                                <li>
                                    <i class="fas fa-check text-success me-2"></i>
                                    Panel de inventario
                                </li>
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
    .role-socio { background-color: #28a745; color: white; }
    .comercio-avatar-large {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: linear-gradient(45deg, #28a745, #20c997);
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
                    window.location.href = 'index.php';
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
