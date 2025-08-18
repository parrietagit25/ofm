<?php
/**
 * Script SIMPLE para corregir la tabla usuarios
 * Sin permisos especiales de MySQL
 */

echo "<h1>Corrección Simple de Tabla Usuarios - OFM</h1>";

try {
    // Incluir archivos necesarios
    require_once __DIR__ . '/includes/db.php';
    
    echo "<p><strong>✅ Conexión a la base de datos:</strong> Exitosa</p>";
    
    // 1. Verificar estructura actual
    echo "<hr><h3>1. Estructura actual de la tabla usuarios</h3>";
    
    $stmt = $pdo->query("DESCRIBE usuarios");
    $campos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<p><strong>Campos encontrados:</strong></p>";
    echo "<ul>";
    foreach ($campos as $campo) {
        echo "<li><strong>" . $campo['Field'] . "</strong> - " . $campo['Type'] . " (" . $campo['Null'] . ")</li>";
    }
    echo "</ul>";
    
    // 2. Verificar si existe campo 'clave'
    $tieneClave = false;
    foreach ($campos as $campo) {
        if ($campo['Field'] === 'clave') {
            $tieneClave = true;
            break;
        }
    }
    
    if (!$tieneClave) {
        echo "<hr><h3>2. Agregando campo 'clave'</h3>";
        
        try {
            $sql = "ALTER TABLE usuarios ADD COLUMN clave VARCHAR(255) NOT NULL AFTER email";
            $pdo->exec($sql);
            echo "<p><strong>✅ Campo 'clave' agregado correctamente</strong></p>";
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
                echo "<p><strong>ℹ️ Campo 'clave' ya existe</strong></p>";
            } else {
                throw $e;
            }
        }
    } else {
        echo "<p><strong>✅ Campo 'clave':</strong> Ya existe</p>";
    }
    
    // 3. Verificar usuarios admin
    echo "<hr><h3>3. Verificando usuarios admin</h3>";
    
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM usuarios WHERE rol = 'admin'");
    $stmt->execute();
    $totalAdmin = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    echo "<p><strong>Total usuarios admin:</strong> " . $totalAdmin . "</p>";
    
    if ($totalAdmin == 0) {
        echo "<p><strong>Creando usuario admin...</strong></p>";
        
        $claveHash = password_hash('admin123', PASSWORD_DEFAULT);
        $sql = "INSERT INTO usuarios (nombre, apellido, email, clave, telefono, rol, activo) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['Administrador', 'Sistema', 'admin@ofm.com', $claveHash, '0000000000', 'admin', 1]);
        
        echo "<p><strong>✅ Usuario admin creado exitosamente</strong></p>";
    } else {
        echo "<p><strong>Actualizando contraseña de admin existente...</strong></p>";
        
        $claveHash = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE usuarios SET clave = ? WHERE rol = 'admin'");
        $stmt->execute([$claveHash]);
        
        echo "<p><strong>✅ Contraseña de admin actualizada</strong></p>";
    }
    
    // 4. Verificar resultado final
    echo "<hr><h3>4. Resultado final</h3>";
    
    $stmt = $pdo->query("SELECT id, nombre, apellido, email, rol, activo FROM usuarios ORDER BY rol, nombre");
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
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
    
    // 5. Mostrar credenciales
    echo "<hr><h3>5. 🔑 Credenciales de Acceso</h3>";
    echo "<div style='background-color: #f8f9fa; padding: 15px; border-radius: 5px; border: 1px solid #dee2e6;'>";
    echo "<p><strong>Usuario Administrador:</strong></p>";
    echo "<ul>";
    echo "<li><strong>Email:</strong> admin@ofm.com</li>";
    echo "<li><strong>Contraseña:</strong> admin123</li>";
    echo "<li><strong>Rol:</strong> admin</li>";
    echo "</ul>";
    echo "</div>";
    
    // 6. Enlaces de prueba
    echo "<hr><h3>6. 🧪 Enlaces de Prueba</h3>";
    echo "<p><a href='public/evara/page-login-register.php' style='background-color: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🔐 Probar Login</a></p>";
    echo "<p><a href='admin/dashboard.php' style='background-color: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>📊 Dashboard Admin</a></p>";
    
    echo "<hr><p><strong>🎉 Corrección completada exitosamente!</strong></p>";
    
} catch (Exception $e) {
    echo "<p><strong>❌ Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>Archivo:</strong> " . htmlspecialchars($e->getFile()) . "</p>";
    echo "<p><strong>Línea:</strong> " . htmlspecialchars($e->getLine()) . "</p>";
    
    // Mostrar información de debug
    echo "<hr><h3>Información de Debug:</h3>";
    echo "<p><strong>PHP Version:</strong> " . phpversion() . "</p>";
    echo "<p><strong>Extensiones PDO:</strong> " . (extension_loaded('pdo') ? 'Cargada' : 'No cargada') . "</p>";
    echo "<p><strong>Extensiones PDO MySQL:</strong> " . (extension_loaded('pdo_mysql') ? 'Cargada' : 'No cargada') . "</p>";
}

echo "<hr><p><small>Este archivo es solo para corrección. Eliminar en producción.</small></p>";
?>
