<?php
require_once __DIR__ . '/../../controllers/loginController.php';

// Verificar que el usuario esté autenticado y sea admin
$loginController->verificarAcceso('admin');

// Obtener información del usuario actual
$usuario = $loginController->obtenerUsuarioActual();

// Verificar expiración de sesión
$loginController->verificarExpiracionSesion();

// Obtener usuarios tipo socio para el selector
require_once __DIR__ . '/../../models/Usuario.php';
$usuarioModel = new Usuario($pdo);
$usuariosSocio = $usuarioModel->obtenerPorRol('socio');

// Procesar formulario
$mensaje = '';
$tipoMensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario_socio_id = $_POST['usuario_socio_id'] ?? '';
    $nombre_comercio = trim($_POST['nombre_comercio'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $direccion = trim($_POST['direccion'] ?? '');
    $telefono_comercio = trim($_POST['telefono_comercio'] ?? '');
    $email_comercio = trim($_POST['email_comercio'] ?? '');
    $activo = isset($_POST['activo']) ? 1 : 0;
    
    // Validaciones
    if (empty($usuario_socio_id)) {
        $mensaje = 'Debes seleccionar un usuario socio';
        $tipoMensaje = 'danger';
    } elseif (empty($nombre_comercio)) {
        $mensaje = 'El nombre del comercio es obligatorio';
        $tipoMensaje = 'danger';
    } elseif (empty($direccion)) {
        $mensaje = 'La dirección del comercio es obligatoria';
        $tipoMensaje = 'danger';
    } else {
        // Crear el comercio usando el modelo
        require_once __DIR__ . '/../../models/Comercio.php';
        $comercioModel = new Comercio($pdo);
        
        // Verificar si ya existe un comercio con el mismo nombre para este usuario
        if ($comercioModel->verificarNombreDuplicado($usuario_socio_id, $nombre_comercio)) {
            $mensaje = 'Este usuario socio ya tiene un comercio con ese nombre';
            $tipoMensaje = 'danger';
        } else {
            // Crear el comercio
            $resultado = $comercioModel->crear(
                $usuario_socio_id,
                $nombre_comercio,
                $descripcion,
                $direccion,
                $telefono_comercio,
                $email_comercio,
                $activo
            );
            
            if ($resultado['success']) {
                $mensaje = 'Comercio creado exitosamente';
                $tipoMensaje = 'success';
                
                // Limpiar formulario
                $_POST = [];
            } else {
                $mensaje = $resultado['message'];
                $tipoMensaje = 'danger';
            }
        }
    }
}
?>

<?php $pageTitle = 'Crear Comercio - Admin OFM'; ?>
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
                    <h1 class="m-0">Crear Comercio</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="../dashboard.php">Inicio</a></li>
                        <li class="breadcrumb-item"><a href="index.php">Comercios</a></li>
                        <li class="breadcrumb-item active">Crear Comercio</li>
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
                                <i class="fas fa-store me-2"></i>Información del Comercio
                            </h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" id="comercioForm">
                                <!-- Selección de Usuario Socio -->
                                <div class="row mb-4">
                                    <div class="col-12">
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle me-2"></i>
                                            <strong>Importante:</strong> Selecciona un usuario tipo socio existente para asociar este comercio.
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="usuario_socio_id" class="form-label">Usuario Socio *</label>
                                            <select class="form-control" id="usuario_socio_id" name="usuario_socio_id" required>
                                                <option value="">Selecciona un usuario socio</option>
                                                <?php foreach ($usuariosSocio as $socio): ?>
                                                    <option value="<?= $socio['id'] ?>" 
                                                            <?= ($_POST['usuario_socio_id'] ?? '') == $socio['id'] ? 'selected' : '' ?>>
                                                        <?= htmlspecialchars($socio['nombre'] . ' ' . $socio['apellido']) ?> 
                                                        (<?= htmlspecialchars($socio['email']) ?>)
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <div class="form-text">Selecciona el usuario socio que será propietario de este comercio</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="nombre_comercio" class="form-label">Nombre del Comercio *</label>
                                            <input type="text" class="form-control" id="nombre_comercio" name="nombre_comercio" 
                                                   value="<?= htmlspecialchars($_POST['nombre_comercio'] ?? '') ?>" required>
                                            <div class="form-text">Nombre comercial del establecimiento</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="email_comercio" class="form-label">Email del Comercio</label>
                                            <input type="email" class="form-control" id="email_comercio" name="email_comercio" 
                                                   value="<?= htmlspecialchars($_POST['email_comercio'] ?? '') ?>">
                                            <div class="form-text">Email de contacto del comercio (opcional)</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="telefono_comercio" class="form-label">Teléfono del Comercio</label>
                                            <input type="tel" class="form-control" id="telefono_comercio" name="telefono_comercio" 
                                                   value="<?= htmlspecialchars($_POST['telefono_comercio'] ?? '') ?>">
                                            <div class="form-text">Teléfono de contacto del comercio (opcional)</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="activo" name="activo" 
                                                       <?= isset($_POST['activo']) ? 'checked' : '' ?>>
                                                <label class="form-check-label" for="activo">
                                                    Comercio Activo
                                                </label>
                                                <div class="form-text">Marca para activar el comercio inmediatamente</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="direccion" class="form-label">Dirección del Comercio *</label>
                                            <textarea class="form-control" id="direccion" name="direccion" rows="3" required><?= htmlspecialchars($_POST['direccion'] ?? '') ?></textarea>
                                            <div class="form-text">Dirección física del establecimiento</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="descripcion" class="form-label">Descripción del Comercio</label>
                                            <textarea class="form-control" id="descripcion" name="descripcion" rows="4"><?= htmlspecialchars($_POST['descripcion'] ?? '') ?></textarea>
                                            <div class="form-text">Descripción breve del tipo de negocio y productos/servicios</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Botones de acción -->
                                <div class="d-flex justify-content-between mt-4">
                                    <a href="index.php" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left me-2"></i>Volver
                                    </a>
                                    <div>
                                        <button type="reset" class="btn btn-outline-secondary me-2">
                                            <i class="fas fa-undo me-2"></i>Limpiar
                                        </button>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-2"></i>Crear Comercio
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <!-- Información adicional -->
                    <div class="card">
                        <div class="card-body text-center">
                            <i class="fas fa-store comercio-icon mb-3"></i>
                            <h5 class="card-title">Nuevo Comercio</h5>
                            <p class="card-text">Asocia un comercio a un usuario socio existente para que pueda gestionar su negocio.</p>
                        </div>
                    </div>

                    <!-- Usuarios Socio Disponibles -->
                    <div class="card mt-3">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="fas fa-users me-2"></i>Usuarios Socio Disponibles
                            </h6>
                        </div>
                        <div class="card-body">
                            <?php if (empty($usuariosSocio)): ?>
                                <div class="text-center text-muted">
                                    <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                                    <p class="mb-0">No hay usuarios socio disponibles</p>
                                    <small>Primero debes crear usuarios tipo socio</small>
                                </div>
                            <?php else: ?>
                                <div class="list-group list-group-flush">
                                    <?php foreach ($usuariosSocio as $socio): ?>
                                        <div class="list-group-item d-flex align-items-center p-2">
                                            <div class="comercio-avatar-small me-2">
                                                <?= strtoupper(substr($socio['nombre'], 0, 1)) ?>
                                            </div>
                                            <div class="flex-grow-1">
                                                <strong class="d-block"><?= htmlspecialchars($socio['nombre'] . ' ' . $socio['apellido']) ?></strong>
                                                <small class="text-muted"><?= htmlspecialchars($socio['email']) ?></small>
                                            </div>
                                            <?php if ($socio['activo']): ?>
                                                <span class="badge badge-success">Activo</span>
                                            <?php else: ?>
                                                <span class="badge badge-secondary">Inactivo</span>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Características del comercio -->
                    <div class="card mt-3">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="fas fa-info-circle me-2"></i>Características
                            </h6>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled small">
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-2"></i>
                                    Asociado a usuario socio existente
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-2"></i>
                                    Gestión de productos independiente
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-2"></i>
                                    Control de ventas personalizado
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-2"></i>
                                    Reportes de negocio individuales
                                </li>
                                <li>
                                    <i class="fas fa-check text-success me-2"></i>
                                    Panel de inventario separado
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Notas importantes -->
                    <div class="card mt-3 bg-light">
                        <div class="card-body">
                            <h6 class="card-title text-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>Notas Importantes
                            </h6>
                            <ul class="list-unstyled small">
                                <li class="mb-2">
                                    • El comercio se asocia a un usuario socio existente
                                </li>
                                <li class="mb-2">
                                    • Un usuario socio puede tener múltiples comercios
                                </li>
                                <li>
                                    • Los datos del comercio son independientes del usuario
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
    .comercio-icon {
        font-size: 4rem;
        color: #28a745;
    }
    .comercio-avatar-small {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background: linear-gradient(45deg, #28a745, #20c997);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        font-size: 0.8rem;
    }
    .list-group-item {
        border: none;
        border-bottom: 1px solid #dee2e6;
    }
    .list-group-item:last-child {
        border-bottom: none;
    }
</style>
";

$additionalJS = "
<script>
    // Validación del formulario
    document.getElementById('comercioForm').addEventListener('submit', function(e) {
        const usuarioSocioId = document.getElementById('usuario_socio_id').value;
        const nombreComercio = document.getElementById('nombre_comercio').value;
        const direccion = document.getElementById('direccion').value;
        
        if (!usuarioSocioId) {
            e.preventDefault();
            alert('Debes seleccionar un usuario socio');
            return false;
        }
        
        if (!nombreComercio.trim()) {
            e.preventDefault();
            alert('El nombre del comercio es obligatorio');
            return false;
        }
        
        if (!direccion.trim()) {
            e.preventDefault();
            alert('La dirección del comercio es obligatoria');
            return false;
        }
    });

    // Mostrar información del socio seleccionado
    document.getElementById('usuario_socio_id').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (selectedOption.value) {
            console.log('Socio seleccionado:', selectedOption.text);
        }
    });
</script>
";
?>

<?php require_once '../includes/footer.php'; ?>
