<?php
require_once __DIR__ . '/../controllers/loginController.php';

// Verificar que el usuario esté autenticado y sea cliente
$loginController->verificarAcceso('cliente');

// Obtener información del usuario actual
$usuario = $loginController->obtenerUsuarioActual();

// Verificar expiración de sesión
$loginController->verificarExpiracionSesion();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard Cliente - OFM</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 12px 20px;
            margin: 2px 0;
            border-radius: 8px;
            transition: all 0.3s;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: white;
            background: rgba(255,255,255,0.1);
            transform: translateX(5px);
        }
        .sidebar .nav-link i {
            margin-right: 10px;
            width: 20px;
        }
        .main-content {
            background-color: #f8f9fa;
            min-height: 100vh;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            transition: transform 0.2s;
        }
        .card:hover {
            transform: translateY(-2px);
        }
        .stats-card {
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
        }
        .navbar-brand {
            font-weight: bold;
            color: #667eea;
        }
        .user-info {
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
            border-radius: 15px;
            padding: 20px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 px-0">
                <div class="sidebar p-3">
                    <div class="text-center mb-4">
                        <h4 class="text-white">OFM Cliente</h4>
                        <small class="text-white-50">Panel de Usuario</small>
                    </div>
                    
                    <nav class="nav flex-column">
                        <a class="nav-link active" href="#dashboard" data-bs-toggle="tab">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                        <a class="nav-link" href="#productos" data-bs-toggle="tab">
                            <i class="fas fa-shopping-bag"></i> Productos
                        </a>
                        <a class="nav-link" href="#compras" data-bs-toggle="tab">
                            <i class="fas fa-shopping-cart"></i> Mis Compras
                        </a>
                        <a class="nav-link" href="#perfil" data-bs-toggle="tab">
                            <i class="fas fa-user"></i> Mi Perfil
                        </a>
                        <a class="nav-link" href="#ofertas" data-bs-toggle="tab">
                            <i class="fas fa-tags"></i> Ofertas
                        </a>
                        <a class="nav-link" href="?logout=1">
                            <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                        </a>
                    </nav>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 px-0">
                <div class="main-content p-4">
                    <!-- Top Navbar -->
                    <nav class="navbar navbar-expand-lg navbar-light bg-white mb-4 rounded shadow-sm">
                        <div class="container-fluid">
                            <span class="navbar-brand">Dashboard Cliente</span>
                            <div class="navbar-nav ms-auto">
                                <div class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                                        <i class="fas fa-user-circle me-2"></i><?= htmlspecialchars($usuario['nombre']) ?>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#perfil" data-bs-toggle="tab">Mi Perfil</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item" href="?logout=1">Cerrar Sesión</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </nav>

                    <!-- Tab Content -->
                    <div class="tab-content">
                        <!-- Dashboard Tab -->
                        <div class="tab-pane fade show active" id="dashboard">
                            <div class="row mb-4">
                                <div class="col-md-3">
                                    <div class="card stats-card">
                                        <div class="card-body text-center">
                                            <i class="fas fa-shopping-bag fa-2x mb-2"></i>
                                            <h5>Productos Vistos</h5>
                                            <h3>24</h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card stats-card">
                                        <div class="card-body text-center">
                                            <i class="fas fa-shopping-cart fa-2x mb-2"></i>
                                            <h5>Compras Realizadas</h5>
                                            <h3>8</h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card stats-card">
                                        <div class="card-body text-center">
                                            <i class="fas fa-dollar-sign fa-2x mb-2"></i>
                                            <h5>Total Gastado</h5>
                                            <h3>$1,250</h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card stats-card">
                                        <div class="card-body text-center">
                                            <i class="fas fa-tags fa-2x mb-2"></i>
                                            <h5>Ofertas Activas</h5>
                                            <h3>12</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-8">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="mb-0">Actividad Reciente</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="d-flex align-items-center mb-3">
                                                <div class="bg-primary rounded-circle p-2 me-3">
                                                    <i class="fas fa-shopping-cart text-white"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">Compra realizada</h6>
                                                    <small class="text-muted">Laptop HP Pavilion - $899.99</small>
                                                </div>
                                                <small class="text-muted ms-auto">Hace 2 días</small>
                                            </div>
                                            <div class="d-flex align-items-center mb-3">
                                                <div class="bg-success rounded-circle p-2 me-3">
                                                    <i class="fas fa-eye text-white"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">Producto visto</h6>
                                                    <small class="text-muted">Smartphone Samsung Galaxy</small>
                                                </div>
                                                <small class="text-muted ms-auto">Hace 3 días</small>
                                            </div>
                                            <div class="d-flex align-items-center">
                                                <div class="bg-warning rounded-circle p-2 me-3">
                                                    <i class="fas fa-tag text-white"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">Oferta encontrada</h6>
                                                    <small class="text-muted">20% descuento en electrónicos</small>
                                                </div>
                                                <small class="text-muted ms-auto">Hace 5 días</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="mb-0">Información del Usuario</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="user-info">
                                                <div class="text-center mb-3">
                                                    <i class="fas fa-user-circle fa-3x"></i>
                                                </div>
                                                <h6 class="text-center"><?= htmlspecialchars($usuario['nombre']) ?></h6>
                                                <p class="text-center mb-2"><?= htmlspecialchars($usuario['email']) ?></p>
                                                <div class="text-center">
                                                    <span class="badge bg-light text-dark">Cliente</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Productos Tab -->
                        <div class="tab-pane fade" id="productos">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">Productos Disponibles</h5>
                                    <div class="input-group" style="width: 300px;">
                                        <input type="text" class="form-control" placeholder="Buscar productos...">
                                        <button class="btn btn-outline-primary" type="button">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <div class="card h-100">
                                                <img src="https://via.placeholder.com/300x200" class="card-img-top" alt="Producto">
                                                <div class="card-body">
                                                    <h6 class="card-title">Laptop HP Pavilion</h6>
                                                    <p class="card-text text-muted">Intel Core i5, 8GB RAM, 256GB SSD</p>
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <span class="text-primary fw-bold">$899.99</span>
                                                        <button class="btn btn-sm btn-primary">Ver Detalles</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <div class="card h-100">
                                                <img src="https://via.placeholder.com/300x200" class="card-img-top" alt="Producto">
                                                <div class="card-body">
                                                    <h6 class="card-title">Smartphone Samsung</h6>
                                                    <p class="card-text text-muted">Galaxy A54 5G, 128GB, 6GB RAM</p>
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <span class="text-primary fw-bold">$449.99</span>
                                                        <button class="btn btn-sm btn-primary">Ver Detalles</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <div class="card h-100">
                                                <img src="https://via.placeholder.com/300x200" class="card-img-top" alt="Producto">
                                                <div class="card-body">
                                                    <h6 class="card-title">Auriculares Bluetooth</h6>
                                                    <p class="card-text text-muted">Cancelación de ruido activa</p>
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <span class="text-primary fw-bold">$89.99</span>
                                                        <button class="btn btn-sm btn-primary">Ver Detalles</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Compras Tab -->
                        <div class="tab-pane fade" id="compras">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Historial de Compras</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Productos</th>
                                                    <th>Total</th>
                                                    <th>Estado</th>
                                                    <th>Fecha</th>
                                                    <th>Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>#001</td>
                                                    <td>Laptop HP Pavilion</td>
                                                    <td>$899.99</td>
                                                    <td><span class="badge bg-success">Entregado</span></td>
                                                    <td>15/01/2024</td>
                                                    <td>
                                                        <button class="btn btn-sm btn-outline-primary">Ver Detalles</button>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>#002</td>
                                                    <td>Smartphone Samsung Galaxy</td>
                                                    <td>$449.99</td>
                                                    <td><span class="badge bg-warning">En Camino</span></td>
                                                    <td>20/01/2024</td>
                                                    <td>
                                                        <button class="btn btn-sm btn-outline-primary">Ver Detalles</button>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Perfil Tab -->
                        <div class="tab-pane fade" id="perfil">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Mi Perfil</h5>
                                </div>
                                <div class="card-body">
                                    <form>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Nombre</label>
                                                <input type="text" class="form-control" value="<?= htmlspecialchars(explode(' ', $usuario['nombre'])[0]) ?>">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Apellido</label>
                                                <input type="text" class="form-control" value="<?= htmlspecialchars(explode(' ', $usuario['nombre'])[1] ?? '') ?>">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Email</label>
                                                <input type="email" class="form-control" value="<?= htmlspecialchars($usuario['email']) ?>" readonly>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Teléfono</label>
                                                <input type="tel" class="form-control" placeholder="Ingrese su teléfono">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Nueva Contraseña</label>
                                                <input type="password" class="form-control" placeholder="Dejar en blanco para no cambiar">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Confirmar Contraseña</label>
                                                <input type="password" class="form-control" placeholder="Confirmar nueva contraseña">
                                            </div>
                                        </div>
                                        <button type="submit" class="btn btn-primary">Actualizar Perfil</button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Ofertas Tab -->
                        <div class="tab-pane fade" id="ofertas">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Ofertas Especiales</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <div class="card border-warning">
                                                <div class="card-body">
                                                    <h6 class="card-title text-warning">
                                                        <i class="fas fa-fire me-2"></i>¡Oferta Flash!
                                                    </h6>
                                                    <p class="card-text">20% de descuento en toda la categoría de electrónicos</p>
                                                    <small class="text-muted">Válido hasta el 31/01/2024</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="card border-success">
                                                <div class="card-body">
                                                    <h6 class="card-title text-success">
                                                        <i class="fas fa-gift me-2"></i>Descuento por Volumen
                                                    </h6>
                                                    <p class="card-text">Compra 3 productos y obtén 15% de descuento adicional</p>
                                                    <small class="text-muted">Aplicable en toda la tienda</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script>
        // Activar tabs
        document.addEventListener('DOMContentLoaded', function() {
            const navLinks = document.querySelectorAll('.nav-link[data-bs-toggle="tab"]');
            const tabPanes = document.querySelectorAll('.tab-pane');

            navLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    // Remover clase active de todos los links y panes
                    navLinks.forEach(l => l.classList.remove('active'));
                    tabPanes.forEach(p => p.classList.remove('show', 'active'));
                    
                    // Agregar clase active al link clickeado
                    this.classList.add('active');
                    
                    // Mostrar el tab correspondiente
                    const target = this.getAttribute('href');
                    document.querySelector(target).classList.add('show', 'active');
                });
            });
        });
    </script>
</body>
</html>
