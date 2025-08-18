<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../models/Usuario.php';

$usuarioModel = new Usuario($pdo);
$usuarios = $usuarioModel->obtenerTodos();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Gestión de Usuarios | Admin</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="../plugins/fontawesome-free/css/all.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="../dist/css/adminlte.min.css">
  <!-- DataTables -->
  <link rel="stylesheet" href="../plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="../plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
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
            <a href="usuarios.php" class="nav-link active">
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
            <h1>Gestión de Usuarios</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="../dashboard.php">Home</a></li>
              <li class="breadcrumb-item active">Usuarios</li>
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
                <h3 class="card-title">Lista de Usuarios</h3>
                <div class="card-tools">
                  <a href="usuario-crear.php" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Nuevo Usuario
                  </a>
                </div>
              </div>
              <div class="card-body">
                <table id="usuariosTable" class="table table-bordered table-striped">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>Nombre</th>
                      <th>Email</th>
                      <th>Rol</th>
                      <th>Estado</th>
                      <th>Fecha Registro</th>
                      <th>Acciones</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($usuarios as $usuario): ?>
                    <tr>
                      <td><?php echo $usuario['id']; ?></td>
                      <td><?php echo htmlspecialchars($usuario['nombre']); ?></td>
                      <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                      <td>
                        <?php 
                        $rol = $usuario['rol'] ?? 'usuario';
                        if ($rol == 'admin') {
                            echo '<span class="badge badge-danger">Administrador</span>';
                        } else {
                            echo '<span class="badge badge-info">Usuario</span>';
                        }
                        ?>
                      </td>
                      <td>
                        <?php if ($usuario['activo'] == 1): ?>
                          <span class="badge badge-success">Activo</span>
                        <?php else: ?>
                          <span class="badge badge-danger">Inactivo</span>
                        <?php endif; ?>
                      </td>
                      <td><?php echo $usuario['creado_en']; ?></td>
                      <td>
                        <a href="usuario-editar.php?id=<?php echo $usuario['id']; ?>" class="btn btn-info btn-sm">
                          <i class="fas fa-edit"></i>
                        </a>
                        <?php if ($usuario['id'] != 1): // No permitir eliminar el admin principal ?>
                        <button type="button" class="btn btn-danger btn-sm" onclick="eliminarUsuario(<?php echo $usuario['id']; ?>)">
                          <i class="fas fa-trash"></i>
                        </button>
                        <?php endif; ?>
                      </td>
                    </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
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
<!-- DataTables -->
<script src="../plugins/datatables/jquery.dataTables.min.js"></script>
<script src="../plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="../plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<!-- AdminLTE App -->
<script src="../dist/js/adminlte.min.js"></script>

<script>
$(function () {
  $('#usuariosTable').DataTable({
    "responsive": true,
    "autoWidth": false,
    "language": {
      "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json"
    }
  });
});

function eliminarUsuario(id) {
  if (confirm('¿Está seguro de que desea eliminar este usuario?')) {
    fetch('usuario-eliminar.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({id: id})
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        alert('Usuario eliminado correctamente');
        location.reload();
      } else {
        alert('Error al eliminar el usuario: ' + data.message);
      }
    })
    .catch(error => {
      console.error('Error:', error);
      alert('Error al eliminar el usuario');
    });
  }
}
</script>
</body>
</html> 