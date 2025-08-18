<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../models/Producto.php';

$mensaje = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $productoModel = new Producto($pdo);
    
    $nombre = $_POST['nombre'] ?? '';
    $descripcion = $_POST['descripcion'] ?? '';
    $precio = $_POST['precio'] ?? 0;
    $stock = $_POST['stock'] ?? 0;
    $activo = isset($_POST['activo']) ? 1 : 0;
    
    // Procesar imagen
    $imagen = '';
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
        $uploadDir = '../../uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $fileExtension = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        
        if (in_array($fileExtension, $allowedExtensions)) {
            $imagen = uniqid() . '.' . $fileExtension;
            $uploadPath = $uploadDir . $imagen;
            
            if (move_uploaded_file($_FILES['imagen']['tmp_name'], $uploadPath)) {
                // Imagen subida correctamente
            } else {
                $error = 'Error al subir la imagen';
            }
        } else {
            $error = 'Formato de imagen no válido. Use: jpg, jpeg, png, gif';
        }
    }
    
    if (empty($error)) {
        try {
            $resultado = $productoModel->crear($nombre, $descripcion, $precio, $stock, $imagen, $activo);
            if ($resultado) {
                $mensaje = 'Producto creado correctamente';
                // Limpiar formulario
                $_POST = array();
            } else {
                $error = 'Error al crear el producto';
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
  <title>Crear Producto | Admin</title>

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
            <h1>Crear Nuevo Producto</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="../dashboard.php">Home</a></li>
              <li class="breadcrumb-item"><a href="productos.php">Productos</a></li>
              <li class="breadcrumb-item active">Crear</li>
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
                <h3 class="card-title">Información del Producto</h3>
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

              <form method="POST" enctype="multipart/form-data">
                <div class="card-body">
                  <div class="row">
                    <div class="col-md-8">
                      <div class="form-group">
                        <label for="nombre">Nombre del Producto *</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" 
                               value="<?php echo htmlspecialchars($_POST['nombre'] ?? ''); ?>" required>
                      </div>
                      
                      <div class="form-group">
                        <label for="descripcion">Descripción</label>
                        <textarea class="form-control" id="descripcion" name="descripcion" rows="4"><?php echo htmlspecialchars($_POST['descripcion'] ?? ''); ?></textarea>
                      </div>
                      
                      <div class="row">
                        <div class="col-md-6">
                          <div class="form-group">
                            <label for="precio">Precio *</label>
                            <div class="input-group">
                              <div class="input-group-prepend">
                                <span class="input-group-text">$</span>
                              </div>
                              <input type="number" class="form-control" id="precio" name="precio" 
                                     value="<?php echo $_POST['precio'] ?? ''; ?>" step="0.01" min="0" required>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-group">
                            <label for="stock">Stock *</label>
                            <input type="number" class="form-control" id="stock" name="stock" 
                                   value="<?php echo $_POST['stock'] ?? ''; ?>" min="0" required>
                          </div>
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-4">
                      <div class="form-group">
                        <label for="imagen">Imagen del Producto</label>
                        <div class="input-group">
                          <div class="custom-file">
                            <input type="file" class="custom-file-input" id="imagen" name="imagen" accept="image/*">
                            <label class="custom-file-label" for="imagen">Elegir archivo</label>
                          </div>
                        </div>
                        <small class="form-text text-muted">Formatos permitidos: JPG, JPEG, PNG, GIF</small>
                      </div>
                      
                      <div class="form-group">
                        <div class="custom-control custom-switch">
                          <input type="checkbox" class="custom-control-input" id="activo" name="activo" 
                                 <?php echo (isset($_POST['activo']) && $_POST['activo']) ? 'checked' : ''; ?>>
                          <label class="custom-control-label" for="activo">Producto Activo</label>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                
                <div class="card-footer">
                  <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Guardar Producto
                  </button>
                  <a href="productos.php" class="btn btn-secondary">
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

<script>
// Actualizar el label del input file
$('input[type="file"]').change(function(e){
    var fileName = e.target.files[0].name;
    $(this).next('.custom-file-label').html(fileName);
});
</script>
</body>
</html> 