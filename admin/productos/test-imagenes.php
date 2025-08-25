<?php
/**
 * Script de prueba para el sistema de imágenes de productos
 * Acceder desde: /ofm/admin/productos/test-imagenes.php
 */

require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/db.php';

echo "<!DOCTYPE html>";
echo "<html>";
echo "<head>";
echo "<title>Test Sistema de Imágenes - OFM</title>";
echo "<meta charset='UTF-8'>";
echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
echo "<style>";
echo "body { font-family: Arial, sans-serif; margin: 20px; }";
echo ".test-section { border: 1px solid #ddd; padding: 20px; margin: 20px 0; border-radius: 8px; }";
echo ".success { border-color: #28a745; background: #d4edda; }";
echo ".error { border-color: #dc3545; background: #f8d7da; }";
echo ".info { border-color: #17a2b8; background: #d1ecf1; }";
echo ".image-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 15px; margin: 15px 0; }";
echo ".image-item { border: 1px solid #ccc; padding: 10px; border-radius: 5px; text-align: center; }";
echo ".image-item img { max-width: 100%; height: auto; border-radius: 3px; }";
echo "</style>";
echo "</head>";
echo "<body>";

echo "<h1>🧪 Test Sistema de Imágenes - OFM</h1>";

// Test 1: Verificar configuración
echo "<div class='test-section info'>";
echo "<h3>1. Verificación de Configuración</h3>";

if (defined('PRODUCTOS_IMAGENES_PATH')) {
    echo "✅ PRODUCTOS_IMAGENES_PATH: " . PRODUCTOS_IMAGENES_PATH . "<br>";
} else {
    echo "❌ PRODUCTOS_IMAGENES_PATH no está definido<br>";
}

if (defined('PRODUCTOS_IMAGENES_URL')) {
    echo "✅ PRODUCTOS_IMAGENES_URL: " . PRODUCTOS_IMAGENES_URL . "<br>";
} else {
    echo "❌ PRODUCTOS_IMAGENES_URL no está definido<br>";
}

echo "</div>";

// Test 2: Verificar directorio
echo "<div class='test-section info'>";
echo "<h3>2. Verificación de Directorio</h3>";

$directorio = PRODUCTOS_IMAGENES_PATH;
if (is_dir($directorio)) {
    echo "✅ Directorio existe: $directorio<br>";
    if (is_writable($directorio)) {
        echo "✅ Directorio es escribible<br>";
    } else {
        echo "❌ Directorio NO es escribible<br>";
    }
} else {
    echo "❌ Directorio NO existe: $directorio<br>";
    echo "Creando directorio...<br>";
    if (mkdir($directorio, 0755, true)) {
        echo "✅ Directorio creado exitosamente<br>";
    } else {
        echo "❌ Error al crear directorio<br>";
    }
}

echo "</div>";

// Test 3: Verificar base de datos
echo "<div class='test-section info'>";
echo "<h3>3. Verificación de Base de Datos</h3>";

