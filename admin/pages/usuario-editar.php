<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../models/Usuario.php';

$mensaje = '';
$error = '';
$usuario = null;

// Obtener ID del usuario
$id = $_GET['id'] ?? 0;
if (!$id) {
    header('Location: usuarios.php');
    exit;
}

$usuarioModel = new Usuario($pdo);

// Obtener datos del usuario
$usuario = $usuarioModel->obtenerPorId($id);
if (!$usuario) {
    header('Location: usuarios.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'] ?? '';
    $email = $_POST['email'] ?? '';
    $rol = $_POST['rol'] ?? 'usuario';
    $activo = isset($_POST['activo']) ? 1 : 0;
    $nuevaClave = $_POST['nueva_clave'] ?? '';
    
    // Validaciones
    if (empty($nombre) || empty($email)) {
        $error = 'Los campos nombre y email son obligatorios';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'El formato del email no es válido';
    } elseif (!empty($nuevaClave) && strlen($nuevaClave) < 6) {
        $error = 'La nueva contraseña debe tener al menos 6 caracteres';
    } else {
        try {
            // Verificar si el email ya existe en otro usuario
            if ($usuarioModel->emailExiste($email, $id)) {
                $error = 'El email ya está registrado por otro usuario';
            } else {
                $resultado = $usuarioModel->actualizar($id, $nombre, $email, $rol, $activo, $nuevaClave);
                if ($resultado) {
                    $mensaje = 'Usuario actualizado correctamente';
                    // Actualizar datos del usuario en la variable
                    $usuario = $usuarioModel->obtenerPorId($id);
                } else {
                    $error = 'Error al actualizar el usuario';
                }
            }
        } catch (Exception $e) {
            $error = 'Error: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Editar Usuario | Admin</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="../plugins/fontawesome-free/css/all.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="../dist/css/adminlte.min.css">
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">

  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
    </ul>
  </nav>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="../dashboard.php" class="brand-link">
      <span class="brand-text font-weight-light">OFM Admin</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
          <li class="nav-item">
            <a href="../dashboard.php" class="nav-link">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>Dashboard</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="productos.php" class="nav-link">
              <i class="nav-icon fas fa-box"></i>
              <p>Productos</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="usuarios.php" class="nav-link">
              <i class="nav-icon fas fa-users"></i>
              <p>Usuarios</p>
            </a>
          </li>
        </ul>
      </nav>
    </div>
  </aside>

  <!-- Content Wrapper -->
  <div class="content-wrapper">
    <!-- Content Header -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Editar Usuario</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="../dashboard.php">Home</a></li>
              <li class="breadcrumb-item"><a href="usuarios.php">Usuarios</a></li>
              <li class="breadcrumb-item active">Editar</li>
            </ol>
          </div>
        </div>
      </div>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Información del Usuario</h3>
              </div>
              
              <?php if (!empty($mensaje)): ?>
                <div class="alert alert-success alert-dismissible">
                  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                  <?php echo $mensaje; ?>
                </div>
              <?php endif; ?>
              
              <?php if (!empty($error)): ?>
                <div class="alert alert-danger alert-dismissible">
                  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                  <?php echo $error; ?>
                </div>
              <?php endif; ?>

              <form method="POST">
                <div class="card-body">
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="nombre">Nombre Completo *</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" 
                               value="<?php echo htmlspecialchars($usuario['nombre']); ?>" required>
                      </div>
                      
                      <div class="form-group">
                        <label for="email">Email *</label>
                        <input type="email" class="form-control" id="email" name="email" 
                               value="<?php echo htmlspecialchars($usuario['email']); ?>" required>
                      </div>
                      
                      <div class="form-group">
                        <label for="nueva_clave">Nueva Contraseña (opcional)</label>
                        <input type="password" class="form-control" id="nueva_clave" name="nueva_clave" 
                               minlength="6">
                        <small class="form-text text-muted">Dejar vacío para mantener la contraseña actual. Mínimo 6 caracteres.</small>
                      </div>
                    </div>
                    
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="rol">Rol</label>
                        <select class="form-control" id="rol" name="rol">
                          <option value="usuario" <?php echo ($usuario['rol'] == 'usuario') ? 'selected' : ''; ?>>Usuario</option>
                          <option value="admin" <?php echo ($usuario['rol'] == 'admin') ? 'selected' : ''; ?>>Administrador</option>
                        </select>
                      </div>
                      
                      <div class="form-group">
                        <div class="custom-control custom-switch">
                          <input type="checkbox" class="custom-control-input" id="activo" name="activo" 
                                 <?php echo ($usuario['activo'] == 1) ? 'checked' : ''; ?>>
                          <label class="custom-control-label" for="activo">Usuario Activo</label>
                        </div>
                      </div>
                      
                      <div class="form-group">
                        <label>Información Adicional</label>
                        <div class="small text-muted">
                          <p><strong>ID:</strong> <?php echo $usuario['id']; ?></p>
                          <p><strong>Registrado:</strong> <?php echo $usuario['creado_en']; ?></p>
                          <?php if (!empty($usuario['actualizado_en'])): ?>
                            <p><strong>Actualizado:</strong> <?php echo $usuario['actualizado_en']; ?></p>
                          <?php endif; ?>
                          <p><strong>Nota:</strong> Los usuarios administradores tienen acceso completo al panel.</p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                
                <div class="card-footer">
                  <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Actualizar Usuario
                  </button>
                  <a href="usuarios.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver
                  </a>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>

  <!-- Footer -->
  <footer class="main-footer">
    <div class="float-right d-none d-sm-inline">
      OFM Admin
    </div>
    <strong>Copyright &copy; 2024</strong> Todos los derechos reservados.
  </footer>
</div>

<!-- jQuery -->
<script src="../plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="../dist/js/adminlte.min.js"></script>
</body>
</html> 