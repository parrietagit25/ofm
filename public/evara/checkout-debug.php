<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🐛 Debug Checkout - Diagnóstico Específico</h1>";

// 1. Verificar includes
echo "<h2>1. 📁 Verificando Includes</h2>";
try {
    require_once __DIR__ . '/../../includes/config.php';
    echo "✅ config.php cargado<br>";
    
    require_once __DIR__ . '/../../includes/db.php';
    echo "✅ db.php cargado<br>";
    
    require_once __DIR__ . '/../../controllers/carritoController.php';
    echo "✅ carritoController.php cargado<br>";
    
    require_once __DIR__ . '/../../controllers/loginController.php';
    echo "✅ loginController.php cargado<br>";
    
    require_once __DIR__ . '/../../controllers/checkoutController.php';
    echo "✅ checkoutController.php cargado<br>";
} catch (Exception $e) {
    echo "❌ Error cargando includes: " . $e->getMessage() . "<br>";
    exit;
}

// 2. Verificar conexión BD
echo "<h2>2. 🔌 Verificando Conexión BD</h2>";
try {
    $pdo = getConnection();
    echo "✅ Conexión BD exitosa<br>";
} catch (Exception $e) {
    echo "❌ Error conexión BD: " . $e->getMessage() . "<br>";
    exit;
}

// 3. Verificar controladores
echo "<h2>3. 🎮 Verificando Controladores</h2>";
try {
    $carritoController = new CarritoController($pdo);
    echo "✅ CarritoController creado<br>";
    
    $loginController = new LoginController($pdo);
    echo "✅ LoginController creado<br>";
    
    $checkoutController = new CheckoutController($pdo);
    echo "✅ CheckoutController creado<br>";
} catch (Exception $e) {
    echo "❌ Error creando controladores: " . $e->getMessage() . "<br>";
    exit;
}

// 4. Verificar autenticación
echo "<h2>4. 🔐 Verificando Autenticación</h2>";
try {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    if ($loginController->estaAutenticado()) {
        $usuario = $loginController->obtenerUsuarioActual();
        echo "✅ Usuario autenticado: " . htmlspecialchars($usuario['nombre']) . "<br>";
        echo "📝 ID Usuario: " . $usuario['id'] . "<br>";
    } else {
        echo "❌ Usuario NO autenticado<br>";
        echo "🔗 <a href='page-login-register.php'>Ir a login</a><br>";
        exit;
    }
} catch (Exception $e) {
    echo "❌ Error verificando autenticación: " . $e->getMessage() . "<br>";
    exit;
}

// 5. Verificar carrito
echo "<h2>5. 🛒 Verificando Carrito</h2>";
try {
    if ($carritoController->carritoVacio()) {
        echo "❌ El carrito está vacío<br>";
        echo "🔗 <a href='index.php'>Ir a productos</a><br>";
        exit;
    }
    
    $carrito = $carritoController->obtenerCarritoDetallado();
    $total = $carritoController->obtenerTotal();
    $cantidadProductos = $carritoController->obtenerCantidadProductos();
    
    echo "✅ Carrito con productos: " . $cantidadProductos . " productos<br>";
    echo "💰 Total: $" . number_format($total, 2) . "<br>";
    
    foreach ($carrito as $item) {
        echo "📦 " . htmlspecialchars($item['nombre']) . " x" . $item['cantidad'] . " - $" . number_format($item['precio'], 2) . "<br>";
    }
} catch (Exception $e) {
    echo "❌ Error verificando carrito: " . $e->getMessage() . "<br>";
    exit;
}

// 6. Simular datos de checkout IDÉNTICOS a la página real
echo "<h2>6. 🧪 Simulando Datos de Checkout (IDÉNTICOS a checkout.php)</h2>";
try {
    $datosCheckout = [
        'nombre' => $usuario['nombre'],
        'email' => $usuario['email'],
        'telefono' => $usuario['telefono'] ?? '123456789',
        'metodo_pago' => 'efectivo',
        'notas' => 'Test desde página de debug',
        'total' => $total,
        'subtotal' => $total,
        'envio' => 0.00,
        'impuestos' => 0.00
    ];
    
    echo "✅ Datos de checkout preparados:<br>";
    foreach ($datosCheckout as $key => $value) {
        echo "📝 $key: " . htmlspecialchars($value) . "<br>";
    }
} catch (Exception $e) {
    echo "❌ Error preparando datos: " . $e->getMessage() . "<br>";
    exit;
}

