<?php
require_once __DIR__ . '/../controllers/loginController.php';

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

// Obtener estadísticas del sistema
require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../models/Producto.php';
require_once __DIR__ . '/../models/Comercio.php';

$usuarioModel = new Usuario($pdo);
$productoModel = new Producto($pdo);
$comercioModel = new Comercio($pdo);

$estadisticasUsuarios = $usuarioModel->obtenerEstadisticas();
$estadisticasProductos = $productoModel->obtenerEstadisticas();
$estadisticasComercios = $comercioModel->obtenerEstadisticas();

$totalProductos = $estadisticasProductos['total_productos'] ?? 0;
$totalComercios = $estadisticasComercios['total_comercios'] ?? 0;

// Configurar título de la página
$pageTitle = 'Dashboard - Admin OFM';
?>

<?php require_once 'includes/header.php'; ?>
<?php require_once 'includes/navbar.php'; ?>
<?php require_once 'includes/sidebar.php'; ?>

<div class="wrapper">
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Dashboard</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Inicio</a></li>
              <li class="breadcrumb-item active">Dashboard</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <!-- Botones de Acción Rápida -->
        <div class="row mb-3">
          <div class="col-12">
            <div class="card card-outline card-primary">
              <div class="card-header">
                <h3 class="card-title">
                  <i class="fas fa-bolt mr-2"></i>Acciones Rápidas
                </h3>
              </div>
              <div class="card-body">
                <div class="row">
                  <div class="col-md-3 mb-2">
                    <a href="comercios/crear.php" class="btn btn-success btn-block">
                      <i class="fas fa-store mr-2"></i>Crear Comercio
                    </a>
                  </div>
                  <div class="col-md-3 mb-2">
                    <a href="usuarios.php" class="btn btn-info btn-block">
                      <i class="fas fa-users mr-2"></i>Gestionar Usuarios
                    </a>
                  </div>
                  <div class="col-md-3 mb-2">
                    <a href="productos/" class="btn btn-warning btn-block">
                      <i class="fas fa-box mr-2"></i>Gestionar Productos
                    </a>
                  </div>
                  <div class="col-md-3 mb-2">
                    <a href="ventas.php" class="btn btn-danger btn-block">
                      <i class="fas fa-chart-line mr-2"></i>Ver Ventas
                    </a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Small boxes (Stat box) -->
        <div class="row">
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-info">
              <div class="inner">
                <h3><?= count($estadisticasUsuarios) > 0 ? array_sum(array_column($estadisticasUsuarios, 'total')) : 0 ?></h3>

                <p>Total Usuarios</p>
              </div>
              <div class="icon">
                <i class="fas fa-users"></i>
              </div>
              <a href="usuarios.php" class="small-box-footer">
                Más información <i class="fas fa-arrow-circle-right"></i>
              </a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-success">
              <div class="inner">
                <h3><?= $totalProductos ?></h3>

                <p>Total Productos</p>
              </div>
              <div class="icon">
                <i class="fas fa-box"></i>
              </div>
              <a href="productos.php" class="small-box-footer">
                Más información <i class="fas fa-arrow-circle-right"></i>
              </a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-warning">
              <div class="inner">
                <h3><?= count($estadisticasUsuarios) > 0 ? array_sum(array_column($estadisticasUsuarios, 'activos_30dias')) : 0 ?></h3>

                <p>Usuarios Activos (30 días)</p>
              </div>
              <div class="icon">
                <i class="fas fa-user-check"></i>
              </div>
              <a href="usuarios.php" class="small-box-footer">
                Más información <i class="fas fa-arrow-circle-right"></i>
              </a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-danger">
              <div class="inner">
                <h3>65</h3>

                <p>Ventas del Mes</p>
              </div>
              <div class="icon">
                <i class="fas fa-chart-line"></i>
              </div>
              <a href="ventas.php" class="small-box-footer">
                Más información <i class="fas fa-arrow-circle-right"></i>
              </a>
            </div>
          </div>
          <!-- ./col -->
        </div>
        <!-- /.row -->
        
        <!-- Segunda fila de estadísticas -->
        <div class="row">
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-info">
              <div class="inner">
                <h3><?= $totalComercios ?></h3>
                <p>Total Comercios</p>
              </div>
              <div class="icon">
                <i class="fas fa-store"></i>
              </div>
              <a href="comercios/" class="small-box-footer">
                Más información <i class="fas fa-arrow-circle-right"></i>
              </a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-secondary">
              <div class="inner">
                <h3><?= count($usuarioModel->obtenerPorRol('cliente')) ?></h3>
                <p>Total Clientes</p>
              </div>
              <div class="icon">
                <i class="fas fa-user-friends"></i>
              </div>
              <a href="usuarios.php" class="small-box-footer">
                Más información <i class="fas fa-arrow-circle-right"></i>
              </a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-success">
              <div class="inner">
                <h3><?= count(array_filter($usuarioModel->obtenerPorRol('socio'), function($u) { return $u['activo']; })) ?></h3>
                <p>Comercios Activos</p>
              </div>
              <div class="icon">
                <i class="fas fa-store-alt"></i>
              </div>
              <a href="comercios/" class="small-box-footer">
                Más información <i class="fas fa-arrow-circle-right"></i>
              </a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-warning">
              <div class="inner">
                <h3><?= count(array_filter($usuarioModel->obtenerPorRol('socio'), function($u) { return !$u['activo']; })) ?></h3>
                <p>Comercios Inactivos</p>
              </div>
              <div class="icon">
                <i class="fas fa-store-slash"></i>
              </div>
              <a href="comercios/" class="small-box-footer">
                Más información <i class="fas fa-arrow-circle-right"></i>
              </a>
            </div>
          </div>
          <!-- ./col -->
        </div>
        <!-- /.row -->
        <!-- Main row -->
        <div class="row">
          <!-- Left col -->
          <div class="col-md-8">
            <!-- TABLE: LATEST ORDERS -->
            <div class="card">
              <div class="card-header border-transparent">
                <h3 class="card-title">Últimas Ventas</h3>

                <div class="card-tools">
                  <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                  </button>
                  <button type="button" class="btn btn-tool" data-card-widget="maximize">
                    <i class="fas fa-expand"></i>
                  </button>
                </div>
              </div>
              <!-- /.card-header -->
              <div class="card-body p-0">
                <div class="table-responsive">
                  <table class="table m-0">
                    <thead>
                    <tr>
                      <th>ID Venta</th>
                      <th>Cliente</th>
                      <th>Producto</th>
                      <th>Total</th>
                      <th>Estado</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                      <td><a href="ventas.php">OR9842</a></td>
                      <td>Juan Pérez</td>
                      <td>Laptop HP Pavilion</td>
                      <td><span class="badge badge-success">$899.99</span></td>
                      <td><span class="badge badge-success">Completada</span></td>
                    </tr>
                    <tr>
                      <td><a href="ventas.php">OR1848</a></td>
                      <td>María García</td>
                      <td>Smartphone Samsung Galaxy</td>
                      <td><span class="badge badge-success">$449.99</span></td>
                      <td><span class="badge badge-warning">Pendiente</span></td>
                    </tr>
                    <tr>
                      <td><a href="ventas.php">OR7429</a></td>
                      <td>Carlos López</td>
                      <td>Auriculares Bluetooth</td>
                      <td><span class="badge badge-success">$89.99</span></td>
                      <td><span class="badge badge-info">Enviado</span></td>
                    </tr>
                    <tr>
                      <td><a href="ventas.php">OR7429</a></td>
                      <td>Ana Martínez</td>
                      <td>Tablet iPad Air</td>
                      <td><span class="badge badge-success">$599.99</span></td>
                      <td><span class="badge badge-success">Entregado</span></td>
                    </tr>
                    <tr>
                      <td><a href="ventas.php">OR1848</a></td>
                      <td>Luis Rodríguez</td>
                      <td>Smart TV 55"</td>
                      <td><span class="badge badge-success">$699.99</span></td>
                      <td><span class="badge badge-warning">Pendiente</span></td>
                    </tr>
                    </tbody>
                  </table>
                </div>
                <!-- /.table-responsive -->
              </div>
              <!-- /.card-body -->
              <div class="card-footer clearfix">
                <a href="ventas.php" class="btn btn-sm btn-info float-left">Ver Todas las Ventas</a>
                <a href="reportes-ventas.php" class="btn btn-sm btn-secondary float-right">Generar Reporte</a>
              </div>
              <!-- /.card-footer -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->

          <div class="col-md-4">
            <!-- PRODUCT LIST -->
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Productos Recientes</h3>

                <div class="card-tools">
                  <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                  </button>
                </div>
              </div>
              <!-- /.card-header -->
              <div class="card-body p-0">
                <ul class="products-list product-list-in-card pl-2 pr-2">
                  <li class="item">
                    <div class="product-img">
                      <img src="https://via.placeholder.com/50x50" alt="Product Image" class="img-size-50">
                    </div>
                    <div class="product-info">
                      <a href="javascript:void(0)" class="product-title">Laptop HP Pavilion
                        <span class="badge badge-warning float-right">$899.99</span></a>
                      <span class="product-description">
                        Intel Core i5, 8GB RAM, 256GB SSD
                      </span>
                    </div>
                  </li>
                  <!-- /.item -->
                  <li class="item">
                    <div class="product-img">
                      <img src="https://via.placeholder.com/50x50" alt="Product Image" class="img-size-50">
                    </div>
                    <div class="product-info">
                      <a href="javascript:void(0)" class="product-title">Smartphone Samsung Galaxy
                        <span class="badge badge-warning float-right">$449.99</span></a>
                      <span class="product-description">
                        Galaxy A54 5G, 128GB, 6GB RAM
                      </span>
                    </div>
                  </li>
                  <!-- /.item -->
                  <li class="item">
                    <div class="product-img">
                      <img src="https://via.placeholder.com/50x50" alt="Product Image" class="img-size-50">
                    </div>
                    <div class="product-info">
                      <a href="javascript:void(0)" class="product-title">Auriculares Bluetooth
                        <span class="badge badge-warning float-right">$89.99</span></a>
                      <span class="product-description">
                        Cancelación de ruido activa
                      </span>
                    </div>
                  </li>
                  <!-- /.item -->
                </ul>
              </div>
              <!-- /.card-body -->
              <div class="card-footer text-center">
                <a href="productos.php" class="uppercase">Ver Todos los Productos</a>
              </div>
              <!-- /.card-footer -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
      </div><!--/. container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
</div>
<!-- ./wrapper -->

<?php require_once 'includes/footer.php'; ?>
