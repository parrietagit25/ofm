<?php
require_once __DIR__ . '/../controllers/loginController.php';
require_once __DIR__ . '/../models/Usuario.php';

// Verificar que el usuario esté autenticado y sea admin
$loginController->verificarAcceso('admin');

// Obtener información del usuario actual
$usuario = $loginController->obtenerUsuarioActual();

// Verificar expiración de sesión
$loginController->verificarExpiracionSesion();

// Procesar formulario
$mensaje = '';
$tipoMensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuarioModel = new Usuario($pdo);
    
    $nombre = trim($_POST['nombre'] ?? '');
    $apellido = trim($_POST['apellido'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $rol = $_POST['rol'] ?? '';
    $clave = $_POST['clave'] ?? '';
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
    } elseif (empty($clave)) {
        $mensaje = 'La contraseña es obligatoria';
        $tipoMensaje = 'danger';
    } elseif (strlen($clave) < 6) {
        $mensaje = 'La contraseña debe tener al menos 6 caracteres';
        $tipoMensaje = 'danger';
    } elseif ($clave !== $confirmar_clave) {
        $mensaje = 'Las contraseñas no coinciden';
        $tipoMensaje = 'danger';
    } else {
        // Verificar si el email ya existe
        $usuarioExistente = $usuarioModel->obtenerPorEmail($email);
        if ($usuarioExistente) {
            $mensaje = 'Ya existe un usuario con ese email';
            $tipoMensaje = 'danger';
        } else {
            // Crear usuario
            $resultado = $usuarioModel->crear($nombre, $apellido, $email, $clave, $telefono, $rol);
            
            if ($resultado['success']) {
                $mensaje = 'Usuario creado exitosamente';
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

// Configurar título de la página
$pageTitle = 'Crear Usuario - Admin OFM';
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
                    <h1 class="m-0">Crear Usuario</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="dashboard.php">Inicio</a></li>
                        <li class="breadcrumb-item"><a href="usuarios.php">Usuarios</a></li>
                        <li class="breadcrumb-item active">Crear Usuario</li>
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

            <!-- Formulario -->
            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-user-plus me-2"></i>Información del Usuario
                            </h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" id="usuarioForm">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="nombre" class="form-label">Nombre *</label>
                                            <input type="text" class="form-control" id="nombre" name="nombre" 
                                                   value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>" required>
                                            <div class="form-text">Nombre del usuario</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="apellido" class="form-label">Apellido *</label>
                                            <input type="text" class="form-control" id="apellido" name="apellido" 
                                                   value="<?= htmlspecialchars($_POST['apellido'] ?? '') ?>" required>
                                            <div class="form-text">Apellido del usuario</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="email" class="form-label">Email *</label>
                                            <input type="email" class="form-control" id="email" name="email" 
                                                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                                            <div class="form-text">Email único para el acceso</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="telefono" class="form-label">Teléfono</label>
                                            <input type="tel" class="form-control" id="telefono" name="telefono" 
                                                   value="<?= htmlspecialchars($_POST['telefono'] ?? '') ?>">
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
                                                <option value="admin" <?= ($_POST['rol'] ?? '') === 'admin' ? 'selected' : '' ?>>Administrador</option>
                                                <option value="socio" <?= ($_POST['rol'] ?? '') === 'socio' ? 'selected' : '' ?>>Socio</option>
                                                <option value="cliente" <?= ($_POST['rol'] ?? '') === 'cliente' ? 'selected' : '' ?>>Cliente</option>
                                            </select>
                                            <div class="form-text">Rol del usuario en el sistema</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="clave" class="form-label">Contraseña *</label>
                                            <input type="password" class="form-control" id="clave" name="clave" 
                                                   required minlength="6">
                                            <div class="form-text">Mínimo 6 caracteres</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="confirmar_clave" class="form-label">Confirmar Contraseña *</label>
                                            <input type="password" class="form-control" id="confirmar_clave" name="confirmar_clave" 
                                                   required minlength="6">
                                            <div class="form-text">Repite la contraseña</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Botones de acción -->
                                <div class="d-flex justify-content-between mt-4">
                                    <a href="usuarios.php" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left me-2"></i>Volver
                                    </a>
                                    <div>
                                        <button type="reset" class="btn btn-outline-secondary me-2">
                                            <i class="fas fa-undo me-2"></i>Limpiar
                                        </button>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-2"></i>Crear Usuario
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
                            <i class="fas fa-user-plus user-icon mb-3"></i>
                            <h5 class="card-title">Nuevo Usuario</h5>
                            <p class="card-text">Crea una nueva cuenta de usuario en el sistema con el rol correspondiente.</p>
                        </div>
                    </div>

                    <!-- Roles disponibles -->
                    <div class="card mt-3">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="fas fa-info-circle me-2"></i>Roles Disponibles
                            </h6>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled small">
                                <li class="mb-2">
                                    <i class="fas fa-user-shield text-danger me-2"></i>
                                    <strong>Administrador:</strong> Acceso completo al sistema
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-store text-success me-2"></i>
                                    <strong>Socio:</strong> Gestión de productos y ventas
                                </li>
                                <li>
                                    <i class="fas fa-shopping-cart text-info me-2"></i>
                                    <strong>Cliente:</strong> Compra de productos
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
                                    • El email debe ser único en el sistema
                                </li>
                                <li class="mb-2">
                                    • La contraseña se encripta automáticamente
                                </li>
                                <li>
                                    • El usuario se crea activo por defecto
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
    .user-icon {
        font-size: 4rem;
        color: #007bff;
    }
</style>
";

$additionalJS = "
<script>
    // Validación del formulario
    document.getElementById('usuarioForm').addEventListener('submit', function(e) {
        const clave = document.getElementById('clave').value;
        const confirmarClave = document.getElementById('confirmar_clave').value;
        
        if (clave !== confirmarClave) {
            e.preventDefault();
            alert('Las contraseñas no coinciden');
            return false;
        }
        
        if (clave.length < 6) {
            e.preventDefault();
            alert('La contraseña debe tener al menos 6 caracteres');
            return false;
        }
    });

    // Validación en tiempo real de la contraseña
    document.getElementById('confirmar_clave').addEventListener('input', function() {
        const clave = document.getElementById('clave').value;
        const confirmarClave = this.value;
        
        if (confirmarClave && clave !== confirmarClave) {
            this.setCustomValidity('Las contraseñas no coinciden');
        } else {
            this.setCustomValidity('');
        }
    });
</script>
";
?>

<?php require_once 'includes/footer.php'; ?>
