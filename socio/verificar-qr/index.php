<?php
require_once __DIR__ . '/../controllers/loginController.php';

// Verificar que el usuario esté autenticado y sea socio
$loginController->verificarAcceso('socio');

// Obtener información del usuario actual
$usuario = $loginController->obtenerUsuarioActual();

// Verificar expiración de sesión
$loginController->verificarExpiracionSesion();

// Obtener comercio del socio
require_once __DIR__ . '/../models/Comercio.php';
$comercioModel = new Comercio($pdo);
$comercios = $comercioModel->obtenerPorUsuarioSocio($usuario['id']);

if (empty($comercios)) {
    header('Location: ../dashboard.php');
    exit;
}

$comercio = $comercios[0];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verificar QR - OFM Socio</title>

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
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            position: fixed;
            width: 250px;
            z-index: 1000;
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 12px 20px;
            margin: 2px 0;
            border-radius: 8px;
            transition: all 0.3s;
            text-decoration: none;
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
            margin-left: 250px;
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
        .qr-scanner {
            border: 2px dashed #28a745;
            border-radius: 15px;
            padding: 20px;
            text-align: center;
            background: #f8f9fa;
        }
        .qr-result {
            background: linear-gradient(45deg, #28a745, #20c997);
            color: white;
            border-radius: 15px;
            padding: 20px;
        }
    </style>
</head>
<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <div class="sidebar p-3">
            <div class="text-center mb-4">
                <h4 class="text-white">OFM Socio</h4>
                <small class="text-white-50">Panel de Negocio</small>
            </div>
            
            <nav class="nav flex-column">
                <a class="nav-link" href="../dashboard.php">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
                <a class="nav-link" href="../productos/">
                    <i class="fas fa-box"></i> Mis Productos
                </a>
                <a class="nav-link" href="../ventas/">
                    <i class="fas fa-chart-line"></i> Ventas
                </a>
                <a class="nav-link active" href="index.php">
                    <i class="fas fa-qrcode"></i> Verificar QR
                </a>
                <a class="nav-link" href="../perfil/">
                    <i class="fas fa-user"></i> Mi Perfil
                </a>
                <a class="nav-link" href="../reportes/">
                    <i class="fas fa-chart-bar"></i> Reportes
                </a>
                <a class="nav-link" href="../dashboard.php?logout=1">
                    <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                </a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="main-content flex-grow-1">
            <div class="p-4">
                <!-- Top Navbar -->
                <nav class="navbar navbar-expand-lg navbar-light bg-white mb-4 rounded shadow-sm">
                    <div class="container-fluid">
                        <span class="navbar-brand">Verificar QR</span>
                        <div class="navbar-nav ms-auto">
                            <div class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-user-circle me-2"></i><?= htmlspecialchars($usuario['nombre']) ?>
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="../perfil/">Mi Perfil</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="../dashboard.php?logout=1">Cerrar Sesión</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </nav>

                <!-- Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Verificador de Códigos QR</h2>
                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-primary" onclick="iniciarEscaneo()">
                            <i class="fas fa-camera me-2"></i>Iniciar Escáner
                        </button>
                        <button class="btn btn-outline-secondary" onclick="limpiarResultado()">
                            <i class="fas fa-eraser me-2"></i>Limpiar
                        </button>
                    </div>
                </div>

                <!-- QR Scanner -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-qrcode me-2"></i>Escáner QR
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="qr-scanner" id="qrScanner">
                                    <i class="fas fa-qrcode fa-4x text-muted mb-3"></i>
                                    <h5 class="text-muted">Escáner de Códigos QR</h5>
                                    <p class="text-muted">Haz clic en "Iniciar Escáner" para comenzar a escanear códigos QR</p>
                                    <div id="videoContainer" style="display: none;">
                                        <video id="qrVideo" width="100%" autoplay></video>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-info-circle me-2"></i>Resultado del Escaneo
                                </h5>
                            </div>
                            <div class="card-body">
                                <div id="qrResult" class="text-center">
                                    <i class="fas fa-qrcode fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">Sin resultado</h5>
                                    <p class="text-muted">Escanea un código QR para ver la información</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Información del Comercio -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-store me-2"></i>Información del Comercio
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6>Nombre del Comercio</h6>
                                        <p class="text-muted"><?= htmlspecialchars($comercio['nombre_comercio']) ?></p>
                                        
                                        <h6>Descripción</h6>
                                        <p class="text-muted"><?= htmlspecialchars($comercio['descripcion']) ?></p>
                                    </div>
                                    <div class="col-md-6">
                                        <h6>Dirección</h6>
                                        <p class="text-muted"><?= htmlspecialchars($comercio['direccion']) ?></p>
                                        
                                        <h6>Teléfono</h6>
                                        <p class="text-muted"><?= htmlspecialchars($comercio['telefono_comercio']) ?></p>
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
    
    <script>
        let stream = null;
        let scanning = false;

        // Función para iniciar el escaneo
        async function iniciarEscaneo() {
            if (scanning) {
                detenerEscaneo();
                return;
            }

            try {
                stream = await navigator.mediaDevices.getUserMedia({ 
                    video: { facingMode: 'environment' } 
                });
                
                const video = document.getElementById('qrVideo');
                video.srcObject = stream;
                
                document.getElementById('videoContainer').style.display = 'block';
                document.getElementById('qrScanner').innerHTML = '';
                document.getElementById('qrScanner').appendChild(document.getElementById('videoContainer'));
                
                scanning = true;
                
                // Aquí se implementaría la lógica de detección de QR
                // Por ahora simulamos un escaneo exitoso después de 3 segundos
                setTimeout(() => {
                    simularEscaneoExitoso();
                }, 3000);
                
            } catch (error) {
                console.error('Error al acceder a la cámara:', error);
                alert('Error al acceder a la cámara. Asegúrate de dar permisos.');
            }
        }

        // Función para detener el escaneo
        function detenerEscaneo() {
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
                stream = null;
            }
            
            scanning = false;
            document.getElementById('videoContainer').style.display = 'none';
            
            // Restaurar vista original
            document.getElementById('qrScanner').innerHTML = `
                <i class="fas fa-qrcode fa-4x text-muted mb-3"></i>
                <h5 class="text-muted">Escáner de Códigos QR</h5>
                <p class="text-muted">Haz clic en "Iniciar Escáner" para comenzar a escanear códigos QR</p>
                <div id="videoContainer" style="display: none;">
                    <video id="qrVideo" width="100%" autoplay></video>
                </div>
            `;
        }

        // Función para simular un escaneo exitoso (para demostración)
        function simularEscaneoExitoso() {
            const qrData = {
                tipo: 'producto',
                id: '12345',
                nombre: 'Producto de Prueba',
                precio: '29.99',
                comercio: '<?= htmlspecialchars($comercio['nombre_comercio']) ?>'
            };
            
            mostrarResultado(qrData);
            detenerEscaneo();
        }

        // Función para mostrar el resultado del escaneo
        function mostrarResultado(data) {
            const resultDiv = document.getElementById('qrResult');
            
            if (data.tipo === 'producto') {
                resultDiv.innerHTML = `
                    <div class="qr-result">
                        <i class="fas fa-check-circle fa-3x mb-3"></i>
                        <h5>Producto Verificado</h5>
                        <div class="row text-start">
                            <div class="col-6">
                                <strong>ID:</strong> ${data.id}
                            </div>
                            <div class="col-6">
                                <strong>Nombre:</strong> ${data.nombre}
                            </div>
                            <div class="col-6">
                                <strong>Precio:</strong> $${data.precio}
                            </div>
                            <div class="col-6">
                                <strong>Comercio:</strong> ${data.comercio}
                            </div>
                        </div>
                        <div class="mt-3">
                            <span class="badge bg-success">Verificado</span>
                        </div>
                    </div>
                `;
            } else {
                resultDiv.innerHTML = `
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
                        <h5>Código QR no reconocido</h5>
                        <p>El código escaneado no corresponde a un producto válido</p>
                    </div>
                `;
            }
        }

        // Función para limpiar el resultado
        function limpiarResultado() {
            document.getElementById('qrResult').innerHTML = `
                <i class="fas fa-qrcode fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Sin resultado</h5>
                <p class="text-muted">Escanea un código QR para ver la información</p>
            `;
        }

        // Limpiar al cargar la página
        document.addEventListener('DOMContentLoaded', function() {
            limpiarResultado();
        });
    </script>
</body>
</html>
