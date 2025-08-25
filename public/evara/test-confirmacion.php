<?php
// Habilitar reporte de errores para debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('display_startup_errors', 1);

echo "<h1>🧪 Test Confirmación de Orden</h1>";
echo "<p>Esta es una página de prueba para diagnosticar problemas en la confirmación.</p>";

try {
    echo "<h2>1. 📁 Verificando Includes</h2>";
    
    // Verificar config.php
    if (file_exists(__DIR__ . '/../../includes/config.php')) {
        require_once __DIR__ . '/../../includes/config.php';
        echo "✅ config.php cargado<br>";
    } else {
        echo "❌ config.php no existe<br>";
        return;
    }
    
    // Verificar db.php
    if (file_exists(__DIR__ . '/../../includes/db.php')) {
        require_once __DIR__ . '/../../includes/db.php';
        echo "✅ db.php cargado<br>";
    } else {
        echo "❌ db.php no existe<br>";
        return;
    }
    
    // Verificar checkoutController.php
    if (file_exists(__DIR__ . '/../../controllers/checkoutController.php')) {
        require_once __DIR__ . '/../../controllers/checkoutController.php';
        echo "✅ checkoutController.php cargado<br>";
    } else {
        echo "❌ checkoutController.php no existe<br>";
        return;
    }
    
    // Verificar loginController.php
    if (file_exists(__DIR__ . '/../../controllers/loginController.php')) {
        require_once __DIR__ . '/../../controllers/loginController.php';
        echo "✅ loginController.php cargado<br>";
    } else {
        echo "❌ loginController.php no existe<br>";
        return;
    }
    
    echo "<h2>2. 🔌 Verificando Conexión BD</h2>";
    $pdo = getConnection();
    if ($pdo) {
        echo "✅ Conexión BD exitosa<br>";
    } else {
        echo "❌ Error en conexión BD<br>";
        return;
    }
    
    echo "<h2>3. 🎮 Verificando Controladores</h2>";
    
    $loginController = new LoginController($pdo);
    if ($loginController) {
        echo "✅ LoginController creado<br>";
    } else {
        echo "❌ Error creando LoginController<br>";
        return;
    }
    
    $checkoutController = new CheckoutController($pdo);
    if ($checkoutController) {
        echo "✅ CheckoutController creado<br>";
    } else {
        echo "❌ Error creando CheckoutController<br>";
        return;
    }
    
    echo "<h2>4. 🔐 Verificando Autenticación</h2>";
    
    if ($loginController->estaAutenticado()) {
        $usuario = $loginController->obtenerUsuarioActual();
        echo "✅ Usuario autenticado: " . $usuario['nombre'] . " " . $usuario['apellido'] . "<br>";
        echo "📝 ID Usuario: " . $usuario['id'] . "<br>";
    } else {
        echo "❌ Usuario no autenticado<br>";
        echo "<p>⚠️ Necesitas estar logueado para probar la confirmación</p>";
        return;
    }
    
    echo "<h2>5. 🗄️ Verificando Orden en BD</h2>";
    
    // Verificar que la orden existe
    $ordenId = 5; // Orden que acabamos de crear
    
    $stmt = $pdo->prepare("SELECT * FROM ordenes WHERE id = ?");
    $stmt->execute([$ordenId]);
    $orden = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($orden) {
        echo "✅ Orden #$ordenId encontrada<br>";
        echo "📝 Número: " . $orden['numero_orden'] . "<br>";
        echo "📝 Total: $" . number_format($orden['total'], 2) . "<br>";
        echo "📝 Estado: " . $orden['estado'] . "<br>";
        echo "📝 Usuario ID: " . $orden['usuario_id'] . "<br>";
    } else {
        echo "❌ Orden #$ordenId no encontrada<br>";
        return;
    }
    
    echo "<h2>6. 🔍 Verificando Detalles de Orden</h2>";
    
    $stmt = $pdo->prepare("SELECT * FROM orden_detalles WHERE orden_id = ?");
    $stmt->execute([$ordenId]);
    $detalles = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if ($detalles) {
        echo "✅ Detalles de orden encontrados: " . count($detalles) . " productos<br>";
        foreach ($detalles as $detalle) {
            echo "📦 Producto ID: " . $detalle['producto_id'] . " - Cantidad: " . $detalle['cantidad'] . "<br>";
        }
    } else {
        echo "❌ No se encontraron detalles de orden<br>";
    }
    
    echo "<h2>7. 📱 Verificando Códigos QR</h2>";
    
    // Los códigos QR están en orden_detalles.codigo_qr
    $stmt = $pdo->prepare("SELECT codigo_qr, producto_id, cantidad FROM orden_detalles WHERE orden_id = ?");
    $stmt->execute([$ordenId]);
    $detallesConQR = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if ($detallesConQR) {
        echo "✅ Códigos QR encontrados: " . count($detallesConQR) . "<br>";
        foreach ($detallesConQR as $detalle) {
            echo "📱 QR: " . $detalle['codigo_qr'] . " - Producto ID: " . $detalle['producto_id'] . " - Cantidad: " . $detalle['cantidad'] . "<br>";
        }
    } else {
        echo "❌ No se encontraron códigos QR<br>";
    }
    
    // También verificar si hay registros en qr_verificaciones (para futuras verificaciones)
    echo "<h3>📋 Verificando Tabla qr_verificaciones (para futuras verificaciones)</h3>";
    
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM qr_verificaciones");
    $stmt->execute();
    $totalQrVerificaciones = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    if ($totalQrVerificaciones > 0) {
        echo "✅ Tabla qr_verificaciones tiene $totalQrVerificaciones registros<br>";
    } else {
        echo "ℹ️ Tabla qr_verificaciones está vacía (se llenará en futuros checkouts)<br>";
    }
    
    echo "<h2>8. 🧪 Probando CheckoutController->obtenerOrden()</h2>";
    
    $resultadoOrden = $checkoutController->obtenerOrden($ordenId);
    
    if ($resultadoOrden['success']) {
        echo "✅ obtenerOrden() exitoso<br>";
        $ordenCompleta = $resultadoOrden['orden'];
        echo "📝 Orden obtenida con " . count($ordenCompleta['detalles']) . " detalles<br>";
    } else {
        echo "❌ Error en obtenerOrden(): " . $resultadoOrden['message'] . "<br>";
    }
    
    echo "<h2>9. 🎯 Verificando Permisos de Usuario</h2>";
    
    if ($orden['usuario_id'] == $usuario['id']) {
        echo "✅ Usuario tiene permisos para ver esta orden<br>";
    } else {
        echo "❌ Usuario NO tiene permisos para ver esta orden<br>";
        echo "📝 Orden pertenece al usuario ID: " . $orden['usuario_id'] . "<br>";
        echo "📝 Usuario actual ID: " . $usuario['id'] . "<br>";
    }
    
    echo "<h2>🎉 Diagnóstico Completado</h2>";
    echo "<p>✅ Todos los componentes principales están funcionando.</p>";
    echo "<p>🔍 Revisa los resultados arriba para identificar el problema específico.</p>";
    
    echo "<h3>📋 Próximos Pasos:</h3>";
    echo "<p>Si todos los tests anteriores son exitosos, el problema puede estar en:</p>";
    echo "<ul>";
    echo "<li>Archivos de template (head.php, header.php, menu.php, footer.php, foot.php)</li>";
    echo "<li>Funciones de template (getProductImageUrl, etc.)</li>";
    echo "<li>Errores de sintaxis en el HTML/PHP</li>";
    echo "<li>Problemas de permisos de archivos</li>";
    echo "</ul>";
    
} catch (Exception $e) {
    echo "<h2>❌ Error en el Test</h2>";
    echo "<p><strong>Error:</strong> " . $e->getMessage() . "</p>";
    echo "<p><strong>Archivo:</strong> " . $e->getFile() . "</p>";
    echo "<p><strong>Línea:</strong> " . $e->getLine() . "</p>";
    echo "<p><strong>Stack trace:</strong></p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
} catch (Error $e) {
    echo "<h2>❌ Error Fatal en el Test</h2>";
    echo "<p><strong>Error:</strong> " . $e->getMessage() . "</p>";
    echo "<p><strong>Archivo:</strong> " . $e->getFile() . "</p>";
    echo "<p><strong>Línea:</strong> " . $e->getLine() . "</p>";
    echo "<p><strong>Stack trace:</strong></p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
h1 { color: #007bff; }
h2 { color: #28a745; margin-top: 30px; }
h3 { color: #ffc107; }
.✅ { color: #28a745; }
.❌ { color: #dc3545; }
.📝 { color: #17a2b8; }
.📦 { color: #6f42c1; }
.📱 { color: #fd7e14; }
.🎯 { color: #e83e8c; }
.🎉 { color: #28a745; }
.⚠️ { color: #ffc107; }
pre { background: #f8f9fa; padding: 15px; border-radius: 5px; overflow-x: auto; }
ul { background: #f8f9fa; padding: 20px; border-radius: 5px; }
</style>
