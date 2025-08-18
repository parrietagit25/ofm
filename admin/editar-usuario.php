<?php
require_once __DIR__ . '/../controllers/loginController.php';
require_once __DIR__ . '/../models/Usuario.php';

// Verificar que el usuario esté autenticado y sea admin
$loginController->verificarAcceso('admin');

// Obtener información del usuario actual
$usuario = $loginController->verificarAcceso('admin');

// Verificar expiración de sesión
$loginController->verificarExpiracionSesion();

// Obtener ID del usuario a editar
$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: usuarios.php');
    exit;
}

// Obtener información del usuario
$usuarioModel = new Usuario($pdo);
$usuarioEditar = $usuarioModel->obtenerPorId($id);

if (!$usuarioEditar) {
    header('Location: usuarios.php');
    exit;
}

// Procesar formulario
$mensaje = '';
$tipoMensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $apellido = trim($_POST['apellido'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $rol = $_POST['rol'] ?? '';
    $activo = isset($_POST['activo']) ? 1 : 0;
    $nueva_clave = $_POST['nueva_clave'] ?? '';
    $confirmar_clave = $_POST['confirmar_clave'] ?? '';
    
    // Validaciones
    if (empty($nombre) || empty($apellido)) {
        $mensaje = 'El nombre y apellido son obligatorios';
        $tipoMensaje = 'danger';
    } elseif (empty($email)) {
        $mensaje = 'El email es obligatorio';
        $tipoMensaje = 'danger';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $mensaje = 'El email no tiene un formato válido';
        $tipoMensaje = 'danger';
    } elseif (empty($rol)) {
        $mensaje = 'Debe seleccionar un rol';
        $tipoMensaje = 'danger';
    } elseif ($nueva_clave && strlen($nueva_clave) < 6) {
        $mensaje = 'La nueva contraseña debe tener al menos 6 caracteres';
        $tipoMensaje = 'danger';
    } elseif ($nueva_clave && $nueva_clave !== $confirmar_clave) {
        $mensaje = 'Las contraseñas no coinciden';
        $tipoMensaje = 'danger';
    } else {
        // Verificar si el email ya existe en otro usuario
        $usuarioExistente = $usuarioModel->obtenerPorEmail($email);
        if ($usuarioExistente && $usuarioExistente['id'] != $id) {
            $mensaje = 'Ya existe otro usuario con ese email';
            $tipoMensaje = 'danger';
        } else {
            // Actualizar usuario
            $datosActualizar = [
                'nombre' => $nombre,
                'apellido' => $apellido,
                'email' => $email,
                'telefono' => $telefono,
                'rol' => $rol,
                'activo' => $activo
            ];
            
            if ($nueva_clave) {
                $datosActualizar['clave'] = $nueva_clave;
            }
            
            $resultado = $usuarioModel->actualizar($id, $datosActualizar);
            
            if ($resultado['success']) {
                $mensaje = 'Usuario actualizado exitosamente';
                $tipoMensaje = 'success';
                
                // Actualizar datos en la variable local
                $usuarioEditar = array_merge($usuarioEditar, $datosActualizar);
            } else {
                $mensaje = $resultado['message'];
                $tipoMensaje = 'danger';
            }
        }
    }
}

// Configurar título de la página
$pageTitle = 'Editar Usuario - Admin OFM';
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
                    <h1 class="m-0">Editar Usuario</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="dashboard.php">Inicio</a></li>
                        <li class="breadcrumb-item"><a href="usuarios.php">Usuarios</a></li>
                        <li class="breadcrumb-item active">Editar Usuario</li>
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
                                <i class="fas fa-user-edit me-2"></i>Editar Información del Usuario
                            </h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" id="editarUsuarioForm">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="nombre" class="form-label">Nombre *</label>
                                            <input type="text" class="form-control" id="nombre" name="nombre" 
                                                   value="<?= htmlspecialchars($usuarioEditar['nombre']) ?>" required>
                                            <div class="form-text">Nombre del usuario</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="apellido" class="form-label">Apellido *</label>
                                            <input type="text" class="form-control" id="apellido" name="apellido" 
                                                   value="<?= htmlspecialchars($usuarioEditar['apellido']) ?>" required>
                                            <div class="form-text">Apellido del usuario</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="email" class="form-label">Email *</label>
                                            <input type="email" class="form-control" id="email" name="email" 
                                                   value="<?= htmlspecialchars($usuarioEditar['email']) ?>" required>
                                            <div class="form-text">Email único para el acceso</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="telefono" class="form-label">Teléfono</label>
                                            <input type="tel" class="form-control" id="telefono" name="telefono" 
                                                   value="<?= htmlspecialchars($usuarioEditar['telefono']) ?>">
                                            <div class="form-text">Teléfono de contacto (opcional)</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="rol" class="form-label">Rol *</label>
                                            <select class="form-control" id="rol" name="rol" required>
                                                <option value="">Seleccionar rol</option>
                                                <option value="admin" <?= $usuarioEditar['rol'] === 'admin' ? 'selected' : '' ?>>Administrador</option>
                                                <option value="socio" <?= $usuarioEditar['rol'] === 'socio' ? 'selected' : '' ?>>Socio</option>
                                                <option value="cliente" <?= $usuarioEditar['rol'] === 'cliente' ? 'selected' : '' ?>>Cliente</option>
                                            </select>
                                            <div class="form-text">Rol del usuario en el sistema</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <div class="form-check mt-4">
                                                <input class="form-check-input" type="checkbox" id="activo" name="activo" 
                                                       <?= $usuarioEditar['activo'] ? 'checked' : '' ?>>
                                                <label class="form-check-label" for="activo">
                                                    Usuario Activo
                                                </label>
                                                <div class="form-text">Desmarca para desactivar el usuario</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <hr class="my-4">

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="nueva_clave" class="form-label">Nueva Contraseña</label>
                                            <input type="password" class="form-control" id="nueva_clave" name="nueva_clave" 
                                                   minlength="6">
                                            <div class="form-text">Deja en blanco para mantener la actual</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="confirmar_clave" class="form-label">Confirmar Nueva Contraseña</label>
                                            <input type="password" class="form-control" id="confirmar_clave" name="confirmar_clave" 
                                                   minlength="6">
                                            <div class="form-text">Repite la nueva contraseña</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Botones de acción -->
                                <div class="d-flex justify-content-between mt-4">
                                    <a href="usuarios.php" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left me-2"></i>Volver
                                    </a>
                                    <div>
                                        <a href="ver-usuario.php?id=<?= $usuarioEditar['id'] ?>" class="btn btn-info me-2">
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
                    <!-- Información del Usuario -->
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="fas fa-info-circle me-2"></i>Información del Usuario
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="text-center mb-3">
                                <div class="user-avatar-medium">
                                    <?= strtoupper(substr($usuarioEditar['nombre'], 0, 1)) ?>
                                </div>
                                <h6 class="mt-2"><?= htmlspecialchars($usuarioEditar['nombre'] . ' ' . $usuarioEditar['apellido']) ?></h6>
                                <p class="text-muted small">ID: #<?= $usuarioEditar['id'] ?></p>
                            </div>
                            
                            <ul class="list-unstyled small">
                                <li class="mb-2">
                                    <i class="fas fa-user-tag me-2"></i>
                                    <strong>Rol:</strong> 
                                    <?php
                                    $roleClass = '';
                                    $roleText = '';
                                    switch ($usuarioEditar['rol']) {
                                        case 'admin': $roleClass = 'role-admin'; $roleText = 'Administrador'; break;
                                        case 'socio': $roleClass = 'role-socio'; $roleText = 'Socio'; break;
                                        case 'cliente': $roleClass = 'role-cliente'; $roleText = 'Cliente'; break;
                                    }
                                    ?>
                                    <span class="badge <?= $roleClass ?>"><?= $roleText ?></span>
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-calendar-check me-2"></i>
                                    <strong>Registrado:</strong> <?= date('d/m/Y', strtotime($usuarioEditar['creado_en'])) ?>
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-clock me-2"></i>
                                    <strong>Estado:</strong> 
                                    <?php if ($usuarioEditar['activo']): ?>
                                        <span class="status-badge status-active">Activo</span>
                                    <?php else: ?>
                                        <span class="status-badge status-inactive">Inactivo</span>
                                    <?php endif; ?>
                                </li>
                                <?php if ($usuarioEditar['ultimo_acceso']): ?>
                                    <li>
                                        <i class="fas fa-sign-in-alt me-2"></i>
                                        <strong>Último acceso:</strong> <?= date('d/m/Y', strtotime($usuarioEditar['ultimo_acceso'])) ?>
                                    </li>
                                <?php endif; ?>
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
                                <a href="ver-usuario.php?id=<?= $usuarioEditar['id'] ?>" class="btn btn-info btn-sm">
                                    <i class="fas fa-eye me-2"></i>Ver Detalles
                                </a>
                                
                                <?php if ($usuarioEditar['activo']): ?>
                                    <button class="btn btn-warning btn-sm" onclick="toggleEstado(<?= $usuarioEditar['id'] ?>, 0)">
                                        <i class="fas fa-eye-slash me-2"></i>Desactivar
                                    </button>
                                <?php else: ?>
                                    <button class="btn btn-success btn-sm" onclick="toggleEstado(<?= $usuarioEditar['id'] ?>, 1)">
                                        <i class="fas fa-eye me-2"></i>Activar
                                    </button>
                                <?php endif; ?>
                                
                                <button class="btn btn-danger btn-sm" onclick="eliminarUsuario(<?= $usuarioEditar['id'] ?>)">
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
    .role-admin { background-color: #dc3545; color: white; }
    .role-socio { background-color: #28a745; color: white; }
    .role-cliente { background-color: #17a2b8; color: white; }
    .user-avatar-medium {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: linear-gradient(45deg, #007bff, #6610f2);
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
    document.getElementById('editarUsuarioForm').addEventListener('submit', function(e) {
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
