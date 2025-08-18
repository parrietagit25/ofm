<?php
require_once __DIR__ . '/../../controllers/loginController.php';
require_once __DIR__ . '/../../models/Comercio.php';

// Verificar que el usuario esté autenticado y sea admin
$loginController->verificarAcceso('admin');

// Obtener información del usuario actual
$usuario = $loginController->obtenerUsuarioActual();

// Verificar expiración de sesión
$loginController->verificarExpiracionSesion();

// Obtener ID del comercio a editar
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

// Procesar formulario
$mensaje = '';
$tipoMensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre_comercio = trim($_POST['nombre_comercio'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $direccion = trim($_POST['direccion'] ?? '');
    $telefono_comercio = trim($_POST['telefono_comercio'] ?? '');
    $email_comercio = trim($_POST['email_comercio'] ?? '');
    $activo = isset($_POST['activo']) ? 1 : 0;
    
    // Validaciones
    if (empty($nombre_comercio)) {
        $mensaje = 'El nombre del comercio es obligatorio';
        $tipoMensaje = 'danger';
    } elseif (empty($direccion)) {
        $mensaje = 'La dirección del comercio es obligatoria';
        $tipoMensaje = 'danger';
    } else {
        // Verificar si ya existe un comercio con el mismo nombre para este usuario
        if ($comercioModel->verificarNombreDuplicado($comercio['usuario_socio_id'], $nombre_comercio, $id)) {
            $mensaje = 'Este usuario socio ya tiene un comercio con ese nombre';
            $tipoMensaje = 'danger';
        } else {
            // Actualizar comercio
            $datosActualizar = [
                'nombre_comercio' => $nombre_comercio,
                'descripcion' => $descripcion,
                'direccion' => $direccion,
                'telefono_comercio' => $telefono_comercio,
                'email_comercio' => $email_comercio,
                'activo' => $activo
            ];
            
            $resultado = $comercioModel->actualizar($id, $datosActualizar);
            
            if ($resultado['success']) {
                $mensaje = 'Comercio actualizado exitosamente';
                $tipoMensaje = 'success';
                
                // Actualizar datos en la variable local
                $comercio = array_merge($comercio, $datosActualizar);
            } else {
                $mensaje = $resultado['message'];
                $tipoMensaje = 'danger';
            }
        }
    }
}

// Configurar título de la página
$pageTitle = 'Editar Comercio - Admin OFM';
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
                    <h1 class="m-0">Editar Comercio</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="../dashboard.php">Inicio</a></li>
                        <li class="breadcrumb-item"><a href="index.php">Comercios</a></li>
                        <li class="breadcrumb-item active">Editar Comercio</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <!-- Mensaje de resultado -->
            <?php if ($mensaje): ?>
                <div class="alert alert-<?= $tipoMensaje ?> alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($mensaje) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="row">
                <!-- Formulario -->
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-store me-2"></i>Editar Información del Comercio
                            </h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" id="editarComercioForm">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="nombre_comercio" class="form-label">Nombre del Comercio *</label>
                                            <input type="text" class="form-control" id="nombre_comercio" name="nombre_comercio" 
                                                   value="<?= htmlspecialchars($comercio['nombre_comercio']) ?>" required>
                                            <div class="form-text">Nombre del comercio</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="descripcion" class="form-label">Descripción</label>
                                            <textarea class="form-control" id="descripcion" name="descripcion" rows="2"><?= htmlspecialchars($comercio['descripcion']) ?></textarea>
                                            <div class="form-text">Descripción del comercio (opcional)</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="direccion" class="form-label">Dirección *</label>
                                            <input type="text" class="form-control" id="direccion" name="direccion" 
                                                   value="<?= htmlspecialchars($comercio['direccion']) ?>" required>
                                            <div class="form-text">Dirección del comercio</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="telefono_comercio" class="form-label">Teléfono del Comercio</label>
                                            <input type="tel" class="form-control" id="telefono_comercio" name="telefono_comercio" 
                                                   value="<?= htmlspecialchars($comercio['telefono_comercio']) ?>">
                                            <div class="form-text">Teléfono de contacto del comercio (opcional)</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="email_comercio" class="form-label">Email del Comercio</label>
                                            <input type="email" class="form-control" id="email_comercio" name="email_comercio" 
                                                   value="<?= htmlspecialchars($comercio['email_comercio']) ?>">
                                            <div class="form-text">Email de contacto del comercio (opcional)</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="activo" name="activo" 
                                                       <?= $comercio['activo'] ? 'checked' : '' ?>>
                                                <label class="form-check-label" for="activo">
                                                    Comercio Activo
                                                </label>
                                                <div class="form-text">Desmarca para desactivar el comercio</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Botones de acción -->
                                <div class="d-flex justify-content-between mt-4">
                                    <a href="index.php" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left me-2"></i>Volver
                                    </a>
                                    <div>
                                        <a href="ver.php?id=<?= $comercio['id'] ?>" class="btn btn-info me-2">
                                            <i class="fas fa-eye me-2"></i>Ver Detalles
                                        </a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-2"></i>Guardar Cambios
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Sidebar con Información -->
                <div class="col-md-4">
                    <!-- Información del Comercio -->
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="fas fa-info-circle me-2"></i>Información del Comercio
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="text-center mb-3">
                                <div class="comercio-avatar-medium">
                                    <?= strtoupper(substr($comercio['nombre_comercio'], 0, 1)) ?>
                                </div>
                                <h6 class="mt-2"><?= htmlspecialchars($comercio['nombre_comercio']) ?></h6>
                                <p class="text-muted small">ID: #<?= $comercio['id'] ?></p>
                                <p class="card-text">
                                    <span class="badge role-socio">Socio/Comercio</span>
                                </p>
                            </div>
                            
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
                                <li class="mb-2">
                                    <i class="fas fa-clock me-2"></i>
                                    <strong>Estado:</strong> 
                                    <?php if ($comercio['activo']): ?>
                                        <span class="status-badge status-active">Activo</span>
                                    <?php else: ?>
                                        <span class="status-badge status-inactive">Inactivo</span>
                                    <?php endif; ?>
                                </li>
                                <li>
                                    <i class="fas fa-map-marker-alt me-2"></i>
                                    <strong>Ubicación:</strong> <?= htmlspecialchars($comercio['direccion']) ?>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Notas Importantes -->
                    <div class="card mt-3 bg-light">
                        <div class="card-body">
                            <h6 class="card-title text-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>Notas Importantes
                            </h6>
                            <ul class="list-unstyled small">
                                <li class="mb-2">
                                    • Los cambios se aplican inmediatamente
                                </li>
                                <li class="mb-2">
                                    • Solo cambia la contraseña si especificas una nueva
                                </li>
                                <li>
                                    • El email debe ser único en el sistema
                                </li>
                            </ul>
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
                                <a href="ver.php?id=<?= $comercio['id'] ?>" class="btn btn-info btn-sm">
                                    <i class="fas fa-eye me-2"></i>Ver Detalles
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
    .comercio-avatar-medium {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: linear-gradient(45deg, #28a745, #20c997);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        font-size: 1.5rem;
        margin: 0 auto;
    }
</style>
";

$additionalJS = "
<script>
    // Validación del formulario
    document.getElementById('editarComercioForm').addEventListener('submit', function(e) {
        const nuevaClave = document.getElementById('nueva_clave').value;
        const confirmarClave = document.getElementById('confirmar_clave').value;
        
        if (nuevaClave && nuevaClave !== confirmarClave) {
            e.preventDefault();
            alert('Las contraseñas no coinciden');
            return false;
        }
        
        if (nuevaClave && nuevaClave.length < 6) {
            e.preventDefault();
            alert('La nueva contraseña debe tener al menos 6 caracteres');
            return false;
        }
    });

    // Validación en tiempo real de la contraseña
    document.getElementById('confirmar_clave').addEventListener('input', function() {
        const nuevaClave = document.getElementById('nueva_clave').value;
        const confirmarClave = this.value;
        
        if (confirmarClave && nuevaClave !== confirmarClave) {
            this.setCustomValidity('Las contraseñas no coinciden');
        } else {
            this.setCustomValidity('');
        }
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
