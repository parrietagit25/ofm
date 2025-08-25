<?php
require_once __DIR__ . '/../../controllers/loginController.php';
require_once __DIR__ . '/../../controllers/qrController.php';
require_once __DIR__ . '/../../includes/db.php';

// Verificar que el usuario esté autenticado
$loginController = new LoginController($pdo);
if (!$loginController->estaAutenticado()) {
    header('Location: page-login-register.php');
    exit;
}

// Obtener parámetros
$ordenId = $_GET['orden_id'] ?? null;
$numero = $_GET['numero'] ?? null;

if (!$ordenId || !$numero) {
    header('Location: dashboard.php');
    exit;
}

// Obtener información de la orden
$pdo = getConnection();
$qrController = new QRController($pdo);

$resultadoQRs = $qrController->obtenerQRsOrden($ordenId);
if (!$resultadoQRs['success']) {
    $error = $resultadoQRs['message'];
}

$qrs = $resultadoQRs['success'] ? $resultadoQRs['qrs'] : [];

// Configurar título
$pageTitle = "Códigos QR - Orden #$numero";
?>

<!DOCTYPE html>
<html class="no-js" lang="es">
<head>
    <meta charset="utf-8">
    <title><?= $pageTitle ?></title>
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="description" content="Códigos QR de la orden #<?= htmlspecialchars($numero) ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="/ofm/public/evara/assets/imgs/theme/favicon.svg">
    <!-- Template CSS -->
    <link rel="stylesheet" href="/ofm/public/evara/assets/css/main.css?v=3.4">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        .qr-container {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            text-align: center;
            transition: all 0.3s ease;
        }
        
        .qr-container:hover {
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
            transform: translateY(-2px);
        }
        
        .qr-image {
            max-width: 200px;
            height: auto;
            margin: 15px auto;
            border: 3px solid #fff;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .qr-info {
            background: #fff;
            padding: 15px;
            border-radius: 8px;
            margin-top: 15px;
        }
        
        .download-btn {
            background: linear-gradient(45deg, #007bff, #0056b3);
            border: none;
            color: white;
            padding: 10px 20px;
            border-radius: 25px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .download-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,123,255,0.4);
            color: white;
        }
        
        .header-info {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
        }
    </style>
</head>

