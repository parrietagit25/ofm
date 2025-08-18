<?php
/**
 * Script para crear usuario admin simple
 * Sin campo de contraseña
 */

echo "<h1>Crear Usuario Admin Simple - OFM</h1>";

try {
    // Incluir archivos necesarios
    require_once __DIR__ . '/includes/db.php';
    
    echo "<p><strong>✅ Conexión a la base de datos:</strong> Exitosa</p>";
    
    // Verificar si ya existe un usuario admin
    echo "<hr><h3>1. Verificando usuarios admin existentes</h3>";
    
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM usuarios WHERE rol = 'admin'");
    $stmt->execute();
    $totalAdmin = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    echo "<p><strong>Total usuarios admin:</strong> " . $totalAdmin . "</p>";
    
    if ($totalAdmin == 0) {
        echo "<p><strong>Creando usuario admin...</strong></p>";
        
        // Hash de la contraseña admin123
        $claveHash = password_hash('admin123', PASSWORD_DEFAULT);
        
        $sql = "INSERT INTO usuarios (nombre, apellido, email, clave, telefono, rol, activo, creado_en) 
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['Administrador', 'Sistema', 'admin@ofm.com', $claveHash, '0000000000', 'admin', 1]);
        
        echo "<p><strong>✅ Usuario admin creado exitosamente</strong></p>";
    } else {
        echo "<p><strong>ℹ️ Ya existe un usuario admin</strong></p>";
    }
    
    // Verificar resultado final
    echo "<hr><h3>2. Usuarios en el sistema</h3>";
    
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
    
    // Mostrar credenciales
    echo "<hr><h3>3. 🔑 Credenciales de Acceso</h3>";
    echo "<div style='background-color: #f8f9fa; padding: 15px; border-radius: 5px; border: 1px solid #dee2e6;'>";
    echo "<p><strong>Usuario Administrador:</strong></p>";
    echo "<ul>";
    echo "<li><strong>Email:</strong> admin@ofm.com</li>";
    echo "<li><strong>Contraseña:</strong> admin123</li>";
    echo "<li><strong>Rol:</strong> admin</li>";
    echo "<li><strong>Acceso:</strong> Email + Contraseña</li>";
    echo "</ul>";
    echo "</div>";
    
    // Enlaces de prueba
    echo "<hr><h3>4. 🧪 Enlaces de Prueba</h3>";
    echo "<p><a href='public/evara/page-login-register.php' style='background-color: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🔐 Probar Login</a></p>";
    echo "<p><a href='admin/dashboard.php' style='background-color: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>📊 Dashboard Admin</a></p>";
    
    echo "<hr><p><strong>🎉 Usuario admin creado exitosamente!</strong></p>";
    
} catch (Exception $e) {
    echo "<p><strong>❌ Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>Archivo:</strong> " . htmlspecialchars($e->getFile()) . "</p>";
    echo "<p><strong>Línea:</strong> " . htmlspecialchars($e->getLine()) . "</p>";
}

echo "<hr><p><small>Este archivo es solo para configuración inicial. Eliminar en producción.</small></p>";
?>
