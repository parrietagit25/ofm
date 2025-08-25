<?php
/**
 * Script de prueba para verificar la generación de QR y sistema de órdenes
 * Accesible solo para administradores
 */

require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../controllers/loginController.php';

// Verificar autenticación
$pdo = getConnection();
$loginController = new LoginController($pdo);

if (!$loginController->estaAutenticado()) {
    header('Location: page-login-register.php');
    exit;
}

$usuario = $loginController->obtenerUsuarioActual();

// Solo administradores pueden acceder
if ($usuario['rol'] !== 'admin') {
    echo "<h1>❌ Acceso Denegado</h1>";
    echo "<p>Solo los administradores pueden acceder a esta página.</p>";
    echo "<a href='index.php'>Volver al inicio</a>";
    exit;
}

echo "<h1>🧪 Test de Generación de QR y Sistema de Órdenes</h1>";
echo "<p><strong>Usuario:</strong> " . htmlspecialchars($usuario['nombre']) . " (Rol: " . $usuario['rol'] . ")</p>";

// 1. Verificar estructura de base de datos
echo "<h2>1. 🗄️ Verificando Estructura de Base de Datos</h2>";
try {
    $tablas = ['ordenes', 'orden_detalles', 'transacciones', 'qr_verificaciones', 'comisiones_socios'];
    
    foreach ($tablas as $tabla) {
        $stmt = $pdo->query("SHOW TABLES LIKE '$tabla'");
        if ($stmt->rowCount() > 0) {
            echo "✅ Tabla <strong>$tabla</strong> existe<br>";
        } else {
            echo "❌ Tabla <strong>$tabla</strong> NO existe<br>";
        }
    }
} catch (Exception $e) {
    echo "❌ Error verificando tablas: " . $e->getMessage() . "<br>";
}

// 2. Verificar órdenes existentes
echo "<h2>2. 📋 Verificando Órdenes Existentes</h2>";
try {
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM ordenes");
    $totalOrdenes = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    echo "📊 Total órdenes en BD: <strong>$totalOrdenes</strong><br>";
    
    if ($totalOrdenes > 0) {
        $stmt = $pdo->query("SELECT * FROM ordenes ORDER BY id DESC LIMIT 3");
        $ultimasOrdenes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "📋 Últimas 3 órdenes:<br>";
        foreach ($ultimasOrdenes as $orden) {
            echo "   - ID: {$orden['id']}, Número: {$orden['numero_orden']}, Total: \${$orden['total']}, Estado: {$orden['estado']}<br>";
        }
    }
} catch (Exception $e) {
    echo "❌ Error verificando órdenes: " . $e->getMessage() . "<br>";
}

