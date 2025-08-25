<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🧪 Test Checkout - Diagnóstico Paso a Paso</h1>";

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

// 6. Verificar tablas de BD
echo "<h2>6. 🗄️ Verificando Tablas de BD</h2>";
try {
    $tablas = ['ordenes', 'orden_detalles', 'transacciones', 'qr_verificaciones', 'comisiones_socios', 'configuracion_comisiones', 'notificaciones_socios', 'estadisticas_ventas_socios'];
    
    foreach ($tablas as $tabla) {
        $stmt = $pdo->query("SHOW TABLES LIKE '$tabla'");
        if ($stmt->rowCount() > 0) {
            echo "✅ Tabla '$tabla' existe<br>";
        } else {
            echo "❌ Tabla '$tabla' NO existe<br>";
        }
    }
} catch (Exception $e) {
    echo "❌ Error verificando tablas: " . $e->getMessage() . "<br>";
}

// 7. Verificar configuración de comisiones
echo "<h2>7. 💰 Verificando Configuración de Comisiones</h2>";
try {
    $stmt = $pdo->query("SELECT * FROM configuracion_comisiones LIMIT 5");
    $comisiones = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($comisiones) > 0) {
        echo "✅ Configuración de comisiones encontrada: " . count($comisiones) . " registros<br>";
        foreach ($comisiones as $comision) {
            echo "📊 " . $comision['tipo_producto'] . ": " . $comision['porcentaje_comision'] . "%<br>";
        }
    } else {
        echo "❌ No hay configuración de comisiones<br>";
    }
} catch (Exception $e) {
    echo "❌ Error verificando comisiones: " . $e->getMessage() . "<br>";
}

