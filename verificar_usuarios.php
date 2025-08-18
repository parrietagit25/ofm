<?php
/**
 * Archivo para verificar y corregir la estructura de la tabla usuarios
 * Este archivo se puede eliminar en producción
 */

echo "<h1>Verificación de Tabla Usuarios - OFM</h1>";

try {
    // Incluir archivos necesarios
    require_once __DIR__ . '/includes/db.php';
    
    echo "<p><strong>✅ Conexión a la base de datos:</strong> Exitosa</p>";
    
    // Verificar estructura de la tabla usuarios
    echo "<hr><h3>Verificando estructura de la tabla usuarios</h3>";
    
    $stmt = $pdo->query("DESCRIBE usuarios");
    $campos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<p><strong>Campos encontrados:</strong></p>";
    echo "<ul>";
    foreach ($campos as $campo) {
        echo "<li><strong>" . $campo['Field'] . "</strong> - " . $campo['Type'] . " (" . $campo['Null'] . ")</li>";
    }
    echo "</ul>";
    
    // Verificar si existe el campo 'clave'
    $tieneClave = false;
    foreach ($campos as $campo) {
        if ($campo['Field'] === 'clave') {
            $tieneClave = true;
            break;
        }
    }
    
    if (!$tieneClave) {
        echo "<p><strong>❌ PROBLEMA:</strong> No existe el campo 'clave' en la tabla usuarios</p>";
        echo "<p>Agregando campo 'clave'...</p>";
        
        $sql = "ALTER TABLE usuarios ADD COLUMN clave VARCHAR(255) NOT NULL AFTER email";
        $pdo->exec($sql);
        echo "<p><strong>✅ Campo 'clave' agregado correctamente</strong></p>";
    } else {
        echo "<p><strong>✅ Campo 'clave':</strong> Existe correctamente</p>";
    }
    
    // Verificar usuarios existentes
    echo "<hr><h3>Verificando usuarios existentes</h3>";
    
    $stmt = $pdo->query("SELECT id, nombre, apellido, email, rol, activo FROM usuarios");
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<p><strong>Total de usuarios:</strong> " . count($usuarios) . "</p>";
    
    if (!empty($usuarios)) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>ID</th><th>Nombre</th><th>Apellido</th><th>Email</th><th>Rol</th><th>Activo</th></tr>";
        foreach ($usuarios as $usuario) {
            echo "<tr>";
            echo "<td>" . $usuario['id'] . "</td>";
            echo "<td>" . htmlspecialchars($usuario['nombre']) . "</td>";
            echo "<td>" . htmlspecialchars($usuario['apellido']) . "</td>";
            echo "<td>" . htmlspecialchars($usuario['email']) . "</td>";
            echo "<td>" . htmlspecialchars($usuario['rol']) . "</td>";
            echo "<td>" . ($usuario['activo'] ? 'Sí' : 'No') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    // Verificar si existe usuario admin
    echo "<hr><h3>Verificando usuario admin</h3>";
    
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE rol = 'admin' LIMIT 1");
    $stmt->execute();
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($admin) {
        echo "<p><strong>✅ Usuario admin encontrado:</strong> " . htmlspecialchars($admin['nombre']) . " " . htmlspecialchars($admin['apellido']) . "</p>";
        echo "<p><strong>Email:</strong> " . htmlspecialchars($admin['email']) . "</p>";
        echo "<p><strong>Activo:</strong> " . ($admin['activo'] ? 'Sí' : 'No') . "</p>";
        
        // Verificar si tiene contraseña
        if (empty($admin['clave'])) {
            echo "<p><strong>❌ PROBLEMA:</strong> El usuario admin no tiene contraseña</p>";
            echo "<p>Estableciendo contraseña por defecto...</p>";
            
            $claveHash = password_hash('admin123', PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE usuarios SET clave = ? WHERE id = ?");
            $stmt->execute([$claveHash, $admin['id']]);
            
            echo "<p><strong>✅ Contraseña establecida:</strong> admin123</p>";
        } else {
            echo "<p><strong>✅ Contraseña:</strong> Configurada</p>";
        }
    } else {
        echo "<p><strong>❌ PROBLEMA:</strong> No existe usuario admin</p>";
        echo "<p>Creando usuario admin por defecto...</p>";
        
        $claveHash = password_hash('admin123', PASSWORD_DEFAULT);
        $sql = "INSERT INTO usuarios (nombre, apellido, email, clave, telefono, rol, activo) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['Administrador', 'Sistema', 'admin@ofm.com', $claveHash, '0000000000', 'admin', 1]);
        
        echo "<p><strong>✅ Usuario admin creado:</strong></p>";
        echo "<ul>";
        echo "<li><strong>Email:</strong> admin@ofm.com</li>";
        echo "<li><strong>Contraseña:</strong> admin123</li>";
        echo "<li><strong>Rol:</strong> admin</li>";
        echo "</ul>";
    }
    
    // Verificar estructura final
    echo "<hr><h3>Estructura final de la tabla</h3>";
    
    $stmt = $pdo->query("DESCRIBE usuarios");
    $camposFinales = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<p><strong>Campos finales:</strong></p>";
    echo "<ul>";
    foreach ($camposFinales as $campo) {
        echo "<li><strong>" . $campo['Field'] . "</strong> - " . $campo['Type'] . " (" . $campo['Null'] . ")</li>";
    }
    echo "</ul>";
    
    echo "<hr><p><strong>🎉 Verificación completada exitosamente!</strong></p>";
    
    // Mostrar credenciales de acceso
    echo "<hr><h3>🔑 Credenciales de Acceso</h3>";
    echo "<p><strong>Usuario Admin:</strong></p>";
    echo "<ul>";
    echo "<li><strong>Email:</strong> admin@ofm.com</li>";
    echo "<li><strong>Contraseña:</strong> admin123</li>";
    echo "<li><strong>URL:</strong> <a href='public/evara/page-login-register.php'>Página de Login</a></li>";
    echo "</ul>";
    
} catch (Exception $e) {
    echo "<p><strong>❌ Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>Archivo:</strong> " . htmlspecialchars($e->getFile()) . "</p>";
    echo "<p><strong>Línea:</strong> " . htmlspecialchars($e->getLine()) . "</p>";
}

echo "<hr><p><small>Este archivo es solo para verificación. Eliminar en producción.</small></p>";
?>