// 3. Verificar detalles de orden con QR
echo "<h2>3. 📱 Verificando Detalles de Orden y QR</h2>";
try {
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM orden_detalles");
    $totalDetalles = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    echo "📊 Total detalles de orden: <strong>$totalDetalles</strong><br>";
    
    if ($totalDetalles > 0) {
        $stmt = $pdo->query("SELECT od.*, p.nombre as nombre_producto, o.numero_orden 
                             FROM orden_detalles od 
                             JOIN productos p ON od.producto_id = p.id 
                             JOIN ordenes o ON od.orden_id = o.id 
                             ORDER BY od.id DESC LIMIT 5");
        $ultimosDetalles = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "📋 Últimos 5 detalles con QR:<br>";
        foreach ($ultimosDetalles as $detalle) {
            echo "   - Producto: {$detalle['nombre_producto']}, Orden: {$detalle['numero_orden']}, Cantidad: {$detalle['cantidad']}, QR: {$detalle['codigo_qr']}<br>";
        }
    }
} catch (Exception $e) {
    echo "❌ Error verificando detalles: " . $e->getMessage() . "<br>";
}

// 4. Verificar transacciones
echo "<h2>4. 💳 Verificando Transacciones</h2>";
try {
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM transacciones");
    $totalTransacciones = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    echo "📊 Total transacciones: <strong>$totalTransacciones</strong><br>";
} catch (Exception $e) {
    echo "❌ Error verificando transacciones: " . $e->getMessage() . "<br>";
}

// 5. Probar generación de QR
echo "<h2>5. 🎯 Probando Generación de QR</h2>";
try {
    // Simular datos para generar QR
    $comercioId = 1;
    $socioId = 1;
    $productoId = 1;
    $ordenId = 999;
    $unidadNumero = 1;
    
    $datos = [
        'comercio_id' => $comercioId,
        'socio_id' => $socioId,
        'producto_id' => $productoId,
        'orden_id' => $ordenId,
        'unidad_numero' => $unidadNumero,
        'timestamp' => time(),
        'random' => mt_rand(100000, 999999)
    ];
    
    $json = json_encode($datos);
    $hash = hash('sha256', $json);
    $codigoQR = 'QR_' . substr($hash, 0, 16) . '_' . time() . '_U' . $unidadNumero;
    
    echo "✅ Código QR generado: <strong>$codigoQR</strong><br>";
    echo "📝 Datos del QR: " . json_encode($datos) . "<br>";
    
    // Generar QR visual
    echo "<div style='margin: 20px 0;'>";
    echo "<h4>QR Generado:</h4>";
    echo "<div id='qr-test' style='background: white; padding: 20px; display: inline-block; border: 1px solid #ccc;'></div>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "❌ Error generando QR: " . $e->getMessage() . "<br>";
}

// 6. Verificar librería QR
echo "<h2>6. 📚 Verificando Librería QR</h2>";
echo "<p>Librería QR cargada: <span id='qr-status'>Verificando...</span></p>";

// 7. Recomendaciones
echo "<h2>7. 💡 Recomendaciones</h2>";
echo "<ul>";
echo "<li><strong>Verificar que las tablas existan:</strong> Si alguna tabla no existe, ejecutar los scripts SQL</li>";
echo "<li><strong>Verificar permisos de BD:</strong> El usuario debe tener permisos de lectura/escritura</li>";
echo "<li><strong>Verificar librería QR:</strong> Asegurar que la librería QR esté cargada correctamente</li>";
echo "<li><strong>Verificar sesiones:</strong> Asegurar que las sesiones funcionen correctamente</li>";
echo "</ul>";

// 8. Enlaces útiles
echo "<h2>8. 🔗 Enlaces Útiles</h2>";
echo "<a href='../admin/ordenes-clientes/index.php' style='margin: 5px; padding: 10px; background: #007bff; color: white; text-decoration: none; border-radius: 5px;'>📋 Panel de Órdenes</a>";
echo "<a href='../admin/productos/index.php' style='margin: 5px; padding: 10px; background: #28a745; color: white; text-decoration: none; border-radius: 5px;'>📦 Gestión de Productos</a>";
echo "<a href='index.php' style='margin: 5px; padding: 10px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px;'>🏠 Volver al Inicio</a>";

echo "<style>";
echo "body { font-family: Arial, sans-serif; margin: 20px; background: #f8f9fa; }";
echo "h1, h2 { color: #333; }";
echo "h2 { border-bottom: 2px solid #007bff; padding-bottom: 5px; margin-top: 30px; }";
echo "a:hover { opacity: 0.8; }";
echo "</style>";
?>

<!-- Librería QR -->
<script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Verificar librería QR
    if (typeof QRCode !== 'undefined') {
        document.getElementById('qr-status').innerHTML = '✅ <strong>QR Code cargada correctamente</strong>';
        
        // Generar QR de prueba
        const codigoQR = '<?= $codigoQR ?? "TEST_QR" ?>';
        const container = document.getElementById('qr-test');
        
        if (container && codigoQR) {
            QRCode.toCanvas(container, codigoQR, {
                width: 200,
                margin: 2,
                color: {
                    dark: '#000000',
                    light: '#FFFFFF'
                }
            }, function (error) {
                if (error) {
                    container.innerHTML = '<p class="text-danger">❌ Error generando QR: ' + error.message + '</p>';
                } else {
                    container.innerHTML += '<br><small class="text-muted">QR generado exitosamente</small>';
                }
            });
        }
    } else {
        document.getElementById('qr-status').innerHTML = '❌ <strong>QR Code NO está cargada</strong>';
    }
});
</script>
