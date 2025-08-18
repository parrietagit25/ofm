<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../models/Producto.php';

$productoModel = new Producto($pdo);
$productos = $productoModel->obtenerTodos();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Gestión de Productos | Admin</title>

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
            <a href="productos.php" class="nav-link active">
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
            <h1>Gestión de Productos</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="../dashboard.php">Home</a></li>
              <li class="breadcrumb-item active">Productos</li>
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
                <h3 class="card-title">Lista de Productos</h3>
                <div class="card-tools">
                  <a href="producto-crear.php" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Nuevo Producto
                  </a>
                </div>
              </div>
              <div class="card-body">
                <table id="productosTable" class="table table-bordered table-striped">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>Imagen</th>
                      <th>Nombre</th>
                      <th>Precio</th>
                      <th>Stock</th>
                      <th>Estado</th>
                      <th>Acciones</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($productos as $producto): ?>
                    <tr>
                      <td><?php echo $producto['id']; ?></td>
                      <td>
                        <?php if (!empty($producto['imagen'])): ?>
                          <img src="../../uploads/<?php echo $producto['imagen']; ?>" alt="<?php echo $producto['nombre']; ?>" style="width: 50px; height: 50px; object-fit: cover;">
                        <?php else: ?>
                          <div style="width: 50px; height: 50px; background-color: #f4f4f4; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-image text-muted"></i>
                          </div>
                        <?php endif; ?>
                      </td>
                      <td><?php echo htmlspecialchars($producto['nombre']); ?></td>
                      <td>$<?php echo number_format($producto['precio'], 2); ?></td>
                      <td><?php echo $producto['stock']; ?></td>
                      <td>
                        <?php if ($producto['activo'] == 1): ?>
                          <span class="badge badge-success">Activo</span>
                        <?php else: ?>
                          <span class="badge badge-danger">Inactivo</span>
                        <?php endif; ?>
                      </td>
                      <td>
                        <a href="producto-editar.php?id=<?php echo $producto['id']; ?>" class="btn btn-info btn-sm">
                          <i class="fas fa-edit"></i>
                        </a>
                        <button type="button" class="btn btn-danger btn-sm" onclick="eliminarProducto(<?php echo $producto['id']; ?>)">
                          <i class="fas fa-trash"></i>
                        </button>
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
  $('#productosTable').DataTable({
    "responsive": true,
    "autoWidth": false,
    "language": {
      "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json"
    }
  });
});

function eliminarProducto(id) {
  if (confirm('¿Está seguro de que desea eliminar este producto?')) {
    fetch('producto-eliminar.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({id: id})
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        alert('Producto eliminado correctamente');
        location.reload();
      } else {
        alert('Error al eliminar el producto: ' + data.message);
      }
    })
    .catch(error => {
      console.error('Error:', error);
      alert('Error al eliminar el producto');
    });
  }
}
</script>
</body>
</html> 