try {
    // Verificar tabla productos
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM productos WHERE status = 'activo'");
    $totalProductos = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    echo "✅ Productos activos: $totalProductos<br>";
    
    // Verificar tabla producto_imagenes
    $stmt = $pdo->query("SHOW TABLES LIKE 'producto_imagenes'");
    if ($stmt->rowCount() > 0) {
        echo "✅ Tabla producto_imagenes existe<br>";
        
        // Verificar columnas
        $stmt = $pdo->query("DESCRIBE producto_imagenes");
        $columnas = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        if (in_array('es_principal', $columnas)) {
            echo "✅ Columna 'es_principal' existe<br>";
        } else {
            echo "❌ Columna 'es_principal' NO existe<br>";
        }
        
        if (in_array('orden', $columnas)) {
            echo "✅ Columna 'orden' existe<br>";
        } else {
            echo "❌ Columna 'orden' NO existe<br>";
        }
        
        // Contar imágenes
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM producto_imagenes");
        $totalImagenes = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        echo "✅ Total de imágenes: $totalImagenes<br>";
        
    } else {
        echo "❌ Tabla producto_imagenes NO existe<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Error en base de datos: " . $e->getMessage() . "<br>";
}

echo "</div>";

// Test 4: Verificar archivos de imagen
echo "<div class='test-section info'>";
echo "<h3>4. Verificación de Archivos de Imagen</h3>";

$archivos = glob($directorio . "/*.{jpg,jpeg,png,gif,webp}", GLOB_BRACE);
$totalArchivos = count($archivos);

echo "✅ Archivos de imagen encontrados: $totalArchivos<br>";

if ($totalArchivos > 0) {
    echo "<div class='image-grid'>";
    foreach (array_slice($archivos, 0, 6) as $archivo) {
        $nombreArchivo = basename($archivo);
        $urlImagen = getProductImageUrl($nombreArchivo);
        
        echo "<div class='image-item'>";
        echo "<img src='$urlImagen' alt='$nombreArchivo'><br>";
        echo "<small>$nombreArchivo</small>";
        echo "</div>";
    }
    echo "</div>";
    
    if ($totalArchivos > 6) {
        echo "<p>... y " . ($totalArchivos - 6) . " archivos más</p>";
    }
} else {
    echo "⚠️ No hay archivos de imagen en el directorio<br>";
    echo "<p>Para crear imágenes de prueba, ejecuta:</p>";
    echo "<code>php scripts/generar_imagenes_placeholder.php</code>";
}

echo "</div>";

// Test 5: Verificar función getProductImageUrl
echo "<div class='test-section info'>";
echo "<h3>5. Verificación de Función getProductImageUrl</h3>";

if (function_exists('getProductImageUrl')) {
    echo "✅ Función getProductImageUrl existe<br>";
    
    $urlTest = getProductImageUrl('test.jpg');
    echo "✅ URL de ejemplo: $urlTest<br>";
    
    $urlVacia = getProductImageUrl('');
    echo "✅ URL con archivo vacío: $urlVacia<br>";
} else {
    echo "❌ Función getProductImageUrl NO existe<br>";
}

echo "</div>";

// Test 6: Verificar productos con imágenes
echo "<div class='test-section info'>";
echo "<h3>6. Verificación de Productos con Imágenes</h3>";

try {
    $sql = "SELECT p.id, p.nombre, COUNT(pi.id) as total_imagenes, 
                   GROUP_CONCAT(pi.nombre_archivo ORDER BY pi.orden) as archivos
            FROM productos p 
            LEFT JOIN producto_imagenes pi ON p.id = pi.producto_id 
            WHERE p.status = 'activo'
            GROUP BY p.id, p.nombre 
            ORDER BY p.id";
    
    $stmt = $pdo->query($sql);
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($productos) > 0) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>ID</th><th>Producto</th><th>Imágenes</th><th>Archivos</th></tr>";
        
        foreach ($productos as $producto) {
            echo "<tr>";
            echo "<td>" . $producto['id'] . "</td>";
            echo "<td>" . htmlspecialchars($producto['nombre']) . "</td>";
            echo "<td>" . $producto['total_imagenes'] . "</td>";
            echo "<td>" . htmlspecialchars($producto['archivos'] ?? 'Sin imágenes') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "⚠️ No hay productos activos en la base de datos<br>";
        echo "<p>Para crear productos de prueba, ejecuta:</p>";
        echo "<code>sql/crear_productos_ejemplo.sql</code>";
    }
    
} catch (Exception $e) {
    echo "❌ Error al consultar productos: " . $e->getMessage() . "<br>";
}

echo "</div>";

// Resumen final
echo "<div class='test-section success'>";
echo "<h3>🎯 Resumen del Test</h3>";

$errores = 0;
if (!defined('PRODUCTOS_IMAGENES_PATH')) $errores++;
if (!defined('PRODUCTOS_IMAGENES_URL')) $errores++;
if (!is_dir($directorio)) $errores++;
if (!is_writable($directorio)) $errores++;
if (!function_exists('getProductImageUrl')) $errores++;

if ($errores === 0) {
    echo "🎉 ¡Sistema de imágenes funcionando correctamente!<br>";
    echo "✅ Puedes crear productos con imágenes desde el panel de administración<br>";
    echo "✅ Las imágenes se mostrarán correctamente en la tienda<br>";
} else {
    echo "⚠️ Se encontraron $errores problema(s) que deben resolverse<br>";
    echo "🔧 Revisa la configuración y ejecuta los scripts de actualización<br>";
}

echo "</div>";

echo "<hr>";
echo "<p><a href='../dashboard.php'>← Volver al Dashboard</a> | ";
echo "<a href='index.php'>Ver Productos</a> | ";
echo "<a href='crear.php'>Crear Producto</a></p>";

echo "</body>";
echo "</html>";
?>
