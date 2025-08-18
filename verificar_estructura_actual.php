<?php
/**
 * Verificar la estructura ACTUAL de la tabla usuarios
 * Para entender qué campos realmente existen
 */

echo "<h1>Verificación de Estructura Real - Tabla Usuarios</h1>";

try {
    // Incluir archivos necesarios
    require_once __DIR__ . '/includes/db.php';
    
    echo "<p><strong>✅ Conexión a la base de datos:</strong> Exitosa</p>";
    
    // 1. Verificar estructura REAL de la tabla usuarios
    echo "<hr><h3>1. Estructura REAL de la tabla usuarios</h3>";
    
    $stmt = $pdo->query("DESCRIBE usuarios");
    $campos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<p><strong>Campos encontrados:</strong></p>";
    echo "<ul>";
    foreach ($campos as $campo) {
        echo "<li><strong>" . $campo['Field'] . "</strong> - " . $campo['Type'] . " (" . $campo['Null'] . ")</li>";
    }
    echo "</ul>";
    
    // 2. Verificar si hay algún campo de contraseña
    echo "<hr><h3>2. Búsqueda de campos de contraseña</h3>";
    
    $camposContraseña = [];
    foreach ($campos as $campo) {
        $nombreCampo = strtolower($campo['Field']);
        if (strpos($nombreCampo, 'pass') !== false || 
            strpos($nombreCampo, 'clave') !== false || 
            strpos($nombreCampo, 'password') !== false ||
            strpos($nombreCampo, 'pwd') !== false) {
            $camposContraseña[] = $campo;
        }
    }
    
    if (!empty($camposContraseña)) {
        echo "<p><strong>✅ Campos de contraseña encontrados:</strong></p>";
        echo "<ul>";
        foreach ($camposContraseña as $campo) {
            echo "<li><strong>" . $campo['Field'] . "</strong> - " . $campo['Type'] . "</li>";
        }
        echo "</ul>";
    } else {
        echo "<p><strong>❌ No se encontraron campos de contraseña</strong></p>";
    }
    
    // 3. Verificar usuarios existentes
    echo "<hr><h3>3. Usuarios existentes</h3>";
    
    $stmt = $pdo->query("SELECT * FROM usuarios LIMIT 5");
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (!empty($usuarios)) {
        echo "<p><strong>Total de usuarios:</strong> " . count($usuarios) . "</p>";
        echo "<p><strong>Primer usuario (ejemplo):</strong></p>";
        
        $primerUsuario = $usuarios[0];
        echo "<div style='background-color: #f8f9fa; padding: 15px; border-radius: 5px; border: 1px solid #dee2e6;'>";
        foreach ($primerUsuario as $campo => $valor) {
            echo "<p><strong>" . htmlspecialchars($campo) . ":</strong> " . htmlspecialchars($valor ?? 'NULL') . "</p>";
        }
        echo "</div>";
    } else {
        echo "<p><strong>No hay usuarios en la tabla</strong></p>";
    }
    
    // 4. Verificar si la tabla tiene datos
    echo "<hr><h3>4. Información de la tabla</h3>";
    
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM usuarios");
    $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    echo "<p><strong>Total de registros:</strong> " . $total . "</p>";
    
    // 5. Mostrar estructura SQL completa
    echo "<hr><h3>5. Estructura SQL completa</h3>";
    
    $stmt = $pdo->query("SHOW CREATE TABLE usuarios");
    $createTable = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (isset($createTable['Create Table'])) {
        echo "<pre style='background-color: #f8f9fa; padding: 15px; border-radius: 5px; border: 1px solid #dee2e6; overflow-x: auto;'>";
        echo htmlspecialchars($createTable['Create Table']);
        echo "</pre>";
    }
    
    echo "<hr><p><strong>🎉 Verificación de estructura completada!</strong></p>";
    
} catch (Exception $e) {
    echo "<p><strong>❌ Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>Archivo:</strong> " . htmlspecialchars($e->getFile()) . "</p>";
    echo "<p><strong>Línea:</strong> " . htmlspecialchars($e->getLine()) . "</p>";
}

echo "<hr><p><small>Este archivo es solo para verificación. Eliminar en producción.</small></p>";
?>