// 8. Simular datos de checkout
echo "<h2>8. 🧪 Simulando Datos de Checkout</h2>";
try {
    $datosCheckout = [
        'nombre' => $usuario['nombre'],
        'email' => $usuario['email'],
        'telefono' => $usuario['telefono'] ?? '123456789',
        'metodo_pago' => 'efectivo',
        'notas' => 'Test desde página de diagnóstico',
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

// 9. Probar método validarDatosCheckout
echo "<h2>9. ✅ Probando Validación de Datos</h2>";
try {
    $reflection = new ReflectionClass($checkoutController);
    $method = $reflection->getMethod('validarDatosCheckout');
    $method->setAccessible(true);
    
    $resultado = $method->invoke($checkoutController, $datosCheckout);
    
    if ($resultado['success']) {
        echo "✅ Validación de datos exitosa<br>";
    } else {
        echo "❌ Validación de datos falló: " . $resultado['message'] . "<br>";
    }
} catch (Exception $e) {
    echo "❌ Error en validación: " . $e->getMessage() . "<br>";
}

// 10. Probar método generarNumeroOrden
echo "<h2>10. 🔢 Probando Generación de Número de Orden</h2>";
try {
    $reflection = new ReflectionClass($checkoutController);
    $method = $reflection->getMethod('generarNumeroOrden');
    $method->setAccessible(true);
    
    $numeroOrden = $method->invoke($checkoutController);
    echo "✅ Número de orden generado: " . htmlspecialchars($numeroOrden) . "<br>";
} catch (Exception $e) {
    echo "❌ Error generando número de orden: " . $e->getMessage() . "<br>";
}

// 11. Probar método obtenerInfoComercioProducto
echo "<h2>11. 🏪 Probando Obtención de Info Comercio</h2>";
try {
    $reflection = new ReflectionClass($checkoutController);
    $method = $reflection->getMethod('obtenerInfoComercioProducto');
    $method->setAccessible(true);
    
    $primerProducto = $carrito[0];
    $infoComercio = $method->invoke($checkoutController, $primerProducto['producto_id']);
    
    echo "✅ Info comercio obtenida:<br>";
    echo "📝 Comercio ID: " . $infoComercio['comercio_id'] . "<br>";
    echo "📝 Socio ID: " . $infoComercio['socio_id'] . "<br>";
    echo "📝 Producto ID: " . $infoComercio['producto_id'] . "<br>";
} catch (Exception $e) {
    echo "❌ Error obteniendo info comercio: " . $e->getMessage() . "<br>";
}

// 12. Probar método generarCodigoQR
echo "<h2>12. 📱 Probando Generación de QR</h2>";
try {
    $reflection = new ReflectionClass($checkoutController);
    $method = $reflection->getMethod('generarCodigoQR');
    $method->setAccessible(true);
    
    $codigoQr = $method->invoke($checkoutController, 1, 1, 1, 1);
    echo "✅ Código QR generado: " . htmlspecialchars($codigoQr) . "<br>";
} catch (Exception $e) {
    echo "❌ Error generando QR: " . $e->getMessage() . "<br>";
}

echo "<hr>";
echo "<h2>🎯 Próximos Pasos</h2>";
echo "<p>Si todos los tests anteriores son exitosos, el problema puede estar en:</p>";
echo "<ul>";
echo "<li>Transacciones de BD</li>";
echo "<li>Permisos de escritura</li>";
echo "<li>Restricciones de foreign keys</li>";
echo "<li>Problemas de sesión</li>";
echo "</ul>";

echo "<p><strong>¿Quieres que probemos procesar una orden real?</strong></p>";
echo "<button onclick='probarCheckout()' style='padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer; margin-right: 10px;'>🧪 Probar Checkout Real</button>";
echo "<button onclick='location.reload()' style='padding: 10px 20px; background: #28a745; color: white; border: none; border-radius: 5px; cursor: pointer;'>🔄 Refrescar Tests</button>";

echo "<div id='resultado' style='margin-top: 20px; padding: 15px; border-radius: 5px; display: none;'></div>";
?>

<script>
function probarCheckout() {
    const resultadoDiv = document.getElementById('resultado');
    resultadoDiv.style.display = 'block';
    resultadoDiv.innerHTML = '<div style="background: #fff3cd; border: 1px solid #ffeaa7; padding: 10px; border-radius: 5px;">🔄 Procesando checkout...</div>';
    
    const datosCheckout = {
        nombre: '<?= addslashes($usuario['nombre']) ?>',
        email: '<?= addslashes($usuario['email']) ?>',
        telefono: '<?= addslashes($usuario['telefono'] ?? '123456789') ?>',
        metodo_pago: 'efectivo',
        notas: 'Test desde página de diagnóstico',
        total: <?= $total ?>,
        subtotal: <?= $total ?>,
        envio: 0.00,
        impuestos: 0.00
    };
    
    fetch('ajax/procesar-checkout.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(datosCheckout)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            resultadoDiv.innerHTML = '<div style="background: #d4edda; border: 1px solid #c3e6cb; padding: 10px; border-radius: 5px; color: #155724;">✅ Checkout exitoso: ' + data.message + '<br>Orden ID: ' + data.orden_id + '<br>Número: ' + data.numero_orden + '</div>';
        } else {
            resultadoDiv.innerHTML = '<div style="background: #f8d7da; border: 1px solid #f5c6cb; padding: 10px; border-radius: 5px; color: #721c24;">❌ Error en checkout: ' + data.message + '</div>';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        resultadoDiv.innerHTML = '<div style="background: #f8d7da; border: 1px solid #f5c6cb; padding: 10px; border-radius: 5px; color: #721c24;">❌ Error de conexión: ' + error.message + '</div>';
    });
}
</script>

<style>
body {
    font-family: Arial, sans-serif;
    margin: 20px;
    background: #f8f9fa;
}
h1, h2 {
    color: #333;
}
h2 {
    border-bottom: 2px solid #007bff;
    padding-bottom: 5px;
    margin-top: 30px;
}
button:hover {
    background: #0056b3 !important;
}
</style>