<body>
    <!-- Header -->
    <header class="header-area header-style-5">
        <div class="header-bottom sticky-bar sticky-white-bg">
            <div class="container">
                <div class="header-wrap header-space-between position-relative">
                    <div class="logo logo-width-1">
                        <a href="/ofm/public/evara/"><img src="/ofm/public/evara/assets/imgs/theme/logo.svg" alt="OFM"></a>
                    </div>
                    <div class="header-action-right">
                        <a href="/ofm/cliente/dashboard.php" class="btn btn-outline-primary">
                            <i class="fi-rs-user mr-2"></i>Mi Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="main">
        <div class="page-header breadcrumb-wrap">
            <div class="container">
                <div class="breadcrumb">
                    <a href="/ofm/public/evara/" rel="nofollow">Inicio</a>
                    <span></span> 
                    <a href="/ofm/cliente/dashboard.php">Mi Cuenta</a>
                    <span></span> Códigos QR
                </div>
            </div>
        </div>
        
        <section class="pt-150 pb-150">
            <div class="container">
                <div class="row">
                    <div class="col-lg-10 m-auto">
                        
                        <!-- Header de la Orden -->
                        <div class="header-info text-center">
                            <h1 class="mb-3">
                                <i class="fi-rs-qr-code mr-3"></i>
                                Códigos QR de la Orden
                            </h1>
                            <h3 class="mb-2">#<?= htmlspecialchars($numero) ?></h3>
                            <p class="mb-0">
                                <strong><?= count($qrs) ?></strong> códigos QR únicos generados
                            </p>
                        </div>

                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger text-center">
                                <i class="fi-rs-alert-triangle mr-2"></i>
                                <?= htmlspecialchars($error) ?>
                            </div>
                        <?php elseif (empty($qrs)): ?>
                            <div class="text-center py-5">
                                <i class="fi-rs-qr-code text-muted" style="font-size: 4rem;"></i>
                                <h4 class="mt-3 text-muted">No se encontraron códigos QR</h4>
                                <p class="text-muted">Esta orden no tiene códigos QR asociados.</p>
                                <a href="/ofm/cliente/dashboard.php" class="btn btn-primary">Volver al Dashboard</a>
                            </div>
                        <?php else: ?>
                            
                            <!-- Información de la Orden -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-body">
                                            <h6 class="card-title">
                                                <i class="fi-rs-info mr-2"></i>Información de la Orden
                                            </h6>
                                            <p class="mb-1"><strong>Número:</strong> <?= htmlspecialchars($numero) ?></p>
                                            <p class="mb-1"><strong>Total de QRs:</strong> <?= count($qrs) ?></p>
                                            <p class="mb-0"><strong>Fecha:</strong> <?= date('d/m/Y H:i', strtotime($qrs[0]['created_at'])) ?></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-body">
                                            <h6 class="card-title">
                                                <i class="fi-rs-download mr-2"></i>Descargar Todos
                                            </h6>
                                            <p class="mb-3">Descarga todos los códigos QR en un archivo ZIP</p>
                                            <button class="btn btn-success w-100" onclick="descargarTodosQR()">
                                                <i class="fi-rs-download mr-2"></i>Descargar ZIP
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Códigos QR -->
                            <div class="row">
                                <?php foreach ($qrs as $qr): ?>
                                    <div class="col-md-6 col-lg-4 mb-4">
                                        <div class="qr-container">
                                            <h6 class="mb-2"><?= htmlspecialchars($qr['nombre_producto']) ?></h6>
                                            <p class="text-muted mb-2">
                                                <small>
                                                    <i class="fi-rs-tag mr-1"></i>
                                                    Unidad <?= $qr['unidad_numero'] ?>
                                                </small>
                                            </p>
                                            
                                            <div class="qr-image-container">
                                                <img src="data:image/png;base64,<?= base64_encode($qrController->generarQRImagen($qr['codigo_qr'])['image_data']) ?>" 
                                                     alt="QR <?= htmlspecialchars($qr['codigo_qr']) ?>" 
                                                     class="qr-image">
                                            </div>
                                            
                                            <div class="qr-info">
                                                <p class="text-muted small mb-2">
                                                    <strong>Código:</strong><br>
                                                    <code><?= htmlspecialchars($qr['codigo_qr']) ?></code>
                                                </p>
                                                <button class="btn download-btn btn-sm" 
                                                        onclick="descargarQR('<?= htmlspecialchars($qr['codigo_qr']) ?>', '<?= htmlspecialchars($qr['nombre_producto']) ?>', <?= $qr['unidad_numero'] ?>)">
                                                    <i class="fi-rs-download mr-2"></i>Descargar
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <!-- Botón Volver -->
                            <div class="text-center mt-5">
                                <a href="/ofm/cliente/dashboard.php" class="btn btn-outline-secondary">
                                    <i class="fi-rs-arrow-left mr-2"></i>Volver al Dashboard
                                </a>
                            </div>
                        <?php endif; ?>
                        
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="main">
        <div class="container pb-20">
            <div class="row">
                <div class="col-lg-6">
                    <p class="float-md-left font-sm text-muted mb-0">
                        &copy; 2024, <strong class="text-brand">OFM</strong> - Tu Marketplace de Confianza
                    </p>
                </div>
                <div class="col-lg-6">
                    <p class="text-lg-end text-start font-sm text-muted mb-0">
                        Desarrollado por <a href="#" target="_blank">OFM Team</a>
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="/ofm/public/evara/assets/js/vendor/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- JSZip para crear archivos ZIP -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    
    <script>
        // Descargar QR individual
        function descargarQR(codigoQR, nombreProducto, unidadNumero) {
            const link = document.createElement('a');
            link.download = `QR_${nombreProducto}_U${unidadNumero}.png`;
            link.href = `data:image/png;base64,${getQRBase64(codigoQR)}`;
            link.click();
        }
        
        // Obtener QR en base64 (simulado - en producción esto vendría del servidor)
        function getQRBase64(codigoQR) {
            // Por ahora retornamos un placeholder
            // En producción, esto debería hacer una llamada AJAX al servidor
            return 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==';
        }
        
        // Descargar todos los QR en ZIP
        async function descargarTodosQR() {
            try {
                const zip = new JSZip();
                const qrImages = document.querySelectorAll('.qr-image');
                
                qrImages.forEach((img, index) => {
                    const nombre = img.alt.replace('QR ', '');
                    const extension = 'png';
                    zip.file(`${nombre}.${extension}`, img.src.split(',')[1], {base64: true});
                });
                
                const content = await zip.generateAsync({type: 'blob'});
                const link = document.createElement('a');
                link.href = URL.createObjectURL(content);
                link.download = `QRs_Orden_<?= htmlspecialchars($numero) ?>.zip`;
                link.click();
                
            } catch (error) {
                console.error('Error generando ZIP:', error);
                alert('Error generando archivo ZIP');
            }
        }
    </script>
</body>
</html>