// 7. Probar checkout REAL con el controlador
echo "<h2>7. 🚀 Probando Checkout REAL con CheckoutController</h2>";
try {
    echo "🔄 Llamando a procesarCheckout()...<br>";
    
    $resultado = $checkoutController->procesarCheckout($datosCheckout);
    
    if ($resultado['success']) {
        echo "✅ <strong>CHECKOUT EXITOSO!</strong><br>";
        echo "📝 Mensaje: " . $resultado['message'] . "<br>";
        echo "🆔 Orden ID: " . $resultado['orden_id'] . "<br>";
        echo "🔢 Número de orden: " . $resultado['numero_orden'] . "<br>";
        echo "💰 Total: $" . number_format($resultado['total'], 2) . "<br>";
        echo "📱 QR generados: " . $resultado['qr_generados'] . "<br>";
    } else {
        echo "❌ <strong>CHECKOUT FALLÓ!</strong><br>";
        echo "📝 Mensaje de error: " . $resultado['message'] . "<br>";
    }
} catch (Exception $e) {
    echo "❌ <strong>EXCEPCIÓN en checkout:</strong><br>";
    echo "📝 Error: " . $e->getMessage() . "<br>";
    echo "📍 Archivo: " . $e->getFile() . "<br>";
    echo "📍 Línea: " . $e->getLine() . "<br>";
    echo "📚 Stack trace:<br>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

// 8. Verificar si se crearon registros en BD
echo "<h2>8. 🗄️ Verificando Registros Creados en BD</h2>";
try {
    // Verificar órdenes
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM ordenes");
    $totalOrdenes = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    echo "📊 Total órdenes en BD: " . $totalOrdenes . "<br>";
    
    // Verificar detalles de orden
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM orden_detalles");
    $totalDetalles = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    echo "📊 Total detalles de orden en BD: " . $totalDetalles . "<br>";
    
    // Verificar transacciones
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM transacciones");
    $totalTransacciones = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    echo "📊 Total transacciones en BD: " . $totalTransacciones . "<br>";
    
    // Mostrar última orden si existe
    if ($totalOrdenes > 0) {
        $stmt = $pdo->query("SELECT * FROM ordenes ORDER BY id DESC LIMIT 1");
        $ultimaOrden = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "📋 Última orden:<br>";
        echo "   - ID: " . $ultimaOrden['id'] . "<br>";
        echo "   - Número: " . $ultimaOrden['numero_orden'] . "<br>";
        echo "   - Total: $" . number_format($ultimaOrden['total'], 2) . "<br>";
        echo "   - Estado: " . $ultimaOrden['estado'] . "<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Error verificando registros: " . $e->getMessage() . "<br>";
}

echo "<hr>";
echo "<h2>🎯 Análisis del Problema</h2>";
echo "<p>Si el checkout falló, el problema puede estar en:</p>";
echo "<ul>";
echo "<li><strong>Transacciones de BD:</strong> Rollback automático</li>";
echo "<li><strong>Foreign keys:</strong> Restricciones de integridad</li>";
echo "<li><strong>Permisos:</strong> Usuario de BD sin permisos de escritura</li>";
echo "<li><strong>Estructura de tablas:</strong> Columnas faltantes o tipos incorrectos</li>";
echo "</ul>";

echo "<p><strong>¿Quieres que revisemos los logs de error o probemos algo específico?</strong></p>";
echo "<button onclick='location.reload()' style='padding: 10px 20px; background: #28a745; color: white; border: none; border-radius: 5px; cursor: pointer; margin-right: 10px;'>🔄 Refrescar</button>";
echo "<a href='checkout.php' style='padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px;'>🔗 Ir a Checkout Real</a>";

echo "<style>";
echo "body { font-family: Arial, sans-serif; margin: 20px; background: #f8f9fa; }";
echo "h1, h2 { color: #333; }";
echo "h2 { border-bottom: 2px solid #007bff; padding-bottom: 5px; margin-top: 30px; }";
echo "button:hover { background: #0056b3 !important; }";
echo "pre { background: #f8f9fa; padding: 10px; border: 1px solid #dee2e6; border-radius: 5px; overflow-x: auto; }";
echo "</style>";
?>
