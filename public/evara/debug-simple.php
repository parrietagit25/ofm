<?php
// Debug simple para identificar el error 500
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Debug Simple OFM</h1>";

echo "<h2>Paso 1: Verificando PHP</h2>";
echo "✅ PHP está funcionando<br>";
echo "Versión PHP: " . phpversion() . "<br>";

echo "<h2>Paso 2: Incluyendo config.php</h2>";
try {
    require_once __DIR__ . '/../../includes/config.php';
    echo "✅ config.php incluido correctamente<br>";
} catch (Exception $e) {
    echo "❌ Error en config.php: " . $e->getMessage() . "<br>";
    exit;
}

echo "<h2>Paso 3: Incluyendo db.php</h2>";
try {
    require_once __DIR__ . '/../../includes/db.php';
    echo "✅ db.php incluido correctamente<br>";
} catch (Exception $e) {
    echo "❌ Error en db.php: " . $e->getMessage() . "<br>";
    exit;
}

echo "<h2>Paso 4: Verificando conexión DB</h2>";
if (isset($pdo)) {
    echo "✅ PDO disponible<br>";
    try {
        $stmt = $pdo->query("SELECT 1");
        echo "✅ Conexión DB funcionando<br>";
    } catch (Exception $e) {
        echo "❌ Error en conexión DB: " . $e->getMessage() . "<br>";
    }
} else {
    echo "❌ PDO NO disponible<br>";
}

echo "<h2>Paso 5: Incluyendo ProductoController</h2>";
try {
    require_once __DIR__ . '/../../controllers/productoController.php';
    echo "✅ ProductoController incluido correctamente<br>";
} catch (Exception $e) {
    echo "❌ Error en ProductoController: " . $e->getMessage() . "<br>";
    exit;
}

echo "<h2>Paso 6: Instanciando ProductoController</h2>";
try {
    $productoController = new ProductoController($pdo);
    echo "✅ ProductoController instanciado correctamente<br>";
} catch (Exception $e) {
    echo "❌ Error al instanciar ProductoController: " . $e->getMessage() . "<br>";
    exit;
}

echo "<h2>Paso 7: Probando método obtenerPorId</h2>";
try {
    $producto = $productoController->obtenerPorId(1);
    if ($producto) {
        echo "✅ Producto encontrado: " . htmlspecialchars($producto['nombre']) . "<br>";
    } else {
        echo "⚠️ No se encontró producto con ID 1<br>";
    }
} catch (Exception $e) {
    echo "❌ Error en obtenerPorId: " . $e->getMessage() . "<br>";
}

echo "<h2>Paso 8: Probando método obtenerTodasImagenes</h2>";
try {
    $imagenes = $productoController->obtenerTodasImagenes(1);
    echo "✅ Imágenes obtenidas: " . count($imagenes) . " imágenes<br>";
} catch (Exception $e) {
    echo "❌ Error en obtenerTodasImagenes: " . $e->getMessage() . "<br>";
}

echo "<h2>Paso 9: Verificando función getProductImageUrl</h2>";
if (function_exists('getProductImageUrl')) {
    echo "✅ getProductImageUrl existe<br>";
    try {
        $url = getProductImageUrl('test.jpg');
        echo "URL de ejemplo: " . htmlspecialchars($url) . "<br>";
    } catch (Exception $e) {
        echo "❌ Error al llamar getProductImageUrl: " . $e->getMessage() . "<br>";
    }
} else {
    echo "❌ getProductImageUrl NO existe<br>";
}

echo "<hr>";
echo "<p><a href='index.php'>← Volver al inicio</a></p>";
?>
